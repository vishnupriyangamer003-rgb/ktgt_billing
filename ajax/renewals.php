<?php
/**
 * AJAX handler for the Renewals module: list (date range / customer / renewal
 * status / active status filters), customer_options (for the modal + filter dropdowns),
 * view, add, edit, soft-delete, set_renewed, set_paid_status, set_status.
 * The dashboard stat cards are computed client-side from the list response rather
 * than a separate stats action, so they always track the currently applied filters.
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/renewal_status.php';
require_login();
header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? 'list';
$userId = current_user()['id'] ?? null;

/**
 * The company a renewal belongs to. It is the company saved on the renewal — but only while that
 * is still one of the customer's companies. If the customer's companies were edited/split/renamed
 * afterwards (so the saved name no longer exists), the renewal falls back to the customer's first
 * company instead of becoming unreachable from every dropdown entry and filter.
 */
const RENEWAL_COMPANY_EXPR = "CASE WHEN r.company_name IS NOT NULL AND r.company_name <> ''
      AND EXISTS (SELECT 1 FROM customer_companies rcc WHERE rcc.customer_id = r.customer_id AND rcc.name = r.company_name)
    THEN r.company_name ELSE c.company_name END";

const RENEWAL_SELECT = "SELECT r.*, c.name AS customer_name, " . RENEWAL_COMPANY_EXPR . " AS company_name, pt.name AS project_type_name,
    sv.name AS server_name, rt.name AS renewal_type_name, ps.name AS paid_status_name,
    ri.id AS invoice_id, ri.invoice_number AS invoice_number, ri.grand_total AS invoice_grand_total
    FROM renewals r
    JOIN customers c ON c.id = r.customer_id
    LEFT JOIN master_project_types pt ON pt.id = r.project_type_id
    LEFT JOIN master_servers sv ON sv.id = r.server_id
    LEFT JOIN master_renewal_types rt ON rt.id = r.renewal_type_id
    LEFT JOIN master_paid_status_types ps ON ps.id = r.paid_status_id
    LEFT JOIN renewal_invoices ri ON ri.renewal_id = r.id";

/** Optionally-referenced master id: validates it points at an active row, or returns null. */
function optional_master_id(string $field, string $table): ?int {
    $id = (int)($_POST[$field] ?? 0);
    if ($id <= 0) return null;
    $chk = db()->prepare("SELECT id FROM {$table} WHERE id = ? AND status = 'active'");
    $chk->execute([$id]);
    if (!$chk->fetch()) json_error('One of the selected options is no longer valid. Please refresh and try again.');
    return $id;
}

function validate_renewal_input(): array {
    $customerId = (int)($_POST['customer_id'] ?? 0);
    if ($customerId <= 0) json_error('Please select a customer.');
    $custChk = db()->prepare('SELECT id FROM customers WHERE id = ? AND is_deleted = 0');
    $custChk->execute([$customerId]);
    if (!$custChk->fetch()) json_error('Please select a valid customer.');

    $companyName = resolve_customer_company($customerId, $_POST['company_name'] ?? '');

    $projectName = clean($_POST['project_name'] ?? '');
    if ($projectName === '') json_error('Project name is required.');

    $expiryDate = clean($_POST['expiry_date'] ?? '');
    if ($expiryDate === '' || !DateTime::createFromFormat('Y-m-d', $expiryDate)) {
        json_error('Please choose a valid expiry date.');
    }

    $status = in_array($_POST['status'] ?? '', ['active', 'inactive']) ? $_POST['status'] : 'active';

    return [
        'customer_id' => $customerId,
        'company_name' => $companyName,
        'project_name' => $projectName,
        'project_type_id' => optional_master_id('project_type_id', 'master_project_types'),
        'server_id' => optional_master_id('server_id', 'master_servers'),
        'domain_control' => clean($_POST['domain_control'] ?? ''),
        'live_link' => clean($_POST['live_link'] ?? ''),
        'expiry_date' => $expiryDate,
        'renewal_type_id' => optional_master_id('renewal_type_id', 'master_renewal_types'),
        'paid_status_id' => optional_master_id('paid_status_id', 'master_paid_status_types') ?? renewal_unpaid_status_id(),
        'status' => $status,
    ];
}

if ($action === 'customer_options') {
    // One entry per company: a customer with 3 companies yields 3 rows (same id, different company_name).
    $rows = db()->query("SELECT c.id, c.name, COALESCE(cc.name, c.company_name) AS company_name, c.mobile, c.project_type_id FROM customers c
        LEFT JOIN customer_companies cc ON cc.customer_id = c.id
        WHERE c.is_deleted = 0 ORDER BY c.name ASC, cc.sort_order ASC, cc.id ASC")->fetchAll();
    json_success($rows);
}

if ($action === 'list') {
    renewal_sync_paid_status_for_expiry();
    $from = clean($_GET['from'] ?? '');
    $to = clean($_GET['to'] ?? '');
    $customerId = (int)($_GET['customer_id'] ?? 0);
    $company = clean($_GET['company'] ?? '');
    $serverId = (int)($_GET['server_id'] ?? 0);
    $paidStatusId = (int)($_GET['paid_status_id'] ?? 0);
    $renewalStatus = $_GET['renewal_status'] ?? '';
    $activeStatus = clean($_GET['status'] ?? '');

    $where = ['r.is_deleted = 0'];
    $params = [];
    if ($from !== '') { $where[] = 'r.expiry_date >= ?'; $params[] = $from; }
    if ($to !== '') { $where[] = 'r.expiry_date <= ?'; $params[] = $to; }
    if ($customerId > 0) { $where[] = 'r.customer_id = ?'; $params[] = $customerId; }
    if ($customerId > 0 && $company !== '') { $where[] = '(' . RENEWAL_COMPANY_EXPR . ') = ?'; $params[] = $company; }
    if ($serverId > 0) { $where[] = 'r.server_id = ?'; $params[] = $serverId; }
    if ($paidStatusId > 0) { $where[] = 'r.paid_status_id = ?'; $params[] = $paidStatusId; }
    if (in_array($activeStatus, ['active', 'inactive'])) { $where[] = 'r.status = ?'; $params[] = $activeStatus; }
    // Kept for the dashboard cards, which still compute Expiring This Month / Pending
    // Renewal / Renewal Done from expiry_date — the list's own Status filter now maps
    // to paid_status_id above (Paid/Unpaid/Hold/Overdue), not this.
    if ($renewalStatus === 'done') { $where[] = 'r.expiry_date >= CURDATE()'; }
    if ($renewalStatus === 'pending') { $where[] = 'r.expiry_date < CURDATE()'; }
    $whereSql = implode(' AND ', $where);

    $stmt = db()->prepare(RENEWAL_SELECT . " WHERE {$whereSql} ORDER BY r.expiry_date ASC");
    $stmt->execute($params);
    json_success($stmt->fetchAll());
}

if ($action === 'view') {
    renewal_sync_paid_status_for_expiry();
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) json_error('Invalid record.');
    $stmt = db()->prepare(RENEWAL_SELECT . ' WHERE r.id = ? AND r.is_deleted = 0');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) json_error('Renewal entry not found.', 404);
    json_success($row);
}

if ($action === 'add') {
    verify_csrf();
    $data = validate_renewal_input();

    $stmt = db()->prepare('INSERT INTO renewals
        (customer_id, company_name, project_name, project_type_id, server_id, domain_control, live_link, expiry_date, renewal_type_id, paid_status_id, status, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $data['customer_id'], $data['company_name'], $data['project_name'], $data['project_type_id'], $data['server_id'],
        $data['domain_control'], $data['live_link'], $data['expiry_date'], $data['renewal_type_id'],
        $data['paid_status_id'], $data['status'], $userId,
    ]);

    json_success(['id' => (int)db()->lastInsertId()], 'Renewal entry added successfully.');
}

if ($action === 'edit') {
    verify_csrf();
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) json_error('Invalid record.');

    $existing = db()->prepare('SELECT paid_status_id, expiry_date, renewal_type_id FROM renewals WHERE id = ? AND is_deleted = 0');
    $existing->execute([$id]);
    $existingRow = $existing->fetch();
    if (!$existingRow) json_error('This renewal entry no longer exists.', 404);

    $data = validate_renewal_input();

    // Unpaid -> Paid: expiry date auto-extends by renewal interval (e.g. 1 month for monthly, 1 year for yearly)
    // from its current value, overriding whatever expiry_date was submitted alongside the status change.
    $extendedExpiry = renewal_extend_expiry_if_newly_paid($existingRow['paid_status_id'], $existingRow['expiry_date'], $data['paid_status_id'], $data['renewal_type_id'] ?? $existingRow['renewal_type_id']);
    if ($extendedExpiry !== null) $data['expiry_date'] = $extendedExpiry;

    $stmt = db()->prepare('UPDATE renewals SET
        customer_id=?, company_name=?, project_name=?, project_type_id=?, server_id=?, domain_control=?, live_link=?,
        expiry_date=?, renewal_type_id=?, paid_status_id=?, status=?, updated_by=? WHERE id=?');
    $stmt->execute([
        $data['customer_id'], $data['company_name'], $data['project_name'], $data['project_type_id'], $data['server_id'],
        $data['domain_control'], $data['live_link'], $data['expiry_date'], $data['renewal_type_id'],
        $data['paid_status_id'], $data['status'], $userId, $id,
    ]);

    json_success(['expiry_date' => $data['expiry_date'], 'expiry_extended' => $extendedExpiry !== null], 'Renewal entry updated successfully.');
}

if ($action === 'delete') {
    verify_csrf();
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) json_error('Invalid record.');
    $stmt = db()->prepare('UPDATE renewals SET is_deleted = 1, deleted_at = NOW(), deleted_by = ? WHERE id = ? AND is_deleted = 0');
    $stmt->execute([$userId, $id]);
    json_success([], 'Renewal entry deleted successfully.');
}

/* Manually-set Active/Inactive status — opened by clicking the Status chip in the list */
if ($action === 'set_status') {
    verify_csrf();
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) json_error('Invalid record.');
    $status = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';
    $stmt = db()->prepare('UPDATE renewals SET status = ?, updated_by = ? WHERE id = ? AND is_deleted = 0');
    $stmt->execute([$status, $userId, $id]);
    json_success(['status' => $status], 'Status updated successfully.');
}

/* Manually-set Renewed/Not Renewed flag — deliberately separate from the computed Renewal
 * Status (expiry_date based). This one is not auto-recomputed; it stays whatever it was
 * last set to until the user changes it again via the list's Renewed popup. */
if ($action === 'set_renewed') {
    verify_csrf();
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) json_error('Invalid record.');
    $isRenewed = ($_POST['is_renewed'] ?? '0') === '1' ? 1 : 0;
    $stmt = db()->prepare('UPDATE renewals SET is_renewed = ?, updated_by = ? WHERE id = ? AND is_deleted = 0');
    $stmt->execute([$isRenewed, $userId, $id]);
    json_success(['is_renewed' => $isRenewed], 'Renewed status updated.');
}

/* Quick popup update for Paid Status, same pattern as set_renewed — doesn't require
 * resubmitting the whole renewal record just to change this one field. Always resolves to
 * a real status (defaults to Unpaid) since a renewal should never be left with no paid status.
 * Unpaid -> Paid here auto-extends the expiry date based on the renewal type interval. */
if ($action === 'set_paid_status') {
    verify_csrf();
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) json_error('Invalid record.');

    $existing = db()->prepare('SELECT paid_status_id, expiry_date, renewal_type_id FROM renewals WHERE id = ? AND is_deleted = 0');
    $existing->execute([$id]);
    $existingRow = $existing->fetch();
    if (!$existingRow) json_error('This renewal entry no longer exists.', 404);

    $paidStatusId = optional_master_id('paid_status_id', 'master_paid_status_types') ?? renewal_unpaid_status_id();
    $extendedExpiry = renewal_extend_expiry_if_newly_paid($existingRow['paid_status_id'], $existingRow['expiry_date'], $paidStatusId, $existingRow['renewal_type_id']);
    $expiryDate = $extendedExpiry ?? $existingRow['expiry_date'];

    $stmt = db()->prepare('UPDATE renewals SET paid_status_id = ?, expiry_date = ?, updated_by = ? WHERE id = ? AND is_deleted = 0');
    $stmt->execute([$paidStatusId, $expiryDate, $userId, $id]);
    json_success(['paid_status_id' => $paidStatusId, 'expiry_date' => $expiryDate, 'expiry_extended' => $extendedExpiry !== null], 'Paid status updated.');
}

/** Sanitizes the posted items array into a clean list of {service, desc, qty, rate}. */
function validate_invoice_items(): array {
    $raw = json_decode($_POST['items'] ?? '[]', true);
    if (!is_array($raw)) json_error('Invalid items data.');
    $items = [];
    foreach ($raw as $it) {
        $service = clean($it['service'] ?? '');
        if ($service === '') continue;
        $items[] = [
            'service' => $service,
            'desc' => clean($it['desc'] ?? ''),
            'qty' => (float)($it['qty'] ?? 0),
            'rate' => (float)($it['rate'] ?? 0),
        ];
    }
    if (!count($items)) json_error('Please add at least one item.');
    return $items;
}

function validate_invoice_input(): array {
    $renewalId = (int)($_POST['renewal_id'] ?? 0);
    if ($renewalId <= 0) json_error('Invalid renewal entry.');
    $chk = db()->prepare('SELECT id FROM renewals WHERE id = ? AND is_deleted = 0');
    $chk->execute([$renewalId]);
    if (!$chk->fetch()) json_error('This renewal entry no longer exists.');

    $invoiceNumber = clean($_POST['invoice_number'] ?? '');
    if ($invoiceNumber === '') json_error('Invoice number is required.');

    $invoiceDate = clean($_POST['invoice_date'] ?? '');
    if ($invoiceDate === '' || !DateTime::createFromFormat('Y-m-d', $invoiceDate)) {
        json_error('Please choose a valid invoice date.');
    }
    $dueDate = clean($_POST['due_date'] ?? '');
    if ($dueDate !== '' && !DateTime::createFromFormat('Y-m-d', $dueDate)) {
        json_error('Please choose a valid due date.');
    }

    return [
        'renewal_id' => $renewalId,
        'invoice_number' => $invoiceNumber,
        'gst_type' => ($_POST['gst_type'] ?? 'gst') === 'nongst' ? 'nongst' : 'gst',
        'doc_type' => clean($_POST['doc_type'] ?? 'TAX INVOICE'),
        'invoice_date' => $invoiceDate,
        'due_date' => $dueDate !== '' ? $dueDate : null,
        'customer_name' => clean($_POST['customer_name'] ?? ''),
        'company_name' => clean($_POST['company_name'] ?? '') ?: null,
        'customer_gst' => clean($_POST['customer_gst'] ?? ''),
        'customer_address' => clean($_POST['customer_address'] ?? ''),
        'customer_mobile' => clean($_POST['customer_mobile'] ?? ''),
        'items_json' => json_encode(validate_invoice_items()),
        'subtotal' => (float)($_POST['subtotal'] ?? 0),
        'discount' => (float)($_POST['discount'] ?? 0),
        'tax_type' => clean($_POST['tax_type'] ?? 'none'),
        'tax_percent' => (float)($_POST['tax_percent'] ?? 0),
        'tax_amount' => (float)($_POST['tax_amount'] ?? 0),
        'round_off' => (float)($_POST['round_off'] ?? 0),
        'grand_total' => (float)($_POST['grand_total'] ?? 0),
        'advance_paid' => (float)($_POST['advance_paid'] ?? 0),
        'balance_due' => (float)($_POST['balance_due'] ?? 0),
        'terms' => clean($_POST['terms'] ?? ''),
    ];
}

if ($action === 'invoice_view') {
    $renewalId = (int)($_GET['renewal_id'] ?? 0);
    if ($renewalId <= 0) json_error('Invalid record.');
    $stmt = db()->prepare('SELECT * FROM renewal_invoices WHERE renewal_id = ?');
    $stmt->execute([$renewalId]);
    $row = $stmt->fetch();
    if (!$row) json_error('Invoice not found for this renewal entry.', 404);
    $row['items'] = json_decode($row['items_json'], true) ?: [];
    unset($row['items_json']);
    json_success($row);
}

if ($action === 'invoice_save') {
    verify_csrf();
    $data = validate_invoice_input();

    $existing = db()->prepare('SELECT id FROM renewal_invoices WHERE renewal_id = ?');
    $existing->execute([$data['renewal_id']]);
    $existingRow = $existing->fetch();

    if ($existingRow) {
        $stmt = db()->prepare('UPDATE renewal_invoices SET
            invoice_number=?, gst_type=?, doc_type=?, invoice_date=?, due_date=?,
            customer_name=?, company_name=?, customer_gst=?, customer_address=?, customer_mobile=?,
            items_json=?, subtotal=?, discount=?, tax_type=?, tax_percent=?, tax_amount=?,
            round_off=?, grand_total=?, advance_paid=?, balance_due=?, terms=?, updated_by=?
            WHERE renewal_id=?');
        $stmt->execute([
            $data['invoice_number'], $data['gst_type'], $data['doc_type'], $data['invoice_date'], $data['due_date'],
            $data['customer_name'], $data['company_name'], $data['customer_gst'], $data['customer_address'], $data['customer_mobile'],
            $data['items_json'], $data['subtotal'], $data['discount'], $data['tax_type'], $data['tax_percent'], $data['tax_amount'],
            $data['round_off'], $data['grand_total'], $data['advance_paid'], $data['balance_due'], $data['terms'], $userId,
            $data['renewal_id'],
        ]);
        $invoiceId = (int)$existingRow['id'];
        $message = 'Invoice updated successfully.';
    } else {
        $stmt = db()->prepare('INSERT INTO renewal_invoices
            (renewal_id, invoice_number, gst_type, doc_type, invoice_date, due_date,
             customer_name, company_name, customer_gst, customer_address, customer_mobile,
             items_json, subtotal, discount, tax_type, tax_percent, tax_amount,
             round_off, grand_total, advance_paid, balance_due, terms, created_by)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $stmt->execute([
            $data['renewal_id'], $data['invoice_number'], $data['gst_type'], $data['doc_type'], $data['invoice_date'], $data['due_date'],
            $data['customer_name'], $data['company_name'], $data['customer_gst'], $data['customer_address'], $data['customer_mobile'],
            $data['items_json'], $data['subtotal'], $data['discount'], $data['tax_type'], $data['tax_percent'], $data['tax_amount'],
            $data['round_off'], $data['grand_total'], $data['advance_paid'], $data['balance_due'], $data['terms'], $userId,
        ]);
        $invoiceId = (int)db()->lastInsertId();
        $message = 'Invoice saved successfully.';
    }

    json_success(['invoice_id' => $invoiceId, 'invoice_number' => $data['invoice_number']], $message);
}

json_error('Unknown action.', 400);
