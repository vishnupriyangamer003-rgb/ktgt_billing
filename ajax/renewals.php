<?php
/**
 * AJAX handler for the Renewals module: list (date range / customer / paid
 * status filters), stats, customer_options (for the modal + filter dropdowns),
 * view, add, edit, soft-delete.
 */
require_once __DIR__ . '/../includes/auth.php';
require_login();
header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? 'list';
$userId = current_user()['id'] ?? null;

const RENEWAL_SELECT = "SELECT r.*, c.name AS customer_name, pt.name AS project_type_name,
    sv.name AS server_name, rt.name AS renewal_type_name, ps.name AS paid_status_name,
    ri.id AS invoice_id, ri.invoice_number AS invoice_number
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

    $projectName = clean($_POST['project_name'] ?? '');
    if ($projectName === '') json_error('Project name is required.');

    $expiryDate = clean($_POST['expiry_date'] ?? '');
    if ($expiryDate === '' || !DateTime::createFromFormat('Y-m-d', $expiryDate)) {
        json_error('Please choose a valid expiry date.');
    }

    return [
        'customer_id' => $customerId,
        'project_name' => $projectName,
        'project_type_id' => optional_master_id('project_type_id', 'master_project_types'),
        'server_id' => optional_master_id('server_id', 'master_servers'),
        'domain_control' => clean($_POST['domain_control'] ?? ''),
        'live_link' => clean($_POST['live_link'] ?? ''),
        'expiry_date' => $expiryDate,
        'renewal_type_id' => optional_master_id('renewal_type_id', 'master_renewal_types'),
        'paid_status_id' => optional_master_id('paid_status_id', 'master_paid_status_types'),
    ];
}

if ($action === 'customer_options') {
    $rows = db()->query("SELECT id, name, company_name, project_type_id FROM customers WHERE is_deleted = 0 ORDER BY name ASC")->fetchAll();
    json_success($rows);
}

if ($action === 'list') {
    $from = clean($_GET['from'] ?? '');
    $to = clean($_GET['to'] ?? '');
    $customerId = (int)($_GET['customer_id'] ?? 0);
    $paidStatusId = (int)($_GET['paid_status_id'] ?? 0);

    $where = ['r.is_deleted = 0'];
    $params = [];
    if ($from !== '') { $where[] = 'r.expiry_date >= ?'; $params[] = $from; }
    if ($to !== '') { $where[] = 'r.expiry_date <= ?'; $params[] = $to; }
    if ($customerId > 0) { $where[] = 'r.customer_id = ?'; $params[] = $customerId; }
    if ($paidStatusId > 0) { $where[] = 'r.paid_status_id = ?'; $params[] = $paidStatusId; }
    $whereSql = implode(' AND ', $where);

    $stmt = db()->prepare(RENEWAL_SELECT . " WHERE {$whereSql} ORDER BY r.expiry_date ASC");
    $stmt->execute($params);
    json_success($stmt->fetchAll());
}

if ($action === 'stats') {
    $monthStart = date('Y-m-01');
    $monthEnd = date('Y-m-t');
    $today = date('Y-m-d');

    $total = (int)db()->query("SELECT COUNT(*) FROM renewals WHERE is_deleted = 0")->fetchColumn();

    $thisMonth = db()->prepare("SELECT COUNT(*) FROM renewals WHERE is_deleted = 0 AND expiry_date >= ? AND expiry_date <= ? AND expiry_date >= ?");
    $thisMonth->execute([$monthStart, $monthEnd, $today]);

    $expired = db()->prepare("SELECT COUNT(*) FROM renewals WHERE is_deleted = 0 AND expiry_date < ?");
    $expired->execute([$today]);

    $paid = db()->query("SELECT COUNT(*) FROM renewals r JOIN master_paid_status_types ps ON ps.id = r.paid_status_id
        WHERE r.is_deleted = 0 AND LOWER(ps.name) = 'paid'")->fetchColumn();

    json_success([
        'total' => $total,
        'this_month' => (int)$thisMonth->fetchColumn(),
        'expired' => (int)$expired->fetchColumn(),
        'paid' => (int)$paid,
    ]);
}

if ($action === 'view') {
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
        (customer_id, project_name, project_type_id, server_id, domain_control, live_link, expiry_date, renewal_type_id, paid_status_id, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $data['customer_id'], $data['project_name'], $data['project_type_id'], $data['server_id'],
        $data['domain_control'], $data['live_link'], $data['expiry_date'], $data['renewal_type_id'],
        $data['paid_status_id'], $userId,
    ]);

    json_success(['id' => (int)db()->lastInsertId()], 'Renewal entry added successfully.');
}

if ($action === 'edit') {
    verify_csrf();
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) json_error('Invalid record.');

    $existing = db()->prepare('SELECT id FROM renewals WHERE id = ? AND is_deleted = 0');
    $existing->execute([$id]);
    if (!$existing->fetch()) json_error('This renewal entry no longer exists.', 404);

    $data = validate_renewal_input();

    $stmt = db()->prepare('UPDATE renewals SET
        customer_id=?, project_name=?, project_type_id=?, server_id=?, domain_control=?, live_link=?,
        expiry_date=?, renewal_type_id=?, paid_status_id=?, updated_by=? WHERE id=?');
    $stmt->execute([
        $data['customer_id'], $data['project_name'], $data['project_type_id'], $data['server_id'],
        $data['domain_control'], $data['live_link'], $data['expiry_date'], $data['renewal_type_id'],
        $data['paid_status_id'], $userId, $id,
    ]);

    json_success([], 'Renewal entry updated successfully.');
}

if ($action === 'delete') {
    verify_csrf();
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) json_error('Invalid record.');
    $stmt = db()->prepare('UPDATE renewals SET is_deleted = 1, deleted_at = NOW(), deleted_by = ? WHERE id = ? AND is_deleted = 0');
    $stmt->execute([$userId, $id]);
    json_success([], 'Renewal entry deleted successfully.');
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
            customer_name=?, customer_gst=?, customer_address=?, customer_mobile=?,
            items_json=?, subtotal=?, discount=?, tax_type=?, tax_percent=?, tax_amount=?,
            round_off=?, grand_total=?, advance_paid=?, balance_due=?, terms=?, updated_by=?
            WHERE renewal_id=?');
        $stmt->execute([
            $data['invoice_number'], $data['gst_type'], $data['doc_type'], $data['invoice_date'], $data['due_date'],
            $data['customer_name'], $data['customer_gst'], $data['customer_address'], $data['customer_mobile'],
            $data['items_json'], $data['subtotal'], $data['discount'], $data['tax_type'], $data['tax_percent'], $data['tax_amount'],
            $data['round_off'], $data['grand_total'], $data['advance_paid'], $data['balance_due'], $data['terms'], $userId,
            $data['renewal_id'],
        ]);
        $invoiceId = (int)$existingRow['id'];
        $message = 'Invoice updated successfully.';
    } else {
        $stmt = db()->prepare('INSERT INTO renewal_invoices
            (renewal_id, invoice_number, gst_type, doc_type, invoice_date, due_date,
             customer_name, customer_gst, customer_address, customer_mobile,
             items_json, subtotal, discount, tax_type, tax_percent, tax_amount,
             round_off, grand_total, advance_paid, balance_due, terms, created_by)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $stmt->execute([
            $data['renewal_id'], $data['invoice_number'], $data['gst_type'], $data['doc_type'], $data['invoice_date'], $data['due_date'],
            $data['customer_name'], $data['customer_gst'], $data['customer_address'], $data['customer_mobile'],
            $data['items_json'], $data['subtotal'], $data['discount'], $data['tax_type'], $data['tax_percent'], $data['tax_amount'],
            $data['round_off'], $data['grand_total'], $data['advance_paid'], $data['balance_due'], $data['terms'], $userId,
        ]);
        $invoiceId = (int)db()->lastInsertId();
        $message = 'Invoice saved successfully.';
    }

    json_success(['invoice_id' => $invoiceId, 'invoice_number' => $data['invoice_number']], $message);
}

json_error('Unknown action.', 400);
