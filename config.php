<?php
// SQLite database path (file-based, portable). For MySQL, replace with PDO mysql dsn.
define('DB_PATH', __DIR__ . '/data/app.sqlite');

// Session & security
session_name('dr_catalog_sess');
session_start();

function db() {
    static $pdo = null;
    if ($pdo === null) {
        if (!file_exists(dirname(DB_PATH))) { mkdir(dirname(DB_PATH), 0777, true); }
        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('PRAGMA foreign_keys = ON;');
    }
    return $pdo;
}

function esc($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function csrf_token() {
    if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(32)); }
    return $_SESSION['csrf'];
}
function csrf_check() {
    if (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? null)) {
        http_response_code(400); exit('CSRF doğrulaması başarısız.');
    }
}

// Auth helpers
function require_login() {
    if (empty($_SESSION['user_id'])) {
        header('Location: /admin/login.php'); exit;
    }
}
?>
