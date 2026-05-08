<?php
/**
 * Admin Session Authentication Helpers
 */
session_start();

function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /admin/login.php");
        exit();
    }
}

function setAdminSession($admin_id, $email) {
    $_SESSION['admin_id'] = $admin_id;
    $_SESSION['admin_email'] = $email;
}

function logout() {
    session_unset();
    session_destroy();
    header("Location: /admin/login.php");
    exit();
}
?>
