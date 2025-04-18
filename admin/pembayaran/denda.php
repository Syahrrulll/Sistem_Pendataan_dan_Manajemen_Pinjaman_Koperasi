<?php
include '../../config.php';
session_start();
if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pinjaman = $_POST['id_pinjaman'];
    $jumlah_denda = $_POST['jumlah_denda'];
    $tanggal_bayar = $_POST['tanggal_bayar'];

    // Simpan denda ke tabel pembayaran
    $conn->query("INSERT INTO pembayaran (id_pinjaman, tanggal_bayar, jumlah_bayar, keterangan) 
                  VALUES ('$id_pinjaman', '$tanggal_bayar', '$jumlah_denda', 'Denda')");
    header('Location: index.php');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencatatan Denda Pembayaran</title>
    <link href="../../assets/css/laporan.css" rel="stylesheet">
</head>
<body>
    <h3>Pencatatan Denda Pembayaran</h3>
    <form method="post">
        <label>ID Pinjaman:</label>
        <input type="text" name="id_pinjaman" required><br>

        <label>Jumlah Denda:</label>
        <input type="number" name="jumlah_denda" required><br>

        <label>Tanggal Pembayaran:</label>
        <input type="date" name="tanggal_bayar" required><br>

        <button type="submit">Simpan Denda</button>
    </form>
</body>
</html>
