<?php
session_start();

// Validasi session
if (isset($_SESSION['user_id'])) {
    // Hapus semua variabel session
    session_unset();

    // Hapus cookie session
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(), 
            '', 
            time() - 42000,
            $params["path"], 
            $params["domain"],
            $params["secure"], 
            $params["httponly"]
        );
    }

    // Hancurkan session
    session_destroy();
}

// Nonaktifkan caching
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// Redirect ke halaman login
header("Location: user_login_register.php?logout=success");
exit();
?>