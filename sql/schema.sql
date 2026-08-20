-- ============================================================
-- KTG Invoice Suite — Phase 1 schema
-- Modules: Login, Masters, Customers, Renewal, Settings, Profile
-- ============================================================

CREATE DATABASE IF NOT EXISTS ktg_billing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ktg_billing;

-- ------------------------------------------------------------
-- USERS — backs Login, Settings > Users, Settings > Roles & Permissions
-- ------------------------------------------------------------
CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  username VARCHAR(60) NOT NULL,
  email VARCHAR(150) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role VARCHAR(60) NOT NULL DEFAULT 'Administrator',
  avatar_color VARCHAR(20) NOT NULL DEFAULT '#2563eb',
  can_manage_customers TINYINT(1) NOT NULL DEFAULT 0,
  can_manage_quotations TINYINT(1) NOT NULL DEFAULT 0,
  can_manage_invoices TINYINT(1) NOT NULL DEFAULT 0,
  can_manage_payments TINYINT(1) NOT NULL DEFAULT 0,
  can_view_reports TINYINT(1) NOT NULL DEFAULT 0,
  can_manage_users TINYINT(1) NOT NULL DEFAULT 0,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_users_username (username),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- MASTERS — one table per master screen already in the UI.
-- Every master carries a full audit trail + soft delete: a "deleted" record just
-- flips status to 'deleted' (deleted_at/deleted_by stamped) rather than being
-- removed, so name uniqueness is enforced in application code (scoped to
-- status='active') instead of a DB-level UNIQUE — a plain UNIQUE(name) can't
-- coexist with soft delete, since a re-added name would collide with its own
-- deleted predecessor.
-- ------------------------------------------------------------
CREATE TABLE master_project_types (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_by INT UNSIGNED DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  updated_by INT UNSIGNED DEFAULT NULL,
  deleted_at TIMESTAMP NULL DEFAULT NULL,
  deleted_by INT UNSIGNED DEFAULT NULL,
  status ENUM('active','deleted') NOT NULL DEFAULT 'active',
  KEY idx_project_type_status (status),
  CONSTRAINT fk_project_type_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_project_type_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_project_type_deleted_by FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE master_services (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  rate DECIMAL(12,2) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_by INT UNSIGNED DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  updated_by INT UNSIGNED DEFAULT NULL,
  deleted_at TIMESTAMP NULL DEFAULT NULL,
  deleted_by INT UNSIGNED DEFAULT NULL,
  status ENUM('active','deleted') NOT NULL DEFAULT 'active',
  KEY idx_service_status (status),
  CONSTRAINT fk_service_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_service_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_service_deleted_by FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE master_taxes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  percent DECIMAL(5,2) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_by INT UNSIGNED DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  updated_by INT UNSIGNED DEFAULT NULL,
  deleted_at TIMESTAMP NULL DEFAULT NULL,
  deleted_by INT UNSIGNED DEFAULT NULL,
  status ENUM('active','deleted') NOT NULL DEFAULT 'active',
  KEY idx_tax_status (status),
  CONSTRAINT fk_tax_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_tax_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_tax_deleted_by FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE master_followup_types (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_by INT UNSIGNED DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  updated_by INT UNSIGNED DEFAULT NULL,
  deleted_at TIMESTAMP NULL DEFAULT NULL,
  deleted_by INT UNSIGNED DEFAULT NULL,
  status ENUM('active','deleted') NOT NULL DEFAULT 'active',
  KEY idx_followup_type_status (status),
  CONSTRAINT fk_followup_type_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_followup_type_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_followup_type_deleted_by FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE master_servers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_by INT UNSIGNED DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  updated_by INT UNSIGNED DEFAULT NULL,
  deleted_at TIMESTAMP NULL DEFAULT NULL,
  deleted_by INT UNSIGNED DEFAULT NULL,
  status ENUM('active','deleted') NOT NULL DEFAULT 'active',
  KEY idx_server_status (status),
  CONSTRAINT fk_server_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_server_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_server_deleted_by FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE master_renewal_types (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_by INT UNSIGNED DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  updated_by INT UNSIGNED DEFAULT NULL,
  deleted_at TIMESTAMP NULL DEFAULT NULL,
  deleted_by INT UNSIGNED DEFAULT NULL,
  status ENUM('active','deleted') NOT NULL DEFAULT 'active',
  KEY idx_renewal_type_status (status),
  CONSTRAINT fk_renewal_type_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_renewal_type_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_renewal_type_deleted_by FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE master_paid_status_types (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_by INT UNSIGNED DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  updated_by INT UNSIGNED DEFAULT NULL,
  deleted_at TIMESTAMP NULL DEFAULT NULL,
  deleted_by INT UNSIGNED DEFAULT NULL,
  status ENUM('active','deleted') NOT NULL DEFAULT 'active',
  KEY idx_paid_status_status (status),
  CONSTRAINT fk_paid_status_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_paid_status_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_paid_status_deleted_by FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- CUSTOMERS
-- ------------------------------------------------------------
CREATE TABLE customers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  company_name VARCHAR(150) DEFAULT NULL,
  contact_person VARCHAR(120) DEFAULT NULL,
  mobile VARCHAR(30) NOT NULL,
  whatsapp VARCHAR(30) DEFAULT NULL,
  email VARCHAR(150) DEFAULT NULL,
  gst_number VARCHAR(20) DEFAULT NULL,
  address TEXT,
  city VARCHAR(100) DEFAULT NULL,
  project_type_id INT UNSIGNED DEFAULT NULL,
  status ENUM('active','pending','inactive') NOT NULL DEFAULT 'pending',
  notes TEXT,
  created_by INT UNSIGNED DEFAULT NULL,
  updated_by INT UNSIGNED DEFAULT NULL,
  is_deleted TINYINT(1) NOT NULL DEFAULT 0,
  deleted_at TIMESTAMP NULL DEFAULT NULL,
  deleted_by INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_customers_status (status),
  KEY idx_customers_mobile (mobile),
  KEY idx_customers_is_deleted (is_deleted),
  CONSTRAINT fk_customers_project_type FOREIGN KEY (project_type_id) REFERENCES master_project_types(id) ON DELETE SET NULL,
  CONSTRAINT fk_customers_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_customers_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_customers_deleted_by FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- RENEWALS — linked to a real customer; "project" kept as free text since
-- the Projects module is out of Phase-1 scope
-- ------------------------------------------------------------
CREATE TABLE renewals (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  customer_id INT UNSIGNED NOT NULL,
  project_name VARCHAR(180) NOT NULL,
  project_type_id INT UNSIGNED DEFAULT NULL,
  server_id INT UNSIGNED DEFAULT NULL,
  domain_control VARCHAR(150) DEFAULT NULL,
  live_link TEXT,
  expiry_date DATE NOT NULL,
  renewal_type_id INT UNSIGNED DEFAULT NULL,
  paid_status_id INT UNSIGNED DEFAULT NULL,
  created_by INT UNSIGNED DEFAULT NULL,
  updated_by INT UNSIGNED DEFAULT NULL,
  is_deleted TINYINT(1) NOT NULL DEFAULT 0,
  deleted_at TIMESTAMP NULL DEFAULT NULL,
  deleted_by INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_renewals_expiry (expiry_date),
  KEY idx_renewals_is_deleted (is_deleted),
  CONSTRAINT fk_renewals_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT,
  CONSTRAINT fk_renewals_project_type FOREIGN KEY (project_type_id) REFERENCES master_project_types(id) ON DELETE SET NULL,
  CONSTRAINT fk_renewals_server FOREIGN KEY (server_id) REFERENCES master_servers(id) ON DELETE SET NULL,
  CONSTRAINT fk_renewals_renewal_type FOREIGN KEY (renewal_type_id) REFERENCES master_renewal_types(id) ON DELETE SET NULL,
  CONSTRAINT fk_renewals_paid_status FOREIGN KEY (paid_status_id) REFERENCES master_paid_status_types(id) ON DELETE SET NULL,
  CONSTRAINT fk_renewals_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_renewals_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_renewals_deleted_by FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- RENEWALS > Generate Invoice — persisted invoice per renewal.
-- One invoice per renewal (UNIQUE renewal_id): first Save inserts,
-- later Saves update the same row (Generate vs View/Edit in the UI).
-- ------------------------------------------------------------
CREATE TABLE renewal_invoices (
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

-- ------------------------------------------------------------
-- INVOICES — standalone invoices module (list/create/edit), separate
-- from the Renewals "Generate Invoice" feature and its own
-- renewal_invoices table. Same audit + soft-delete pattern as
-- customers/renewals. Items stored as JSON (only ever displayed,
-- never queried standalone).
-- ------------------------------------------------------------
CREATE TABLE invoices (
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

-- ------------------------------------------------------------
-- SETTINGS > Company Profile — separate GST / Non-GST letterhead
-- ------------------------------------------------------------
CREATE TABLE company_profiles (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  gst_type ENUM('gst','nongst') NOT NULL,
  name VARCHAR(150) DEFAULT NULL,
  website VARCHAR(150) DEFAULT NULL,
  phone VARCHAR(30) DEFAULT NULL,
  email VARCHAR(150) DEFAULT NULL,
  address TEXT,
  gstin VARCHAR(20) DEFAULT NULL,
  pan VARCHAR(20) DEFAULT NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_company_profiles_gst_type (gst_type)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- PROFILE.PHP — singleton company record (name/GST/bank/logo)
-- ------------------------------------------------------------
CREATE TABLE company_details (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) DEFAULT NULL,
  short_name VARCHAR(50) DEFAULT NULL,
  phone VARCHAR(30) DEFAULT NULL,
  email VARCHAR(150) DEFAULT NULL,
  website VARCHAR(150) DEFAULT NULL,
  established_year VARCHAR(10) DEFAULT NULL,
  address TEXT,
  gstin VARCHAR(20) DEFAULT NULL,
  pan VARCHAR(20) DEFAULT NULL,
  state VARCHAR(60) DEFAULT NULL,
  state_code VARCHAR(10) DEFAULT NULL,
  bank_name VARCHAR(120) DEFAULT NULL,
  bank_branch VARCHAR(120) DEFAULT NULL,
  account_holder VARCHAR(150) DEFAULT NULL,
  account_number VARCHAR(40) DEFAULT NULL,
  ifsc_code VARCHAR(20) DEFAULT NULL,
  account_type ENUM('Current','Savings') NOT NULL DEFAULT 'Current',
  upi_id VARCHAR(100) DEFAULT NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- SETTINGS > Prefixes (singleton)
-- ------------------------------------------------------------
CREATE TABLE settings_prefixes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  quotation_prefix VARCHAR(30) NOT NULL DEFAULT 'QUO-2026-',
  quotation_next_number INT UNSIGNED NOT NULL DEFAULT 1,
  invoice_prefix VARCHAR(30) NOT NULL DEFAULT 'INV-2026-',
  invoice_next_number INT UNSIGNED NOT NULL DEFAULT 1,
  payment_prefix VARCHAR(30) NOT NULL DEFAULT 'PAY-',
  payment_next_number INT UNSIGNED NOT NULL DEFAULT 1,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- SETTINGS > Payment Modes
-- ------------------------------------------------------------
CREATE TABLE payment_modes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(60) NOT NULL,
  icon VARCHAR(60) NOT NULL DEFAULT 'bi-cash',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_payment_mode_name (name)
) ENGINE=InnoDB;

-- ============================================================
-- SEED DATA — mirrors the existing dummy data so the UI isn't empty
-- ============================================================

-- Users (demo passwords — change in production)
-- Vicky / vicky.kumar / Ktg@2026
-- Divya N / divya.n / Divya@2026
-- Karthik R / karthik.r / Karthik@2026
INSERT INTO users (name, username, email, password_hash, role, avatar_color, can_manage_customers, can_manage_quotations, can_manage_invoices, can_manage_payments, can_view_reports, can_manage_users, status) VALUES
('Vicky', 'vicky.kumar', 'ktgkrr@gmail.com', '$2y$10$M2fGUhGGt4tP0aO8/.xA6uJe9PLW8d3Bcdkqo4SWrlEHINKwc.bgS', 'Administrator', '#2563eb', 1,1,1,1,1,1, 'active'),
('Divya N', 'divya.n', 'divya@knocktheglobe.com', '$2y$10$viCWUZZqD2ss2lnRF.2Wb.d1qY0GF585NQrgGeI7.9U7tSz9zG7Ey', 'Accountant', '#7c3aed', 0,0,1,1,1,0, 'active'),
('Karthik R', 'karthik.r', 'karthik@knocktheglobe.com', '$2y$10$XP0VEK0fcvBO1bMPSrAeG.PXp/aLtpc/mK8OjWfITVrKqQZDVm4bC', 'Sales Executive', '#0891b2', 1,1,0,0,0,0, 'inactive');

-- Masters
INSERT INTO master_project_types (name) VALUES
('Website'), ('Web Application'), ('Mobile Application'), ('E-Commerce Platform'), ('ERP Software'), ('Digital Marketing');

INSERT INTO master_services (name, rate) VALUES
('Website Designing', 35000), ('Web Development', 65000), ('Mobile App Development', 85000),
('Domain Registration', 1200), ('Web Hosting', 6000), ('AMC (Annual Maintenance)', 15000),
('Renewal Charges', 5000), ('Video Editing', 2500), ('Logo & Branding', 12000), ('SEO Services', 18000);

INSERT INTO master_taxes (name, percent) VALUES
('GST 18%', 18), ('GST 12%', 12), ('GST 5%', 5), ('No Tax', 0);

INSERT INTO master_followup_types (name) VALUES
('Call'), ('Meeting'), ('Email'), ('WhatsApp');

INSERT INTO master_servers (name) VALUES
('Hostinger'), ('GoDaddy'), ('AWS'), ('Bluehost'), ('Own VPS / Server');

INSERT INTO master_renewal_types (name) VALUES
('Monthly'), ('Quarterly'), ('Half-Yearly'), ('Yearly');

INSERT INTO master_paid_status_types (name) VALUES
('Paid'), ('Unpaid'), ('Hold'), ('Overdue');

-- Customers
INSERT INTO customers (name, company_name, contact_person, mobile, whatsapp, email, gst_number, address, city, project_type_id, status, created_at) VALUES
('Zen Foods Pvt Ltd', 'Zen Foods Pvt Ltd', 'Arun Kumar', '98765 43210', '98765 43210', 'arun@zenfoods.com', '33ZENFD1234F1Z2', '12 Anna Nagar, Karur, TN', 'Karur', (SELECT id FROM master_project_types WHERE name='Website'), 'active', '2026-01-14'),
('Bright Retail Solutions', 'Bright Retail Solutions', 'Priya S', '98432 11223', '98432 11223', 'priya@brightretail.in', '33BRSPL5678F1Z9', '45 Gandhi Rd, Trichy, TN', 'Trichy', (SELECT id FROM master_project_types WHERE name='Web Application'), 'active', '2025-11-02'),
('Nova Textiles', 'Nova Textiles Ltd', 'Karthik R', '90000 12345', '90000 12345', 'karthik@novatextiles.com', '33NOVAT9988F1Z3', '7 Mill Street, Coimbatore, TN', 'Coimbatore', (SELECT id FROM master_project_types WHERE name='Website'), 'pending', '2026-02-20'),
('Sunrise Hospitals', 'Sunrise Hospitals', 'Dr. Meena', '99887 66554', '99887 66554', 'meena@sunrisehosp.com', '33SUNH2233F1Z6', '22 Hospital Rd, Madurai, TN', 'Madurai', (SELECT id FROM master_project_types WHERE name='Web Application'), 'active', '2025-09-18'),
('Velocity Motors', 'Velocity Motors Pvt Ltd', 'Suresh Babu', '93445 12009', '93445 12009', 'suresh@velocitymotors.in', '33VELM4455F1Z1', '9 Bypass Road, Salem, TN', 'Salem', (SELECT id FROM master_project_types WHERE name='Website'), 'inactive', '2025-06-10'),
('Orbit Learning Academy', 'Orbit Learning Academy', 'Divya N', '90032 11987', '90032 11987', 'divya@orbitlearning.edu', '33ORBL7788F1Z4', '18 School St, Erode, TN', 'Erode', (SELECT id FROM master_project_types WHERE name='Web Application'), 'active', '2026-03-05'),
('Green Leaf Organics', 'Green Leaf Organics', 'Ramesh V', '91234 56780', '91234 56780', 'ramesh@greenleaf.com', '33GRNLF3344F1Z8', '3 Market Rd, Karur, TN', 'Karur', (SELECT id FROM master_project_types WHERE name='Website'), 'active', '2026-04-11'),
('Pinnacle Constructions', 'Pinnacle Constructions', 'Vijay Anand', '98123 45670', '98123 45670', 'vijay@pinnacleco.in', '33PINC6677F1Z0', '55 Ring Rd, Trichy, TN', 'Trichy', (SELECT id FROM master_project_types WHERE name='Web Application'), 'pending', '2026-05-22');

-- Renewals (customer_id resolved by name — relies on the customer insert order above)
INSERT INTO renewals (customer_id, project_name, project_type_id, server_id, domain_control, live_link, expiry_date, renewal_type_id, paid_status_id) VALUES
((SELECT id FROM customers WHERE name='Zen Foods Pvt Ltd'), 'Corporate Website Revamp', (SELECT id FROM master_project_types WHERE name='Website'), (SELECT id FROM master_servers WHERE name='Hostinger'), 'Client-side (GoDaddy)', 'https://zenfoods.com — admin: zenfoods.com/wp-admin', '2026-08-18', (SELECT id FROM master_renewal_types WHERE name='Yearly'), (SELECT id FROM master_paid_status_types WHERE name='Paid')),
((SELECT id FROM customers WHERE name='Bright Retail Solutions'), 'E-commerce Web App', (SELECT id FROM master_project_types WHERE name='Web Application'), (SELECT id FROM master_servers WHERE name='AWS'), 'With us (KTG)', 'https://brightretail.in — admin: brightretail.in/admin', '2026-07-25', (SELECT id FROM master_renewal_types WHERE name='Yearly'), (SELECT id FROM master_paid_status_types WHERE name='Unpaid')),
((SELECT id FROM customers WHERE name='Nova Textiles'), 'Inventory Management System', (SELECT id FROM master_project_types WHERE name='Web Application'), (SELECT id FROM master_servers WHERE name='Own VPS / Server'), 'With us (KTG)', 'http://inventory.novatextiles.com — admin login pending', '2026-09-10', (SELECT id FROM master_renewal_types WHERE name='Yearly'), (SELECT id FROM master_paid_status_types WHERE name='Hold')),
((SELECT id FROM customers WHERE name='Sunrise Hospitals'), 'Patient Portal', (SELECT id FROM master_project_types WHERE name='Web Application'), (SELECT id FROM master_servers WHERE name='Bluehost'), 'Client-side', 'https://sunrisehosp.com/portal', '2026-08-30', (SELECT id FROM master_renewal_types WHERE name='Half-Yearly'), (SELECT id FROM master_paid_status_types WHERE name='Paid')),
((SELECT id FROM customers WHERE name='Velocity Motors'), 'Dealer Management Website', (SELECT id FROM master_project_types WHERE name='Website'), (SELECT id FROM master_servers WHERE name='GoDaddy'), 'Client-side (GoDaddy)', 'https://velocitymotors.in', '2026-12-05', (SELECT id FROM master_renewal_types WHERE name='Yearly'), (SELECT id FROM master_paid_status_types WHERE name='Paid')),
((SELECT id FROM customers WHERE name='Orbit Learning Academy'), 'LMS Platform', (SELECT id FROM master_project_types WHERE name='Web Application'), (SELECT id FROM master_servers WHERE name='Hostinger'), 'With us (KTG)', 'https://orbitlearning.edu — admin: orbitlearning.edu/cp', '2026-07-15', (SELECT id FROM master_renewal_types WHERE name='Monthly'), (SELECT id FROM master_paid_status_types WHERE name='Overdue')),
((SELECT id FROM customers WHERE name='Green Leaf Organics'), 'Organic Store Website', (SELECT id FROM master_project_types WHERE name='Website'), (SELECT id FROM master_servers WHERE name='GoDaddy'), 'Client-side (GoDaddy)', 'https://greenleaforganics.com', '2026-08-05', (SELECT id FROM master_renewal_types WHERE name='Yearly'), (SELECT id FROM master_paid_status_types WHERE name='Unpaid')),
((SELECT id FROM customers WHERE name='Pinnacle Constructions'), 'Real Estate Showcase Site', (SELECT id FROM master_project_types WHERE name='Website'), (SELECT id FROM master_servers WHERE name='Bluehost'), 'With us (KTG)', 'https://pinnacleco.in', '2027-01-20', (SELECT id FROM master_renewal_types WHERE name='Yearly'), (SELECT id FROM master_paid_status_types WHERE name='Paid'));

-- Company profiles (Settings)
INSERT INTO company_profiles (gst_type, name, website, phone, email, address, gstin, pan) VALUES
('gst', 'Knock The Globe Technologies', 'www.knocktheglobe.com', '72001 23452', 'knockus@knocktheglobe.com', '4th Cross Rd, Vaiyapuri Nagar, Karur, Tamil Nadu 639002', '33ABCDE1234F1Z5', 'ABCDE1234F'),
('nongst', 'Knock The Globe Technologies', 'www.knocktheglobe.com', '72001 23452', 'knockus@knocktheglobe.com', '4th Cross Rd, Vaiyapuri Nagar, Karur, Tamil Nadu 639002', '', '');

-- Company details (Profile.php)
INSERT INTO company_details (name, short_name, phone, email, website, established_year, address, gstin, pan, state, state_code, bank_name, bank_branch, account_holder, account_number, ifsc_code, account_type, upi_id) VALUES
('Knock The Globe Technologies', 'KTG', '72001 23452', 'knockus@knocktheglobe.com', 'www.knocktheglobe.com', '2024', '4th Cross Rd, Vaiyapuri Nagar, Karur, Tamil Nadu 639002', '33ABCDE1234F1Z5', 'ABCDE1234F', 'Tamil Nadu', '33', 'HDFC Bank', 'Karur Main Branch', 'Knock The Globe Technologies', '50100234567890', 'HDFC0001234', 'Current', 'knockus@okhdfcbank');

-- Settings: prefixes (singleton, matches Settings > Prefixes mockup)
INSERT INTO settings_prefixes (quotation_prefix, quotation_next_number, invoice_prefix, invoice_next_number, payment_prefix, payment_next_number) VALUES
('QUO-2026-', 92, 'INV-2026-', 151, 'PAY-', 2002);

-- Settings: payment modes
INSERT INTO payment_modes (name, icon, is_active, sort_order) VALUES
('Cash', 'bi-cash', 1, 1),
('UPI', 'bi-phone', 1, 2),
('Bank Transfer', 'bi-bank', 1, 3),
('Cheque', 'bi-postcard', 1, 4),
('Card', 'bi-credit-card', 1, 5);
