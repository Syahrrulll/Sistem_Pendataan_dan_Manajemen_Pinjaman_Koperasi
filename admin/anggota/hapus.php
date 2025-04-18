<?php
include '../../config.php';
session_start();
if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

$id = $_GET['id'];

$conn->query("DELETE FROM anggota WHERE id = '$id'");
header('Location: index.php');
