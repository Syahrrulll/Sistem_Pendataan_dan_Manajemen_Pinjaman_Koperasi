<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';

if (!is_logged_in() || $_SESSION['role'] != 'admin') {
    redirect('../../login.php');
}

// Tambah anggota
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah'])) {
    $id = generate_id('AGT', 'anggota', 'id');
    $nama = clean_input($_POST['nama']);
    $alamat = clean_input($_POST['alamat']);
    $telepon = clean_input($_POST['telepon']);
    $email = clean_input($_POST['email']);
    $pekerjaan = clean_input($_POST['pekerjaan']);
    $status = clean_input($_POST['status']);
    
    $query = query("INSERT INTO anggota VALUES (
        '$id', '$nama', '$alamat', '$telepon', '$email', '$pekerjaan', '$status', NOW()
    )");
    
    if ($query) {
        set_flash_message('success', 'Anggota berhasil ditambahkan!');
        redirect('index.php');
    } else {
        set_flash_message('danger', 'Gagal menambahkan anggota!');
    }
}

// Hapus anggota
if (isset($_GET['hapus'])) {
    $id = clean_input($_GET['hapus']);
    $query = query("DELETE FROM anggota WHERE id='$id'");
    
    if ($query) {
        set_flash_message('success', 'Anggota berhasil dihapus!');
    } else {
        set_flash_message('danger', 'Gagal menghapus anggota!');
    }
    redirect('index.php');
}

// Ambil data anggota
$anggota = query("SELECT * FROM anggota ORDER BY nama ASC");

$title = "Manajemen Anggota";
$css = ["admin.css"];
include '../../includes/admin-head.php';
?>

<body class="admin-dashboard">
    <div class="dashboard-container">
        <?php include '../../includes/admin-sidebar.php'; ?>
        
        <div class="main-content">
            <?php include '../../includes/admin-header.php'; ?>
            
            <div class="page-content">
                <div class="container">
                    <div class="page-header">
                        <h1 class="page-title">Manajemen Anggota</h1>
                        <div class="breadcrumb">
                            <a href="../dashboard.php">Home</a> / <span>Anggota</span>
                        </div>
                    </div>
                    
                    <?php display_flash_message(); ?>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card card-hover">
                                <div class="card-header">
                                    <h3>Tambah Anggota Baru</h3>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <div class="form-group">
                                            <label class="form-label">Nama Lengkap</label>
                                            <input type="text" name="nama" class="form-control" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="form-label">Alamat</label>
                                            <textarea name="alamat" class="form-control" rows="3" required></textarea>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="form-label">Telepon</label>
                                            <input type="text" name="telepon" class="form-control" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="form-label">Pekerjaan</label>
                                            <input type="text" name="pekerjaan" class="form-control" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-control" required>
                                                <option value="aktif">Aktif</option>
                                                <option value="nonaktif">Nonaktif</option>
                                            </select>
                                        </div>
                                        
                                        <button type="submit" name="tambah" class="btn btn-primary btn-block">
                                            <i class="fas fa-save mr-2"></i> Simpan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="card card-hover">
                                <div class="card-header">
                                    <h3>Daftar Anggota</h3>
                                    <div class="card-actions">
                                        <input type="text" class="form-control" placeholder="Cari anggota...">
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nama</th>
                                                    <th>Telepon</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($row = $anggota->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= $row['id'] ?></td>
                                                    <td><?= $row['nama'] ?></td>
                                                    <td><?= $row['telepon'] ?></td>
                                                    <td>
                                                        <span class="badge badge-<?= $row['status'] == 'aktif' ? 'success' : 'danger' ?>">
                                                            <?= ucfirst($row['status']) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="index.php?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus anggota ini?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php 
    $js = ["admin.js"];
    include '../../includes/admin-footer.php'; 
    ?>
</body>
</html>