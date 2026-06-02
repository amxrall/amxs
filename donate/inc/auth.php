<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_login']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "/index.php");
        exit;
    }
}
?>