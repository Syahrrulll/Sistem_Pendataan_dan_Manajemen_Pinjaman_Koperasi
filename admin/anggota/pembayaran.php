<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

if (!isLoggedIn() || $_SESSION['role'] != 'anggota') {
    redirect('../login.php');
}

$id_anggota = $_SESSION['user_id'];

// Dapatkan pinjaman aktif anggota
$pinjaman_aktif = query("SELECT * FROM pinjaman 
                        WHERE id_anggota='$id_anggota' AND status='disetujui'");

// Dapatkan riwayat pembayaran
$pembayaran = query("SELECT p.*, c.tanggal as jatuh_tempo 
                    FROM pembayaran p
                    JOIN cicilan c ON p.id_cicilan = c.id_cicilan
                    JOIN pinjaman pm ON p.id_pinjaman = pm.id_pinjaman
                    WHERE pm.id_anggota='$id_anggota'
                    ORDER BY p.tanggal_bayar DESC");
?>

<?php include '../includes/header.php'; ?>

<div class="content">
    <h2>Manajemen Pembayaran</h2>
    
    <div class="card">
        <h3>Pinjaman Aktif</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Pinjaman</th>
                    <th>Jumlah Pinjaman</th>
                    <th>Bunga</th>
                    <th>Tenor</th>
                    <th>Total Pinjaman</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while($row = $pinjaman_aktif->fetch_assoc()): 
                    $total_pinjaman = $row['jumlah'] + ($row['jumlah'] * $row['bunga'] / 100);
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['id_pinjaman'] ?></td>
                    <td><?= formatRupiah($row['jumlah']) ?></td>
                    <td><?= $row['bunga'] ?>%</td>
                    <td><?= $row['tenor'] ?> bulan</td>
                    <td><?= formatRupiah($total_pinjaman) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    
    <div class="card">
        <h3>Jadwal Cicilan</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Pinjaman</th>
                    <th>Tanggal Jatuh Tempo</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $query = query("SELECT c.*, p.id_pinjaman 
                              FROM cicilan c
                              JOIN pinjaman p ON c.id_pinjaman = p.id_pinjaman
                              WHERE p.id_anggota='$id_anggota'
                              ORDER BY c.tanggal ASC");
                $no = 1; 
                while($row = $query->fetch_assoc()): 
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['id_pinjaman'] ?></td>
                    <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                    <td><?= formatRupiah($row['jumlah']) ?></td>
                    <td><?= ucfirst($row['status']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    
    <div class="card">
        <h3>Riwayat Pembayaran</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Pembayaran</th>
                    <th>ID Pinjaman</th>
                    <th>Tanggal Bayar</th>
                    <th>Jatuh Tempo</th>
                    <th>Jumlah Bayar</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while($row = $pembayaran->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['id_pembayaran'] ?></td>
                    <td><?= $row['id_pinjaman'] ?></td>
                    <td><?= date('d/m/Y', strtotime($row['tanggal_bayar'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($row['jatuh_tempo'])) ?></td>
                    <td><?= formatRupiah($row['jumlah_bayar']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>