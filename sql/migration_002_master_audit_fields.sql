-- ============================================================
-- Migration 002 — audit trail + soft delete for all 7 Masters tables
-- Adds: created_by, updated_at, updated_by, deleted_at, deleted_by, status
-- Delete no longer removes the row — it flips status to 'deleted'.
-- ============================================================
USE ktg_billing;

-- The plain UNIQUE(name) constraint can't coexist with soft delete (a re-added
-- name would collide with its own deleted predecessor), so duplicate checking
-- moves entirely into application code, scoped to status='active'.

ALTER TABLE master_project_types
  DROP INDEX uq_project_type_name,
  ADD COLUMN created_by INT UNSIGNED NULL AFTER created_at,
  ADD COLUMN updated_at TIMESTAMP NULL DEFAULT NULL AFTER created_by,
  ADD COLUMN updated_by INT UNSIGNED NULL AFTER updated_at,
  ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL AFTER updated_by,
  ADD COLUMN deleted_by INT UNSIGNED NULL AFTER deleted_at,
  ADD COLUMN status ENUM('active','deleted') NOT NULL DEFAULT 'active' AFTER deleted_by,
  ADD CONSTRAINT fk_project_type_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  ADD CONSTRAINT fk_project_type_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
  ADD CONSTRAINT fk_project_type_deleted_by FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE SET NULL,
  ADD KEY idx_project_type_status (status);

ALTER TABLE master_services
  DROP INDEX uq_service_name,
  ADD COLUMN created_by INT UNSIGNED NULL AFTER created_at,
  ADD COLUMN updated_at TIMESTAMP NULL DEFAULT NULL AFTER created_by,
  ADD COLUMN updated_by INT UNSIGNED NULL AFTER updated_at,
  ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL AFTER updated_by,
  ADD COLUMN deleted_by INT UNSIGNED NULL AFTER deleted_at,
  ADD COLUMN status ENUM('active','deleted') NOT NULL DEFAULT 'active' AFTER deleted_by,
  ADD CONSTRAINT fk_service_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  ADD CONSTRAINT fk_service_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
  ADD CONSTRAINT fk_service_deleted_by FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE SET NULL,
  ADD KEY idx_service_status (status);

ALTER TABLE master_taxes
  DROP INDEX uq_tax_name,
  ADD COLUMN created_by INT UNSIGNED NULL AFTER created_at,
  ADD COLUMN updated_at TIMESTAMP NULL DEFAULT NULL AFTER created_by,
  ADD COLUMN updated_by INT UNSIGNED NULL AFTER updated_at,
  ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL AFTER updated_by,
  ADD COLUMN deleted_by INT UNSIGNED NULL AFTER deleted_at,
  ADD COLUMN status ENUM('active','deleted') NOT NULL DEFAULT 'active' AFTER deleted_by,
  ADD CONSTRAINT fk_tax_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  ADD CONSTRAINT fk_tax_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
  ADD CONSTRAINT fk_tax_deleted_by FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE SET NULL,
  ADD KEY idx_tax_status (status);

ALTER TABLE master_followup_types
  DROP INDEX uq_followup_type_name,
  ADD COLUMN created_by INT UNSIGNED NULL AFTER created_at,
  ADD COLUMN updated_at TIMESTAMP NULL DEFAULT NULL AFTER created_by,
  ADD COLUMN updated_by INT UNSIGNED NULL AFTER updated_at,
  ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL AFTER updated_by,
  ADD COLUMN deleted_by INT UNSIGNED NULL AFTER deleted_at,
  ADD COLUMN status ENUM('active','deleted') NOT NULL DEFAULT 'active' AFTER deleted_by,
  ADD CONSTRAINT fk_followup_type_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  ADD CONSTRAINT fk_followup_type_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
  ADD CONSTRAINT fk_followup_type_deleted_by FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE SET NULL,
  ADD KEY idx_followup_type_status (status);

ALTER TABLE master_servers
  DROP INDEX uq_server_name,
  ADD COLUMN created_by INT UNSIGNED NULL AFTER created_at,
  ADD COLUMN updated_at TIMESTAMP NULL DEFAULT NULL AFTER created_by,
  ADD COLUMN updated_by INT UNSIGNED NULL AFTER updated_at,
  ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL AFTER updated_by,
  ADD COLUMN deleted_by INT UNSIGNED NULL AFTER deleted_at,
  ADD COLUMN status ENUM('active','deleted') NOT NULL DEFAULT 'active' AFTER deleted_by,
  ADD CONSTRAINT fk_server_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  ADD CONSTRAINT fk_server_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
  ADD CONSTRAINT fk_server_deleted_by FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE SET NULL,
  ADD KEY idx_server_status (status);

ALTER TABLE master_renewal_types
  DROP INDEX uq_renewal_type_name,
  ADD COLUMN created_by INT UNSIGNED NULL AFTER created_at,
  ADD COLUMN updated_at TIMESTAMP NULL DEFAULT NULL AFTER created_by,
  ADD COLUMN updated_by INT UNSIGNED NULL AFTER updated_at,
  ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL AFTER updated_by,
  ADD COLUMN deleted_by INT UNSIGNED NULL AFTER deleted_at,
  ADD COLUMN status ENUM('active','deleted') NOT NULL DEFAULT 'active' AFTER deleted_by,
  ADD CONSTRAINT fk_renewal_type_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  ADD CONSTRAINT fk_renewal_type_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
  ADD CONSTRAINT fk_renewal_type_deleted_by FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE SET NULL,
  ADD KEY idx_renewal_type_status (status);

ALTER TABLE master_paid_status_types
  DROP INDEX uq_paid_status_name,
  ADD COLUMN created_by INT UNSIGNED NULL AFTER created_at,
  ADD COLUMN updated_at TIMESTAMP NULL DEFAULT NULL AFTER created_by,
  ADD COLUMN updated_by INT UNSIGNED NULL AFTER updated_at,
  ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL AFTER updated_by,
  ADD COLUMN deleted_by INT UNSIGNED NULL AFTER deleted_at,
  ADD COLUMN status ENUM('active','deleted') NOT NULL DEFAULT 'active' AFTER deleted_by,
  ADD CONSTRAINT fk_paid_status_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  ADD CONSTRAINT fk_paid_status_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
  ADD CONSTRAINT fk_paid_status_deleted_by FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE SET NULL,
  ADD KEY idx_paid_status_status (status);
