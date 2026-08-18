<?php
/**
 * App-wide configuration and session bootstrap.
 * Included first by config/db.php and includes/auth.php.
 */

define('APP_ROOT', dirname(__DIR__));
define('SESSION_TIMEOUT', 1800); // 30 minutes of inactivity

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
