<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';

/** True if a valid, non-expired session exists. Expires+clears the session on timeout. */
function is_logged_in(): bool {
    if (empty($_SESSION['user_id'])) {
        return false;
    }
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
        logout_user();
        return false;
    }
    $_SESSION['last_activity'] = time();
    return true;
}

/** Call at the top of every protected page (already wired into components/header.php). */
function require_login(): void {
    if (is_logged_in()) {
        return;
    }
    if (is_ajax_request()) {
        json_error('Session expired. Please log in again.', 401);
    }
    header('Location: login.php');
    exit;
}

/** Call at the top of login.php so an already-authenticated user skips it. */
function require_guest(): void {
    if (is_logged_in()) {
        header('Location: index.php');
        exit;
    }
}

/** Cached per-request lookup of the signed-in user (password hash stripped). */
function current_user(): ?array {
    static $user = null;
    static $loaded = false;
    if ($loaded) {
        return $user;
    }
    $loaded = true;
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    $stmt = db()->prepare('SELECT id, name, username, email, role, avatar_color, can_manage_customers, can_manage_quotations, can_manage_invoices, can_manage_payments, can_view_reports, can_manage_users, status, created_at FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $row = $stmt->fetch();
    $user = $row ?: null;
    return $user;
}

/**
 * Verifies credentials and starts a session on success.
 * @return array{0: bool, 1: string|array} [success, errorMessage|userRow]
 */
function attempt_login(string $identifier, string $password): array {
    if ($identifier === '' || $password === '') {
        return [false, 'Please enter both a username/email and password.'];
    }
    $stmt = db()->prepare('SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1');
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return [false, 'Invalid username/email or password.'];
    }
    if ($user['status'] !== 'active') {
        return [false, 'Your account is inactive. Please contact the administrator.'];
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['last_activity'] = time();
    unset($user['password_hash']);
    return [true, $user];
}

function logout_user(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

function user_has_permission(string $key): bool {
    $u = current_user();
    return $u ? (bool)($u[$key] ?? false) : false;
}
