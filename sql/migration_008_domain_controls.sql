-- ============================================================
-- Migration 008 — Domain Control master
-- Turns the free-text "Domain Control" box on the Renewals form into a
-- dropdown backed by its own master (managed on domain-controls.php, and
-- addable inline from the renewal form). renewals.domain_control keeps
-- storing the chosen name as text, so existing rows/list/export are unchanged.
-- ============================================================
USE ktg_billing;

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
