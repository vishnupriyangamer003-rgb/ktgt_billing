-- ============================================================
-- Migration 005 — persisted "Generate Renewal Invoice" records
-- One invoice per renewal (UNIQUE renewal_id): the first Save inserts,
-- every Save after that updates the same row, matching the UI's
-- Generate (not-yet-saved) vs View/Edit (already-saved) distinction.
-- ============================================================
USE ktg_billing;

CREATE TABLE IF NOT EXISTS renewal_invoices (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  renewal_id INT UNSIGNED NOT NULL,
  invoice_number VARCHAR(50) NOT NULL,
  gst_type ENUM('gst','nongst') NOT NULL DEFAULT 'gst',
  doc_type VARCHAR(30) NOT NULL DEFAULT 'TAX INVOICE',
  invoice_date DATE NOT NULL,
  due_date DATE DEFAULT NULL,
  customer_name VARCHAR(150) DEFAULT NULL,
  customer_gst VARCHAR(30) DEFAULT NULL,
  customer_address VARCHAR(255) DEFAULT NULL,
  customer_mobile VARCHAR(20) DEFAULT NULL,
  items_json TEXT NOT NULL,
  subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
  discount DECIMAL(12,2) NOT NULL DEFAULT 0,
  tax_type VARCHAR(20) NOT NULL DEFAULT 'none',
  tax_percent DECIMAL(5,2) NOT NULL DEFAULT 0,
  tax_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
  round_off DECIMAL(6,2) NOT NULL DEFAULT 0,
  grand_total DECIMAL(12,2) NOT NULL DEFAULT 0,
  advance_paid DECIMAL(12,2) NOT NULL DEFAULT 0,
  balance_due DECIMAL(12,2) NOT NULL DEFAULT 0,
  terms TEXT,
  created_by INT UNSIGNED DEFAULT NULL,
  updated_by INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_renewal_invoices_renewal (renewal_id),
  CONSTRAINT fk_renewal_invoices_renewal FOREIGN KEY (renewal_id) REFERENCES renewals(id) ON DELETE CASCADE,
  CONSTRAINT fk_renewal_invoices_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_renewal_invoices_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;
