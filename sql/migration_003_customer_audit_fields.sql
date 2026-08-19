-- ============================================================
-- Migration 003 — audit trail + soft delete for customers
-- customers.status (active/pending/inactive) is a *business* status and stays
-- as-is; deletion is tracked separately via is_deleted/deleted_at/deleted_by
-- so the two concepts don't collide.
-- ============================================================
USE ktg_billing;

ALTER TABLE customers
  ADD COLUMN created_by INT UNSIGNED NULL AFTER notes,
  ADD COLUMN updated_by INT UNSIGNED NULL AFTER created_by,
  ADD COLUMN is_deleted TINYINT(1) NOT NULL DEFAULT 0 AFTER updated_by,
  ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL AFTER is_deleted,
  ADD COLUMN deleted_by INT UNSIGNED NULL AFTER deleted_at,
  ADD KEY idx_customers_is_deleted (is_deleted),
  ADD CONSTRAINT fk_customers_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  ADD CONSTRAINT fk_customers_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
  ADD CONSTRAINT fk_customers_deleted_by FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE SET NULL;
