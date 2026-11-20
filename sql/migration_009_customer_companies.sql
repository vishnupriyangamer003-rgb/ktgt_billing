-- ============================================================
-- Migration 009 — multiple companies per customer
-- One customer can own several companies. customer_companies holds the full
-- ordered list; customers.company_name stays as the FIRST (primary) company so
-- invoices, payments, renewals and every other module that reads it keep working.
-- ============================================================
USE ktg_billing;

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
