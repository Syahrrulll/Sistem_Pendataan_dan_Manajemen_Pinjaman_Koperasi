<?php
require_once 'config/functions.php';

if (is_logged_in()) {
    if ($_SESSION['role'] == 'admin') {
        redirect('admin/dashboard.php');
    } else {
        redirect('member/dashboard.php');
    }
} else {
    redirect('login.php');
}
?>