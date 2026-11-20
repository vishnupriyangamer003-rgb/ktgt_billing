-- ============================================================
-- LIVE DATABASE UPDATE — 2026-11-02
-- Run once on the live database (phpMyAdmin: select the live DB first, then
-- Import / SQL tab). Safe to re-run: every step checks before it changes anything.
-- Do NOT add "USE ktg_billing" — the live database name may differ.
-- BACK UP the live database before running.
--
-- Combines:  (0) renewals.status + renewals.is_renewed   (were never in a migration file)
--            (1) migration_008  Domain Control master
--            (2) migration_009  multiple companies per customer
--            (3) migration_010  company stored on renewals / invoices / renewal_invoices
-- ============================================================

-- ------------------------------------------------------------
-- (0) Renewals: Active/Inactive status + Renewed flag
-- ------------------------------------------------------------
SET @s = IF((SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'renewals' AND column_name = 'status') = 0,
  "ALTER TABLE renewals ADD COLUMN status ENUM('active','inactive') NOT NULL DEFAULT 'active' AFTER paid_status_id", 'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s = IF((SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'renewals' AND column_name = 'is_renewed') = 0,
  "ALTER TABLE renewals ADD COLUMN is_renewed TINYINT(1) NOT NULL DEFAULT 0 AFTER status", 'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- ------------------------------------------------------------
-- (1) Domain Control master
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS master_domain_controls (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_by INT UNSIGNED DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  updated_by INT UNSIGNED DEFAULT NULL,
  deleted_at TIMESTAMP NULL DEFAULT NULL,
  deleted_by INT UNSIGNED DEFAULT NULL,
  status ENUM('active','deleted') NOT NULL DEFAULT 'active',
  KEY idx_domain_control_status (status),
  CONSTRAINT fk_domain_control_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_domain_control_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_domain_control_deleted_by FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Seed from the values already typed into renewals so nothing in use is missing from the dropdown.
INSERT INTO master_domain_controls (name)
SELECT DISTINCT TRIM(r.domain_control) FROM renewals r
WHERE r.domain_control IS NOT NULL AND TRIM(r.domain_control) <> ''
  AND NOT EXISTS (SELECT 1 FROM master_domain_controls m WHERE m.status = 'active' AND LOWER(m.name) = LOWER(TRIM(r.domain_control)));

-- ------------------------------------------------------------
-- (2) Multiple companies per customer
--     customers.company_name stays as the FIRST (primary) company.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS customer_companies (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  customer_id INT UNSIGNED NOT NULL,
  name VARCHAR(150) NOT NULL,
  sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  KEY idx_cc_customer (customer_id),
  KEY idx_cc_name (name),
  CONSTRAINT fk_cc_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Backfill: each existing customer's single company becomes company #1.
INSERT INTO customer_companies (customer_id, name, sort_order)
SELECT c.id, TRIM(c.company_name), 0 FROM customers c
WHERE c.company_name IS NOT NULL AND TRIM(c.company_name) <> ''
  AND NOT EXISTS (SELECT 1 FROM customer_companies cc WHERE cc.customer_id = c.id);

-- ------------------------------------------------------------
-- (3) Which company a renewal / invoice is for
-- ------------------------------------------------------------
SET @s = IF((SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'renewals' AND column_name = 'company_name') = 0,
  "ALTER TABLE renewals ADD COLUMN company_name VARCHAR(150) DEFAULT NULL AFTER customer_id", 'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s = IF((SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'invoices' AND column_name = 'company_name') = 0,
  "ALTER TABLE invoices ADD COLUMN company_name VARCHAR(150) DEFAULT NULL AFTER customer_id", 'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s = IF((SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'renewal_invoices' AND column_name = 'company_name') = 0,
  "ALTER TABLE renewal_invoices ADD COLUMN company_name VARCHAR(150) DEFAULT NULL AFTER customer_name", 'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- Backfill existing rows with the customer's primary company (only where still empty).
UPDATE renewals r JOIN customers c ON c.id = r.customer_id
  SET r.company_name = NULLIF(TRIM(c.company_name), '') WHERE r.company_name IS NULL;
UPDATE invoices i JOIN customers c ON c.id = i.customer_id
  SET i.company_name = NULLIF(TRIM(c.company_name), '') WHERE i.company_name IS NULL;
UPDATE renewal_invoices ri JOIN renewals r ON r.id = ri.renewal_id
  SET ri.company_name = r.company_name WHERE ri.company_name IS NULL;
