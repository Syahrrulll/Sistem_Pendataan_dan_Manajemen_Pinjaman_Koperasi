<?php
require_once 'database.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function hitungBunga($pokok, $bunga, $tenor) {
    return ($pokok * $bunga / 100) * $tenor;
}

function generateKode($prefix, $table, $field) {
    $query = query("SELECT MAX($field) as max_code FROM $table");
    $data = $query->fetch_assoc();
    $max_code = $data['max_code'];
    
    $urutan = (int) substr($max_code, strlen($prefix));
    $urutan++;
    
    return $prefix . sprintf("%05s", $urutan);
}

function checkRole($role) {
    if ($_SESSION['role'] != $role) {
        redirect('../index.php');
    }
}
?>