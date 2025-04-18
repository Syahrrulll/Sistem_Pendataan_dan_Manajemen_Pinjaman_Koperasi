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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pinjaman</title>
    <link href="../../assets/css/laporan.css" rel="stylesheet">
</head>
<body>
    <h3>Laporan Pinjaman</h3>
    <p>Nama Anggota: <?= $pinjaman['id_anggota'] ?></p>
    <p>Jumlah Pinjaman: Rp <?= number_format($pinjaman['jumlah'], 0, ',', '.') ?></p>
    <p>Status Pinjaman: <?= $pinjaman['status'] ?></p>
    
    <!-- Menampilkan riwayat pembayaran -->
    <h4>Riwayat Pembayaran:</h4>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Pembayaran</th>
                <th>Jumlah Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sqlPembayaran = "SELECT * FROM pembayaran WHERE id_pinjaman = '$id'";
            $resultPembayaran = $conn->query($sqlPembayaran);
            $no = 1;
            while ($rowPembayaran = $resultPembayaran->fetch_assoc()) {
                echo "<tr>
                        <td>$no</td>
                        <td>{$rowPembayaran['tanggal_bayar']}</td>
                        <td>Rp " . number_format($rowPembayaran['jumlah_bayar'], 0, ',', '.') . "</td>
                      </tr>";
                $no++;
            }
            ?>
        </tbody>
    </table>
</body>
</html>
