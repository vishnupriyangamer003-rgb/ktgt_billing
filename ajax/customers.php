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

/** Validates customer fields from $_POST. Exits with a JSON error on failure. */
function validate_customer_input(): array {
    $name = clean($_POST['name'] ?? '');
    $mobile = clean($_POST['mobile'] ?? '');
    $email = clean($_POST['email'] ?? '');

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
        'company_name' => clean($_POST['company_name'] ?? ''),
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
    $perPage = min(50, max(1, (int)($_GET['per_page'] ?? 10)));
    $offset = ($page - 1) * $perPage;

    $where = ['c.is_deleted = 0'];
    $params = [];
    if ($q !== '') {
        $where[] = '(c.name LIKE ? OR c.company_name LIKE ? OR c.mobile LIKE ? OR c.email LIKE ?)';
        $like = '%' . $q . '%';
        array_push($params, $like, $like, $like, $like);
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

    $countStmt = db()->prepare("SELECT COUNT(*) FROM customers c WHERE {$whereSql}");
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();

    $stmt = db()->prepare(CUSTOMER_SELECT . " WHERE {$whereSql} ORDER BY c.created_at DESC LIMIT {$perPage} OFFSET {$offset}");
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
    json_success($row);
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

    json_success(['id' => (int)db()->lastInsertId()], 'Customer added successfully.');
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
