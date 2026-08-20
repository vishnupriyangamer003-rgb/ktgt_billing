-- ============================================================
-- Migration 004 — audit trail + soft delete for renewals
-- (paid_status_id is a business/payment concept, untouched — deletion is
-- tracked separately via is_deleted/deleted_at/deleted_by, same pattern as
-- migration 003 for customers)
-- ============================================================
USE ktg_billing;

ALTER TABLE renewals
  ADD COLUMN created_by INT UNSIGNED NULL AFTER paid_status_id,
  ADD COLUMN updated_by INT UNSIGNED NULL AFTER created_by,
  ADD COLUMN is_deleted TINYINT(1) NOT NULL DEFAULT 0 AFTER updated_by,
  ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL AFTER is_deleted,
  ADD COLUMN deleted_by INT UNSIGNED NULL AFTER deleted_at,
  ADD KEY idx_renewals_is_deleted (is_deleted),
  ADD CONSTRAINT fk_renewals_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  ADD CONSTRAINT fk_renewals_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
  ADD CONSTRAINT fk_renewals_deleted_by FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE SET NULL;
