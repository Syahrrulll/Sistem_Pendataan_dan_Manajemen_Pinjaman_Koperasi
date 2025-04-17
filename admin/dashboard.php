<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

if (!isLoggedIn() || $_SESSION['role'] != 'admin') {
    redirect('../login.php');
}

// Hitung jumlah anggota
$anggota = query("SELECT COUNT(*) as total FROM anggota")->fetch_assoc();

// Hitung pinjaman aktif
$pinjaman = query("SELECT COUNT(*) as total FROM pinjaman WHERE status='disetujui'")->fetch_assoc();

// Hitung total pembayaran bulan ini
$pembayaran = query("SELECT SUM(jumlah_bayar) as total FROM pembayaran 
                     WHERE MONTH(tanggal_bayar) = MONTH(CURRENT_DATE()) 
                     AND YEAR(tanggal_bayar) = YEAR(CURRENT_DATE())")->fetch_assoc();
?>

<?php include '../includes/header.php'; ?>

<div class="dashboard">
    <h2>Dashboard Admin</h2>
    <div class="stats">
        <div class="stat-card">
            <h3>Total Anggota</h3>
            <p><?= $anggota['total'] ?></p>
        </div>
        <div class="stat-card">
            <h3>Pinjaman Aktif</h3>
            <p><?= $pinjaman['total'] ?></p>
        </div>
        <div class="stat-card">
            <h3>Pembayaran Bulan Ini</h3>
            <p><?= formatRupiah($pembayaran['total'] ?? 0) ?></p>
        </div>
    </div>
    
    <div class="recent-activity">
        <h3>Aktivitas Terakhir</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Anggota</th>
                    <th>Jumlah Pinjaman</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = query("SELECT p.*, a.nama FROM pinjaman p 
                               JOIN anggota a ON p.id_anggota = a.id_anggota 
                               ORDER BY p.tanggal_pinjam DESC LIMIT 5");
                $no = 1;
                while ($row = $query->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['nama'] ?></td>
                    <td><?= formatRupiah($row['jumlah']) ?></td>
                    <td><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?></td>
                    <td><?= ucfirst($row['status']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>