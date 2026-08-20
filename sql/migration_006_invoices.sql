-- ============================================================
-- Migration 006 — Invoices module (list/create/edit, standalone
-- from the Renewals "Generate Invoice" feature which keeps its own
-- renewal_invoices table). Same audit + soft-delete pattern as
-- customers/renewals. Items stored as JSON, same as renewal_invoices,
-- since line items are only ever displayed, never queried standalone.
-- ============================================================
USE ktg_billing;

CREATE TABLE IF NOT EXISTS invoices (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  invoice_number VARCHAR(50) NOT NULL,
  customer_id INT UNSIGNED NOT NULL,
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
  status ENUM('draft','pending','partial','paid','overdue','cancelled') NOT NULL DEFAULT 'pending',
  terms TEXT,
  created_by INT UNSIGNED DEFAULT NULL,
  updated_by INT UNSIGNED DEFAULT NULL,
  is_deleted TINYINT(1) NOT NULL DEFAULT 0,
  deleted_at TIMESTAMP NULL DEFAULT NULL,
  deleted_by INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_invoices_number (invoice_number),
  KEY idx_invoices_customer (customer_id),
  KEY idx_invoices_status (status),
  KEY idx_invoices_gst_type (gst_type),
  KEY idx_invoices_date (invoice_date),
  KEY idx_invoices_is_deleted (is_deleted),
  CONSTRAINT fk_invoices_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT,
  CONSTRAINT fk_invoices_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_invoices_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_invoices_deleted_by FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;
