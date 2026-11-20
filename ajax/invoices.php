<?php
/**
 * AJAX handler for the Invoices module: list (status/GST/date filters),
 * stats, customer_options, next_number preview, view, add, edit, soft-delete.
 */
require_once __DIR__ . '/../includes/auth.php';
require_login();
header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? 'list';
$userId = current_user()['id'] ?? null;

const INVOICE_STATUSES = ['draft', 'pending', 'partial', 'paid', 'overdue', 'cancelled'];

/** GST invoices use the INV- prefix, Non-GST (Bill of Supply) use BOS- — each series
 * numbers independently per year, scanned from existing rows (soft-deleted included,
 * so numbers are never reused once issued). */
function compute_next_invoice_number(string $gstType): string {
    $prefix = $gstType === 'nongst' ? 'BOS' : 'INV';
    $year = date('Y');
    $stmt = db()->prepare("SELECT invoice_number FROM invoices WHERE invoice_number LIKE ?");
    $stmt->execute(["{$prefix}-{$year}-%"]);
    $max = 0;
    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $num) {
        if (preg_match('/^' . preg_quote($prefix, '/') . '-' . preg_quote($year, '/') . '-(\d+)$/', $num, $m)) {
            $max = max($max, (int)$m[1]);
        }
    }
    return sprintf('%s-%s-%04d', $prefix, $year, $max + 1);
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
    $customerId = (int)($_POST['customer_id'] ?? 0);
    if ($customerId <= 0) json_error('Please select a customer.');
    $custChk = db()->prepare('SELECT id FROM customers WHERE id = ? AND is_deleted = 0');
    $custChk->execute([$customerId]);
    if (!$custChk->fetch()) json_error('Please select a valid customer.');

    $invoiceDate = clean($_POST['invoice_date'] ?? '');
    if ($invoiceDate === '' || !DateTime::createFromFormat('Y-m-d', $invoiceDate)) {
        json_error('Please choose a valid invoice date.');
    }
    $dueDate = clean($_POST['due_date'] ?? '');
    if ($dueDate !== '' && !DateTime::createFromFormat('Y-m-d', $dueDate)) {
        json_error('Please choose a valid due date.');
    }

    $status = $_POST['status'] ?? 'pending';
    if (!in_array($status, INVOICE_STATUSES, true)) $status = 'pending';

    return [
        'customer_id' => $customerId,
        'company_name' => resolve_customer_company($customerId, $_POST['company_name'] ?? ''),
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
        'status' => $status,
        'terms' => clean($_POST['terms'] ?? ''),
    ];
}

if ($action === 'customer_options') {
    // One entry per company: a customer with 3 companies yields 3 rows (same id, different company_name).
    $rows = db()->query("SELECT c.id, c.name, COALESCE(cc.name, c.company_name) AS company_name, c.gst_number, c.address, c.mobile FROM customers c
        LEFT JOIN customer_companies cc ON cc.customer_id = c.id
        WHERE c.is_deleted = 0 ORDER BY c.name ASC, cc.sort_order ASC, cc.id ASC")->fetchAll();
    json_success($rows);
}

if ($action === 'next_number') {
    $gstType = ($_GET['gst_type'] ?? 'gst') === 'nongst' ? 'nongst' : 'gst';
    json_success(['invoice_number' => compute_next_invoice_number($gstType)]);
}

if ($action === 'list') {
    $status = $_GET['status'] ?? '';
    $gstType = $_GET['gst_type'] ?? '';
    $from = clean($_GET['from'] ?? '');
    $to = clean($_GET['to'] ?? '');

    $where = ['is_deleted = 0'];
    $params = [];
    if (in_array($status, INVOICE_STATUSES, true)) { $where[] = 'status = ?'; $params[] = $status; }
    if (in_array($gstType, ['gst', 'nongst'], true)) { $where[] = 'gst_type = ?'; $params[] = $gstType; }
    if ($from !== '') { $where[] = 'invoice_date >= ?'; $params[] = $from; }
    if ($to !== '') { $where[] = 'invoice_date <= ?'; $params[] = $to; }
    $whereSql = implode(' AND ', $where);

    $stmt = db()->prepare("SELECT * FROM invoices WHERE {$whereSql} ORDER BY invoice_date DESC, id DESC");
    $stmt->execute($params);
    json_success($stmt->fetchAll());
}

if ($action === 'stats') {
    $gstType = $_GET['gst_type'] ?? '';
    $where = 'is_deleted = 0';
    $params = [];
    if (in_array($gstType, ['gst', 'nongst'], true)) { $where .= ' AND gst_type = ?'; $params[] = $gstType; }
    $stmt = db()->prepare("SELECT status, COUNT(*) c FROM invoices WHERE {$where} GROUP BY status");
    $stmt->execute($params);
    $counts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    json_success([
        'paid' => (int)($counts['paid'] ?? 0),
        'pending' => (int)($counts['pending'] ?? 0),
        'partial' => (int)($counts['partial'] ?? 0),
        'overdue' => (int)($counts['overdue'] ?? 0),
    ]);
}

if ($action === 'view') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) json_error('Invalid invoice.');
    $stmt = db()->prepare('SELECT * FROM invoices WHERE id = ? AND is_deleted = 0');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) json_error('Invoice not found.', 404);
    $row['items'] = json_decode($row['items_json'], true) ?: [];
    unset($row['items_json']);
    json_success($row);
}

if ($action === 'add') {
    verify_csrf();
    $data = validate_invoice_input();
    $invoiceNumber = compute_next_invoice_number($data['gst_type']);

    $stmt = db()->prepare('INSERT INTO invoices
        (invoice_number, customer_id, company_name, gst_type, doc_type, invoice_date, due_date,
         customer_name, customer_gst, customer_address, customer_mobile,
         items_json, subtotal, discount, tax_type, tax_percent, tax_amount,
         round_off, grand_total, advance_paid, balance_due, status, terms, created_by)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    $stmt->execute([
        $invoiceNumber, $data['customer_id'], $data['company_name'], $data['gst_type'], $data['doc_type'], $data['invoice_date'], $data['due_date'],
        $data['customer_name'], $data['customer_gst'], $data['customer_address'], $data['customer_mobile'],
        $data['items_json'], $data['subtotal'], $data['discount'], $data['tax_type'], $data['tax_percent'], $data['tax_amount'],
        $data['round_off'], $data['grand_total'], $data['advance_paid'], $data['balance_due'], $data['status'], $data['terms'], $userId,
    ]);

    json_success(['id' => (int)db()->lastInsertId(), 'invoice_number' => $invoiceNumber], 'Invoice saved successfully.');
}

if ($action === 'edit') {
    verify_csrf();
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) json_error('Invalid invoice.');

    $existing = db()->prepare('SELECT id FROM invoices WHERE id = ? AND is_deleted = 0');
    $existing->execute([$id]);
    if (!$existing->fetch()) json_error('This invoice no longer exists.', 404);

    $data = validate_invoice_input();

    $stmt = db()->prepare('UPDATE invoices SET
        customer_id=?, company_name=?, gst_type=?, doc_type=?, invoice_date=?, due_date=?,
        customer_name=?, customer_gst=?, customer_address=?, customer_mobile=?,
        items_json=?, subtotal=?, discount=?, tax_type=?, tax_percent=?, tax_amount=?,
        round_off=?, grand_total=?, advance_paid=?, balance_due=?, status=?, terms=?, updated_by=?
        WHERE id=?');
    $stmt->execute([
        $data['customer_id'], $data['company_name'], $data['gst_type'], $data['doc_type'], $data['invoice_date'], $data['due_date'],
        $data['customer_name'], $data['customer_gst'], $data['customer_address'], $data['customer_mobile'],
        $data['items_json'], $data['subtotal'], $data['discount'], $data['tax_type'], $data['tax_percent'], $data['tax_amount'],
        $data['round_off'], $data['grand_total'], $data['advance_paid'], $data['balance_due'], $data['status'], $data['terms'], $userId,
        $id,
    ]);

    json_success([], 'Invoice updated successfully.');
}

if ($action === 'delete') {
    verify_csrf();
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) json_error('Invalid invoice.');
    $stmt = db()->prepare('UPDATE invoices SET is_deleted = 1, deleted_at = NOW(), deleted_by = ? WHERE id = ? AND is_deleted = 0');
    $stmt->execute([$userId, $id]);
    json_success([], 'Invoice deleted successfully.');
}

json_error('Unknown action.', 400);
