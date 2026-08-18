<?php
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');
$action = $_POST['action'] ?? '';

if ($action === 'login') {
    $identifier = clean($_POST['identifier'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    [$ok, $result] = attempt_login($identifier, $password);
    if (!$ok) {
        json_error($result, 401);
    }
    json_success(['user' => ['name' => $result['name'], 'role' => $result['role']]], 'Signed in successfully.');
}

if ($action === 'logout') {
    // No CSRF check here — logging a session out is safe to allow even if the
    // token is stale (e.g. it already expired), and blocking it would strand the user.
    logout_user();
    json_success([], 'Logged out.');
}

json_error('Unknown action.', 400);
