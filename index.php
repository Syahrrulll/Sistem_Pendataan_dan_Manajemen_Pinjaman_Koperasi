<?php
session_start();
require_once 'config/database.php';
require_once 'config/functions.php';

if (isLoggedIn()) {
    if ($_SESSION['role'] == 'admin') {
        redirect('admin/dashboard.php');
    } else {
        redirect('anggota/dashboard.php');
    }
} else {
    redirect('login.php');
}
?>