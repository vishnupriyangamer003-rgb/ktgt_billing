<?php
/**
 * Generic AJAX handler for all 7 Masters screens (project-types.php, services.php,
 * taxes.php, followup-types.php, servers.php, renewal-types.php, paid-status.php).
 * One reusable endpoint instead of seven near-identical files — the "master" key
 * (whitelisted below) selects which table is touched.
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
];

$masterKey = $_REQUEST['master'] ?? '';
if (!isset(MASTER_CONFIG[$masterKey])) {
    json_error('Unknown master type.', 400);
}
$cfg = MASTER_CONFIG[$masterKey];
$table = $cfg['table'];
$action = $_REQUEST['action'] ?? 'list';

if ($action === 'list') {
    $rows = db()->query("SELECT * FROM {$table} ORDER BY id ASC")->fetchAll();
    json_success($rows);
}

if ($action === 'add') {
    verify_csrf();

    $name = clean($_POST['name'] ?? '');
    if ($name === '') {
        json_error('Please enter a name.');
    }
    if (mb_strlen($name) > 150) {
        json_error('Name is too long (max 150 characters).');
    }

    $dupe = db()->prepare("SELECT id FROM {$table} WHERE LOWER(name) = LOWER(?)");
    $dupe->execute([$name]);
    if ($dupe->fetch()) {
        json_error('"' . $name . '" already exists.');
    }

    if (in_array('rate', $cfg['fields'], true)) {
        $rate = (float)($_POST['rate'] ?? 0);
        if ($rate < 0) json_error('Rate cannot be negative.');
        $stmt = db()->prepare("INSERT INTO {$table} (name, rate) VALUES (?, ?)");
        $stmt->execute([$name, $rate]);
    } elseif (in_array('percent', $cfg['fields'], true)) {
        if (!isset($_POST['percent']) || $_POST['percent'] === '') {
            json_error('Please enter a tax percentage.');
        }
        $percent = (float)$_POST['percent'];
        if ($percent < 0 || $percent > 100) {
            json_error('Tax percentage must be between 0 and 100.');
        }
        $stmt = db()->prepare("INSERT INTO {$table} (name, percent) VALUES (?, ?)");
        $stmt->execute([$name, $percent]);
    } else {
        $stmt = db()->prepare("INSERT INTO {$table} (name) VALUES (?)");
        $stmt->execute([$name]);
    }

    json_success(['id' => (int)db()->lastInsertId()], 'Added successfully.');
}

if ($action === 'delete') {
    verify_csrf();
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
        json_error('Invalid record.');
    }
    $stmt = db()->prepare("DELETE FROM {$table} WHERE id = ?");
    $stmt->execute([$id]);
    json_success([], 'Deleted successfully.');
}

json_error('Unknown action.', 400);
