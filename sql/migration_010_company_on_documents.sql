-- ============================================================
-- Migration 010 — remember WHICH of a customer's companies a document is for
-- Customer dropdowns now list one entry per company ("Vishnu - Vichu", "Vishnu - Service"…),
-- so renewals and invoices store the chosen company alongside customer_id.
-- Existing rows are backfilled with the customer's primary company.
-- ============================================================
USE ktg_billing;

ALTER TABLE renewals ADD COLUMN company_name VARCHAR(150) DEFAULT NULL AFTER customer_id;
ALTER TABLE invoices ADD COLUMN company_name VARCHAR(150) DEFAULT NULL AFTER customer_id;
ALTER TABLE renewal_invoices ADD COLUMN company_name VARCHAR(150) DEFAULT NULL AFTER customer_name;

UPDATE renewals r JOIN customers c ON c.id = r.customer_id
  SET r.company_name = NULLIF(TRIM(c.company_name), '');
UPDATE invoices i JOIN customers c ON c.id = i.customer_id
  SET i.company_name = NULLIF(TRIM(c.company_name), '');
UPDATE renewal_invoices ri JOIN renewals r ON r.id = ri.renewal_id
  SET ri.company_name = r.company_name;
