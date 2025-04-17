<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/functions.php';

if (!isLoggedIn() || $_SESSION['role'] != 'admin') {
    redirect('../login.php');
}

// Tambah anggota
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah'])) {
    $id_anggota = generateKode('AGT', 'anggota', 'id_anggota');
    $nama = escape($_POST['nama']);
    $alamat = escape($_POST['alamat']);
    $telepon = escape($_POST['telepon']);
    $pekerjaan = escape($_POST['pekerjaan']);
    $status = escape($_POST['status']);
    
    $query = query("INSERT INTO anggota VALUES (
        '$id_anggota', '$nama', '$alamat', '$telepon', '$pekerjaan', '$status', NOW()
    )");
    
    if ($query) {
        $_SESSION['success'] = "Anggota berhasil ditambahkan!";
        redirect('index.php');
    } else {
        $_SESSION['error'] = "Gagal menambahkan anggota!";
    }
}

// Hapus anggota
if (isset($_GET['hapus'])) {
    $id = escape($_GET['hapus']);
    $query = query("DELETE FROM anggota WHERE id_anggota='$id'");
    
    if ($query) {
        $_SESSION['success'] = "Anggota berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Gagal menghapus anggota!";
    }
    redirect('index.php');
}

$anggota = query("SELECT * FROM anggota ORDER BY nama ASC");
?>

<?php include '../../includes/header.php'; ?>

<div class="content">
    <h2>Manajemen Anggota</h2>
    
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <div class="card">
        <h3>Tambah Anggota Baru</h3>
        <form method="POST">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" required>
            </div>
            <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat" required></textarea>
            </div>
            <div class="form-group">
                <label>Telepon</label>
                <input type="text" name="telepon" required>
            </div>
            <div class="form-group">
                <label>Pekerjaan</label>
                <input type="text" name="pekerjaan" required>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>
            <button type="submit" name="tambah" class="btn">Simpan</button>
        </form>
    </div>
    
    <div class="card">
        <h3>Daftar Anggota</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Anggota</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Telepon</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while($row = $anggota->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['id_anggota'] ?></td>
                    <td><?= $row['nama'] ?></td>
                    <td><?= $row['alamat'] ?></td>
                    <td><?= $row['telepon'] ?></td>
                    <td><?= ucfirst($row['status']) ?></td>
                    <td>
                        <a href="edit.php?id=<?= $row['id_anggota'] ?>" class="btn btn-edit">Edit</a>
                        <a href="index.php?hapus=<?= $row['id_anggota'] ?>" class="btn btn-danger" onclick="return confirm('Yakin hapus?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>