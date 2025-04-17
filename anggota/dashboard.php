<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

if (!isLoggedIn() || $_SESSION['role'] != 'anggota') {
    redirect('../login.php');
}

// Dapatkan ID anggota dari session
$id_anggota = $_SESSION['user_id'];

// Data pinjaman aktif
$pinjaman_aktif = query("SELECT COUNT(*) as total FROM pinjaman 
                        WHERE id_anggota='$id_anggota' AND status='disetujui'")->fetch_assoc();

// Total tunggakan
$tunggakan = query("SELECT SUM(c.jumlah) as total FROM cicilan c
                   JOIN pinjaman p ON c.id_pinjaman = p.id_pinjaman
                   WHERE p.id_anggota='$id_anggota' AND c.status='belum lunas' 
                   AND c.tanggal < CURDATE()")->fetch_assoc();

// Riwayat pinjaman terakhir
$riwayat = query("SELECT * FROM pinjaman WHERE id_anggota='$id_anggota' 
                 ORDER BY tanggal_pinjam DESC LIMIT 1")->fetch_assoc();
?>

<?php include '../includes/header.php'; ?>

<div class="dashboard">
    <h2>Dashboard Anggota</h2>
    <p>Selamat datang, <?= $_SESSION['nama'] ?></p>
    
    <div class="stats">
        <div class="stat-card">
            <h3>Pinjaman Aktif</h3>
            <p><?= $pinjaman_aktif['total'] ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Tunggakan</h3>
            <p><?= formatRupiah($tunggakan['total'] ?? 0) ?></p>
        </div>
        <div class="stat-card">
            <h3>Pinjaman Terakhir</h3>
            <p><?= $riwayat ? formatRupiah($riwayat['jumlah']) : '-' ?></p>
        </div>
    </div>
    
    <div class="actions">
        <a href="pinjaman.php" class="btn">Ajukan Pinjaman</a>
        <a href="pembayaran.php" class="btn">Lihat Pembayaran</a>
    </div>
    
    <div class="recent-activity">
        <h3>Pembayaran Mendatang</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Jatuh Tempo</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = query("SELECT c.* FROM cicilan c
                              JOIN pinjaman p ON c.id_pinjaman = p.id_pinjaman
                              WHERE p.id_anggota='$id_anggota' AND c.status='belum lunas'
                              ORDER BY c.tanggal ASC LIMIT 5");
                $no = 1;
                while ($row = $query->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                    <td><?= formatRupiah($row['jumlah']) ?></td>
                    <td><?= ucfirst($row['status']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>