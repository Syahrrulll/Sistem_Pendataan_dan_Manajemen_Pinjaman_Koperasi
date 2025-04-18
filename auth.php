<?php
session_start();
function checkLogin($role = null) {
    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit();
    }
    if ($role && $_SESSION['user']['role'] !== $role) {
        die("Akses ditolak.");
    }
}
?>
