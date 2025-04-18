<?php
include '../../config.php';
session_start();
if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

$id = $_GET['id'];
$sql = "SELECT * FROM pinjaman WHERE id = '$id'";
$result = $conn->query($sql);
$pinjaman = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $conn->query("UPDATE pinjaman SET status='$status' WHERE id='$id'");

    // Jika disetujui, hitung jadwal cicilan
    if ($status === 'disetujui') {
        $jumlah = $pinjaman['jumlah'];
        $bunga = $pinjaman['bunga'];
        $tenor = $pinjaman['tenor'];
        $totalPinjaman = $jumlah + ($jumlah * $bunga / 100);
        $cicilanPerBulan = $totalPinjaman / $tenor;

        // Simpan jadwal cicilan
        for ($i = 1; $i <= $tenor; $i++) {
            $conn->query("INSERT INTO pembayaran (id_pinjaman, tanggal_bayar, jumlah_bayar) 
                          VALUES ('$id', DATE_ADD(NOW(), INTERVAL $i MONTH), $cicilanPerBulan)");
        }
    }
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan Pinjaman</title>
    <link href="../../assets/css/laporan.css" rel="stylesheet">
</head>
<body>
    <h3>Persetujuan Pinjaman</h3>
    <p>Nama Anggota: <?= $pinjaman['id_anggota'] ?></p>
    <p>Jumlah Pinjaman: Rp <?= number_format($pinjaman['jumlah'], 0, ',', '.') ?></p>
    <form method="post">
        <label for="status">Status:</label>
        <select name="status">
            <option value="menunggu" <?= $pinjaman['status'] === 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
            <option value="disetujui" <?= $pinjaman['status'] === 'disetujui' ? 'selected' : '' ?>>Disetujui</option>
            <option value="ditolak" <?= $pinjaman['status'] === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
        </select>
        <button type="submit">Simpan</button>
    </form>
</body>
</html>
