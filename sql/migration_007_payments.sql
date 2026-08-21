-- ============================================================
-- Migration 007 — Payments module. Records a customer payment and
-- how it's allocated across their outstanding invoices (bill-wise
-- tally, oldest invoice first — same as the existing UI's logic).
-- invoices.advance_paid/balance_due/status are updated in place as
-- allocations are applied, so those columns stay authoritative for
-- the Invoices list/create pages without needing to re-derive from
-- payment history every time.
-- ============================================================
USE ktg_billing;

CREATE TABLE IF NOT EXISTS payments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  payment_number VARCHAR(50) NOT NULL,
  customer_id INT UNSIGNED NOT NULL,
  customer_name VARCHAR(150) DEFAULT NULL,
  payment_date DATE NOT NULL,
  amount DECIMAL(12,2) NOT NULL DEFAULT 0,
  mode VARCHAR(50) DEFAULT NULL,
  reference_number VARCHAR(100) DEFAULT NULL,
  received_by VARCHAR(120) DEFAULT NULL,
  remarks VARCHAR(255) DEFAULT NULL,
  created_by INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_payments_number (payment_number),
  KEY idx_payments_customer (customer_id),
  KEY idx_payments_date (payment_date),
  CONSTRAINT fk_payments_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT,
  CONSTRAINT fk_payments_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payment_allocations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  payment_id INT UNSIGNED NOT NULL,
  invoice_id INT UNSIGNED NOT NULL,
  amount DECIMAL(12,2) NOT NULL DEFAULT 0,
  KEY idx_pa_payment (payment_id),
  KEY idx_pa_invoice (invoice_id),
  CONSTRAINT fk_pa_payment FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE,
  CONSTRAINT fk_pa_invoice FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
) ENGINE=InnoDB;
