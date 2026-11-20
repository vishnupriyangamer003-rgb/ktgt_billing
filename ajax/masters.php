<?php
/**
 * Generic AJAX handler for all 8 Masters screens (project-types.php, services.php,
 * taxes.php, followup-types.php, servers.php, renewal-types.php, paid-status.php,
 * domain-controls.php).
 * One reusable endpoint instead of seven near-identical files — the "master" key
 * (whitelisted below) selects which table is touched.
 *
 * Delete is a soft delete: status flips to 'deleted' (deleted_at/deleted_by
 * stamped) rather than removing the row, so name-uniqueness checks and the
 * list query are always scoped to status = 'active'.
 */
require_once __DIR__ . '/../includes/auth.php';
require_login();
header('Content-Type: application/json');

const MASTER_CONFIG = [
    'project_types'     => ['table' => 'master_project_types',     'fields' => ['name']],
    'services'          => ['table' => 'master_services',          'fields' => ['name', 'rate']],
    'taxes'             => ['table' => 'master_taxes',              'fields' => ['name', 'percent']],
    'followup_types'    => ['table' => 'master_followup_types',    'fields' => ['name']],
    'servers'           => ['table' => 'master_servers',            'fields' => ['name']],
    'renewal_types'     => ['table' => 'master_renewal_types',     'fields' => ['name']],
    'paid_status_types' => ['table' => 'master_paid_status_types', 'fields' => ['name']],
    'domain_controls'   => ['table' => 'master_domain_controls',   'fields' => ['name']],
];

$masterKey = $_REQUEST['master'] ?? '';
if (!isset(MASTER_CONFIG[$masterKey])) {
    json_error('Unknown master type.', 400);
}
$cfg = MASTER_CONFIG[$masterKey];
$table = $cfg['table'];
$action = $_REQUEST['action'] ?? 'list';
$userId = current_user()['id'] ?? null;

/** Validates name (+ rate/percent if this master has them) from $_POST. Exits on error. */
function validate_master_input(array $cfg): array {
    $name = clean($_POST['name'] ?? '');
    if ($name === '') {
        json_error('Please enter a name.');
    }
    if (mb_strlen($name) > 150) {
        json_error('Name is too long (max 150 characters).');
    }

    $extra = [];
    if (in_array('rate', $cfg['fields'], true)) {
        $rate = (float)($_POST['rate'] ?? 0);
        if ($rate < 0) json_error('Rate cannot be negative.');
        $extra['rate'] = $rate;
    } elseif (in_array('percent', $cfg['fields'], true)) {
        if (!isset($_POST['percent']) || $_POST['percent'] === '') {
            json_error('Please enter a tax percentage.');
        }
        $percent = (float)$_POST['percent'];
        if ($percent < 0 || $percent > 100) {
            json_error('Tax percentage must be between 0 and 100.');
        }
        $extra['percent'] = $percent;
    }
    return [$name, $extra];
}

if ($action === 'list') {
    $stmt = db()->prepare("SELECT * FROM {$table} WHERE status = 'active' ORDER BY id ASC");
    $stmt->execute();
    json_success($stmt->fetchAll());
}

if ($action === 'add') {
    verify_csrf();
    [$name, $extra] = validate_master_input($cfg);

    $dupe = db()->prepare("SELECT id FROM {$table} WHERE status = 'active' AND LOWER(name) = LOWER(?)");
    $dupe->execute([$name]);
    if ($dupe->fetch()) {
        json_error('"' . $name . '" already exists.');
    }

    if (isset($extra['rate'])) {
        $stmt = db()->prepare("INSERT INTO {$table} (name, rate, created_by) VALUES (?, ?, ?)");
        $stmt->execute([$name, $extra['rate'], $userId]);
    } elseif (isset($extra['percent'])) {
        $stmt = db()->prepare("INSERT INTO {$table} (name, percent, created_by) VALUES (?, ?, ?)");
        $stmt->execute([$name, $extra['percent'], $userId]);
    } else {
        $stmt = db()->prepare("INSERT INTO {$table} (name, created_by) VALUES (?, ?)");
        $stmt->execute([$name, $userId]);
    }

    json_success(['id' => (int)db()->lastInsertId()], 'Added successfully.');
}

if ($action === 'edit') {
    verify_csrf();
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
        json_error('Invalid record.');
    }
    [$name, $extra] = validate_master_input($cfg);

    $existing = db()->prepare("SELECT id FROM {$table} WHERE id = ? AND status = 'active'");
    $existing->execute([$id]);
    if (!$existing->fetch()) {
        json_error('This record no longer exists.', 404);
    }

    $dupe = db()->prepare("SELECT id FROM {$table} WHERE status = 'active' AND LOWER(name) = LOWER(?) AND id != ?");
    $dupe->execute([$name, $id]);
    if ($dupe->fetch()) {
        json_error('"' . $name . '" already exists.');
    }

    if (isset($extra['rate'])) {
        $stmt = db()->prepare("UPDATE {$table} SET name = ?, rate = ?, updated_at = NOW(), updated_by = ? WHERE id = ?");
        $stmt->execute([$name, $extra['rate'], $userId, $id]);
    } elseif (isset($extra['percent'])) {
        $stmt = db()->prepare("UPDATE {$table} SET name = ?, percent = ?, updated_at = NOW(), updated_by = ? WHERE id = ?");
        $stmt->execute([$name, $extra['percent'], $userId, $id]);
    } else {
        $stmt = db()->prepare("UPDATE {$table} SET name = ?, updated_at = NOW(), updated_by = ? WHERE id = ?");
        $stmt->execute([$name, $userId, $id]);
    }

    json_success([], 'Updated successfully.');
}

if ($action === 'delete') {
    verify_csrf();
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
        json_error('Invalid record.');
    }
    $stmt = db()->prepare("UPDATE {$table} SET status = 'deleted', deleted_at = NOW(), deleted_by = ? WHERE id = ? AND status = 'active'");
    $stmt->execute([$userId, $id]);
    json_success([], 'Deleted successfully.');
}

json_error('Unknown action.', 400);
