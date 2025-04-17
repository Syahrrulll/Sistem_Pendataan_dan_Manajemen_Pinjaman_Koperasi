<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

if (!isLoggedIn() || $_SESSION['role'] != 'anggota') {
    redirect('../login.php');
}

$id_anggota = $_SESSION['user_id'];

// Proses pengajuan pinjaman
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pinjaman = generateKode('PJM', 'pinjaman', 'id_pinjaman');
    $jumlah = escape($_POST['jumlah']);
    $bunga = 1; // Bunga 1% per bulan
    $tenor = escape($_POST['tenor']);
    $tujuan = escape($_POST['tujuan']);
    
    $query = query("INSERT INTO pinjaman VALUES (
        '$id_pinjaman', '$id_anggota', '$jumlah', '$bunga', '$tenor', '$tujuan',
        'menunggu', NOW()
    )");
    
    if ($query) {
        $_SESSION['success'] = "Pengajuan pinjaman berhasil dikirim!";
        redirect('pinjaman.php');
    } else {
        $_SESSION['error'] = "Gagal mengajukan pinjaman!";
    }
}

$pinjaman = query("SELECT p.* FROM pinjaman p 
                  WHERE p.id_anggota='$id_anggota' 
                  ORDER BY p.tanggal_pinjam DESC");
?>

<?php include '../includes/header.php'; ?>

<div class="content">
    <h2>Pengajuan Pinjaman</h2>
    
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <div class="card">
        <h3>Form Pengajuan Pinjaman</h3>
        <form method="POST">
            <div class="form-group">
                <label>Jumlah Pinjaman</label>
                <input type="number" name="jumlah" min="100000" required>
            </div>
            <div class="form-group">
                <label>Tenor (bulan)</label>
                <select name="tenor" required>
                    <option value="6">6 Bulan</option>
                    <option value="12">12 Bulan</option>
                    <option value="24">24 Bulan</option>
                </select>
            </div>
            <div class="form-group">
                <label>Tujuan Pinjaman</label>
                <textarea name="tujuan" required></textarea>
            </div>
            <button type="submit" class="btn">Ajukan Pinjaman</button>
        </form>
    </div>
    
    <div class="card">
        <h3>Riwayat Pinjaman</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Pinjaman</th>
                    <th>Jumlah</th>
                    <th>Tenor</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while($row = $pinjaman->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['id_pinjaman'] ?></td>
                    <td><?= formatRupiah($row['jumlah']) ?></td>
                    <td><?= $row['tenor'] ?> bulan</td>
                    <td><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?></td>
                    <td><?= ucfirst($row['status']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>