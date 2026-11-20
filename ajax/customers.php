<?php
/**
 * AJAX handler for the Customers module: list (search/filter/paginate), add,
 * edit, view (single record, used by customer-details.php), delete (soft).
 */
require_once __DIR__ . '/../includes/auth.php';
require_login();
header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? 'list';
$userId = current_user()['id'] ?? null;

const CUSTOMER_SELECT = "SELECT c.*, pt.name AS project_type_name
    FROM customers c
    LEFT JOIN master_project_types pt ON pt.id = c.project_type_id";

/**
 * The list shows one entry per company: a customer with two companies appears as two rows
 * (same customer id, so View/Edit/Delete still act on the one customer record). A customer
 * with no company on file still gets a single row. company_name is overridden with that
 * entry's own company.
 */
const CUSTOMER_LIST_FROM = " FROM customers c
    LEFT JOIN customer_companies cc ON cc.customer_id = c.id
    LEFT JOIN master_project_types pt ON pt.id = c.project_type_id";

/**
 * Companies come from `companies` (JSON array) — or, for callers that still post a single
 * `company_name` (e.g. the quick-add popup on renewals.php), from that. Trimmed, de-duplicated
 * case-insensitively, order kept; the first one is the customer's primary company.
 */
function parse_companies(): array {
    $raw = json_decode($_POST['companies'] ?? '', true);
    if (!is_array($raw)) $raw = [$_POST['company_name'] ?? ''];
    $out = [];
    $seen = [];
    foreach ($raw as $c) {
        $c = clean(is_string($c) ? $c : '');
        if ($c === '') continue;
        if (mb_strlen($c) > 150) json_error('Company name is too long (max 150 characters).');
        $key = mb_strtolower($c);
        if (isset($seen[$key])) continue;
        $seen[$key] = true;
        $out[] = $c;
    }
    if (count($out) > 20) json_error('A customer can have at most 20 companies.');
    return $out;
}

/** Replaces the customer's stored company list and mirrors the first into customers.company_name. */
function save_companies(int $customerId, array $companies): void {
    db()->prepare('DELETE FROM customer_companies WHERE customer_id = ?')->execute([$customerId]);
    $ins = db()->prepare('INSERT INTO customer_companies (customer_id, name, sort_order) VALUES (?, ?, ?)');
    foreach ($companies as $i => $name) $ins->execute([$customerId, $name, $i]);
    db()->prepare('UPDATE customers SET company_name = ? WHERE id = ?')->execute([$companies[0] ?? null, $customerId]);
}

/** Adds a `companies` string array to each customer row (one query for the whole set). */
function attach_companies(array &$rows): void {
    if (!$rows) return;
    $ids = array_map(fn($r) => (int)$r['id'], $rows);
    $in = implode(',', array_fill(0, count($ids), '?'));
    $stmt = db()->prepare("SELECT customer_id, name FROM customer_companies WHERE customer_id IN ({$in}) ORDER BY sort_order, id");
    $stmt->execute($ids);
    $byCustomer = [];
    foreach ($stmt->fetchAll() as $r) $byCustomer[(int)$r['customer_id']][] = $r['name'];
    foreach ($rows as &$row) {
        $row['companies'] = $byCustomer[(int)$row['id']] ?? [];
    }
    unset($row);
}

/** Validates customer fields from $_POST. Exits with a JSON error on failure. */
function validate_customer_input(): array {
    $name = clean($_POST['name'] ?? '');
    $mobile = clean($_POST['mobile'] ?? '');
    $email = clean($_POST['email'] ?? '');

    $companies = parse_companies();

    if ($name === '') json_error('Customer name is required.');
    if (mb_strlen($name) > 150) json_error('Customer name is too long.');
    if ($mobile === '') json_error('Mobile number is required.');
    if (!preg_match('/^[0-9+\-\s()]{7,20}$/', $mobile)) json_error('Please enter a valid mobile number.');
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) json_error('Please enter a valid email address.');

    $status = $_POST['status'] ?? 'pending';
    if (!in_array($status, ['active', 'pending', 'inactive'], true)) $status = 'pending';

    $projectTypeId = (int)($_POST['project_type_id'] ?? 0);
    if ($projectTypeId > 0) {
        $chk = db()->prepare("SELECT id FROM master_project_types WHERE id = ? AND status = 'active'");
        $chk->execute([$projectTypeId]);
        if (!$chk->fetch()) json_error('Please choose a valid project type.');
    } else {
        $projectTypeId = null;
    }

    return [
        'name' => $name,
        'companies' => $companies,
        'company_name' => $companies[0] ?? '',
        'contact_person' => clean($_POST['contact_person'] ?? ''),
        'mobile' => $mobile,
        'whatsapp' => clean($_POST['whatsapp'] ?? ''),
        'email' => $email,
        'gst_number' => clean($_POST['gst_number'] ?? ''),
        'address' => clean($_POST['address'] ?? ''),
        'city' => clean($_POST['city'] ?? ''),
        'project_type_id' => $projectTypeId,
        'status' => $status,
        'notes' => clean($_POST['notes'] ?? ''),
    ];
}

if ($action === 'list') {
    $q = clean($_GET['q'] ?? '');
    $status = $_GET['status'] ?? '';
    $projectTypeId = (int)($_GET['project_type_id'] ?? 0);
    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = min(500, max(1, (int)($_GET['per_page'] ?? 50)));
    $offset = ($page - 1) * $perPage;

    $where = ['c.is_deleted = 0'];
    $params = [];
    if ($q !== '') {
        // Searching a company name narrows to that company's row; name/mobile/email keep all of a customer's rows.
        $where[] = '(c.name LIKE ? OR cc.name LIKE ? OR c.company_name LIKE ? OR c.mobile LIKE ? OR c.email LIKE ?)';
        $like = '%' . $q . '%';
        array_push($params, $like, $like, $like, $like, $like);
    }
    if (in_array($status, ['active', 'pending', 'inactive'], true)) {
        $where[] = 'c.status = ?';
        $params[] = $status;
    }
    if ($projectTypeId > 0) {
        $where[] = 'c.project_type_id = ?';
        $params[] = $projectTypeId;
    }
    $whereSql = implode(' AND ', $where);

    $countStmt = db()->prepare("SELECT COUNT(*)" . CUSTOMER_LIST_FROM . " WHERE {$whereSql}");
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();

    $stmt = db()->prepare("SELECT c.*, COALESCE(cc.name, c.company_name) AS company_name, pt.name AS project_type_name"
        . CUSTOMER_LIST_FROM . " WHERE {$whereSql} ORDER BY c.created_at DESC, c.id DESC, cc.sort_order ASC, cc.id ASC LIMIT {$perPage} OFFSET {$offset}");
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    json_success([
        'rows' => $rows,
        'total' => $total,
        'page' => $page,
        'per_page' => $perPage,
        'total_pages' => (int)ceil($total / $perPage),
    ]);
}

if ($action === 'stats') {
    $counts = db()->query("SELECT status, COUNT(*) c FROM customers WHERE is_deleted = 0 GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
    json_success([
        'total' => array_sum($counts),
        'active' => (int)($counts['active'] ?? 0),
        'pending' => (int)($counts['pending'] ?? 0),
        'inactive' => (int)($counts['inactive'] ?? 0),
    ]);
}

if ($action === 'view') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) json_error('Invalid customer.');
    $stmt = db()->prepare(CUSTOMER_SELECT . ' WHERE c.id = ? AND c.is_deleted = 0');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) json_error('Customer not found.', 404);
    $rows = [$row];
    attach_companies($rows);
    json_success($rows[0]);
}

if ($action === 'add') {
    verify_csrf();
    $data = validate_customer_input();

    $dupe = db()->prepare('SELECT id FROM customers WHERE is_deleted = 0 AND mobile = ?');
    $dupe->execute([$data['mobile']]);
    if ($dupe->fetch()) {
        json_error('A customer with this mobile number already exists.');
    }

    $stmt = db()->prepare('INSERT INTO customers
        (name, company_name, contact_person, mobile, whatsapp, email, gst_number, address, city, project_type_id, status, notes, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $data['name'], $data['company_name'], $data['contact_person'], $data['mobile'], $data['whatsapp'],
        $data['email'], $data['gst_number'], $data['address'], $data['city'], $data['project_type_id'],
        $data['status'], $data['notes'], $userId,
    ]);

    $newId = (int)db()->lastInsertId();
    save_companies($newId, $data['companies']);

    json_success(['id' => $newId], 'Customer added successfully.');
}

if ($action === 'edit') {
    verify_csrf();
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) json_error('Invalid customer.');

    $existing = db()->prepare('SELECT id FROM customers WHERE id = ? AND is_deleted = 0');
    $existing->execute([$id]);
    if (!$existing->fetch()) json_error('This customer no longer exists.', 404);

    $data = validate_customer_input();

    $dupe = db()->prepare('SELECT id FROM customers WHERE is_deleted = 0 AND mobile = ? AND id != ?');
    $dupe->execute([$data['mobile'], $id]);
    if ($dupe->fetch()) {
        json_error('Another customer already uses this mobile number.');
    }

    $stmt = db()->prepare('UPDATE customers SET
        name=?, company_name=?, contact_person=?, mobile=?, whatsapp=?, email=?, gst_number=?, address=?, city=?,
        project_type_id=?, status=?, notes=?, updated_by=? WHERE id=?');
    $stmt->execute([
        $data['name'], $data['company_name'], $data['contact_person'], $data['mobile'], $data['whatsapp'],
        $data['email'], $data['gst_number'], $data['address'], $data['city'], $data['project_type_id'],
        $data['status'], $data['notes'], $userId, $id,
    ]);

    save_companies($id, $data['companies']);

    json_success([], 'Customer updated successfully.');
}

if ($action === 'delete') {
    verify_csrf();
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) json_error('Invalid customer.');
    $stmt = db()->prepare('UPDATE customers SET is_deleted = 1, deleted_at = NOW(), deleted_by = ? WHERE id = ? AND is_deleted = 0');
    $stmt->execute([$userId, $id]);
    json_success([], 'Customer deleted successfully.');
}

json_error('Unknown action.', 400);
