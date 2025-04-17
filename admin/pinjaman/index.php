<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/functions.php';

if (!isLoggedIn() || $_SESSION['role'] != 'admin') {
    redirect('../login.php');
}

// Proses persetujuan pinjaman
if (isset($_GET['setuju'])) {
    $id = escape($_GET['setuju']);
    $query = query("UPDATE pinjaman SET status='disetujui' WHERE id_pinjaman='$id'");
    
    if ($query) {
        // Buat jadwal cicilan
        $pinjaman = query("SELECT * FROM pinjaman WHERE id_pinjaman='$id'")->fetch_assoc();
        $jumlah_cicilan = $pinjaman['tenor'];
        $total_pinjaman = $pinjaman['jumlah'] + ($pinjaman['jumlah'] * $pinjaman['bunga'] / 100);
        $jumlah_per_cicilan = $total_pinjaman / $jumlah_cicilan;
        
        $tanggal = date('Y-m-d');
        for ($i = 1; $i <= $jumlah_cicilan; $i++) {
            $tanggal = date('Y-m-d', strtotime("+$i month", strtotime($tanggal)));
            query("INSERT INTO cicilan VALUES (
                NULL, '$id', '$tanggal', '$jumlah_per_cicilan', 'belum lunas'
            )");
        }
        
        $_SESSION['success'] = "Pinjaman disetujui dan jadwal cicilan dibuat!";
    } else {
        $_SESSION['error'] = "Gagal menyetujui pinjaman!";
    }
    redirect('index.php');
}

// Proses penolakan pinjaman
if (isset($_GET['tolak'])) {
    $id = escape($_GET['tolak']);
    $query = query("UPDATE pinjaman SET status='ditolak' WHERE id_pinjaman='$id'");
    
    if ($query) {
        $_SESSION['success'] = "Pinjaman ditolak!";
    } else {
        $_SESSION['error'] = "Gagal menolak pinjaman!";
    }
    redirect('index.php');
}

$pinjaman = query("SELECT p.*, a.nama FROM pinjaman p 
                  JOIN anggota a ON p.id_anggota = a.id_anggota 
                  ORDER BY p.tanggal_pinjam DESC");
?>

<?php include '../../includes/header.php'; ?>

<div class="content">
    <h2>Manajemen Pinjaman</h2>
    
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <div class="card">
        <h3>Daftar Pengajuan Pinjaman</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Pinjaman</th>
                    <th>Nama Anggota</th>
                    <th>Jumlah</th>
                    <th>Bunga</th>
                    <th>Tenor</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while($row = $pinjaman->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['id_pinjaman'] ?></td>
                    <td><?= $row['nama'] ?></td>
                    <td><?= formatRupiah($row['jumlah']) ?></td>
                    <td><?= $row['bunga'] ?>%</td>
                    <td><?= $row['tenor'] ?> bulan</td>
                    <td><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?></td>
                    <td><?= ucfirst($row['status']) ?></td>
                    <td>
                        <?php if($row['status'] == 'menunggu'): ?>
                            <a href="index.php?setuju=<?= $row['id_pinjaman'] ?>" class="btn btn-success">Setujui</a>
                            <a href="index.php?tolak=<?= $row['id_pinjaman'] ?>" class="btn btn-danger">Tolak</a>
                        <?php endif; ?>
                        <a href="detail.php?id=<?= $row['id_pinjaman'] ?>" class="btn">Detail</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>