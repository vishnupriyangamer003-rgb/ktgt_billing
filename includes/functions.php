<?php
/** Small shared helpers used across ajax/ handlers and pages. */

function json_response($data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function json_error(string $message, int $status = 400): void {
    json_response(['success' => false, 'message' => $message], $status);
}

function json_success($data = [], string $message = ''): void {
    json_response(['success' => true, 'message' => $message, 'data' => $data]);
}

function clean(?string $v): string {
    return trim($v ?? '');
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Call at the top of every state-changing AJAX handler (add/edit/delete/save). */
function verify_csrf(): void {
    $token = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!$token || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        json_error('Your session has expired. Please refresh the page and try again.', 419);
    }
}

function is_ajax_request(): bool {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}
