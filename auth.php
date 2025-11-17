<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function current_user() {
    return $_SESSION["user"] ?? null;
}

function login_user($kh) {
    $_SESSION["user"] = [
        "MaKH"  => $kh["MaKH"],
        "TenKH" => $kh["TenKH"],
        "Email" => $kh["Email"]
    ];
}

function logout_user() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

function require_login() {
    if (!current_user()) {
        header("Location: login.php");
        exit;
    }
}
?>