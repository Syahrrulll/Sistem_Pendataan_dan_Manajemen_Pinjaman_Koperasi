<?php
require_once '../config/database.php';
require_once '../config/functions.php';

if (!is_logged_in()) {
    redirect('../login.php');
}

if ($_SESSION['role'] != 'member') {
    redirect('../unauthorized.php');
}

$member_id = $_SESSION['user_id'];

// Data pinjaman aktif
$pinjaman_aktif = query("SELECT COUNT(*) as total FROM pinjaman WHERE id_anggota='$member_id' AND status='disetujui'")->fetch_assoc()['total'];

// Total tunggakan
$tunggakan = query("
    SELECT SUM(c.jumlah) as total 
    FROM cicilan c
    JOIN pinjaman p ON c.id_pinjaman = p.id
    WHERE p.id_anggota='$member_id' AND c.status='belum lunas' 
    AND c.tanggal < CURRENT_DATE()
")->fetch_assoc()['total'];

// Pinjaman terakhir
$pinjaman_terakhir = query("
    SELECT * FROM pinjaman 
    WHERE id_anggota='$member_id' 
    ORDER BY tanggal_pinjam DESC 
    LIMIT 1
")->fetch_assoc();

$title = "Dashboard Anggota";
$css = ["member.css", "animations.css"];
include '../includes/member-head.php';
?>

<body class="member-dashboard">
    <div class="dashboard-container">
        <?php include '../includes/member-sidebar.php'; ?>
        
        <div class="main-content">
            <?php include '../includes/member-header.php'; ?>
            
            <div class="page-content">
                <div class="container">
                    <div class="page-header">
                        <h1 class="page-title">Dashboard Anggota</h1>
                        <div class="breadcrumb">
                            <a href="#">Home</a> / <span>Dashboard</span>
                        </div>
                    </div>
                    
                    <?php display_flash_message(); ?>
                    
                    <div class="welcome-banner">
                        <div class="banner-content">
                            <h2>Selamat datang, <?= $_SESSION['nama'] ?>!</h2>
                            <p>Berikut ringkasan informasi pinjaman Anda</p>
                        </div>
                        <div class="banner-actions">
                            <a href="ajukan.php" class="btn btn-primary">
                                <i class="fas fa-plus mr-2"></i> Ajukan Pinjaman Baru
                            </a>
                        </div>
                    </div>
                    
                    <!-- Stats Grid -->
                    <div class="stats-grid">
                        <div class="stat-card card-hover">
                            <div class="stat-icon bg-primary">
                                <i class="fas fa-hand-holding-usd"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Pinjaman Aktif</h3>
                                <span class="stat-value"><?= $pinjaman_aktif ?></span>
                                <span class="stat-change">
                                    <i class="fas fa-info-circle"></i> Total pinjaman aktif
                                </span>
                            </div>
                        </div>
                        
                        <div class="stat-card card-hover">
                            <div class="stat-icon bg-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Tunggakan</h3>
                                <span class="stat-value"><?= format_rupiah($tunggakan) ?></span>
                                <span class="stat-change">
                                    <i class="fas fa-info-circle"></i> Total pembayaran tertunda
                                </span>
                            </div>
                        </div>
                        
                        <div class="stat-card card-hover">
                            <div class="stat-icon bg-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Pinjaman Terakhir</h3>
                                <span class="stat-value">
                                    <?= $pinjaman_terakhir ? format_rupiah($pinjaman_terakhir['jumlah']) : '-' ?>
                                </span>
                                <span class="stat-change">
                                    <?php if ($pinjaman_terakhir): ?>
                                        Status: <?= ucfirst($pinjaman_terakhir['status']) ?>
                                    <?php else: ?>
                                        Belum ada pinjaman
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pembayaran Mendatang -->
                    <div class="card card-hover">
                        <div class="card-header">
                            <h3>Pembayaran Mendatang</h3>
                            <a href="pembayaran.php" class="btn btn-sm btn-primary">Lihat Semua</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Tanggal Jatuh Tempo</th>
                                            <th>Jumlah</th>
                                            <th>Pinjaman</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $pembayaran = query("
                                            SELECT c.*, p.jumlah as jumlah_pinjaman 
                                            FROM cicilan c
                                            JOIN pinjaman p ON c.id_pinjaman = p.id
                                            WHERE p.id_anggota='$member_id' AND c.status='belum lunas'
                                            ORDER BY c.tanggal ASC
                                            LIMIT 5
                                        ");
                                        
                                        while ($row = $pembayaran->fetch_assoc()):
                                        ?>
                                        <tr>
                                            <td><?= tanggal_indonesia($row['tanggal']) ?></td>
                                            <td><?= format_rupiah($row['jumlah']) ?></td>
                                            <td><?= format_rupiah($row['jumlah_pinjaman']) ?></td>
                                            <td>
                                                <span class="badge badge-<?= $row['tanggal'] < date('Y-m-d') ? 'danger' : 'warning' ?>">
                                                    <?= $row['tanggal'] < date('Y-m-d') ? 'Terlambat' : 'Belum Jatuh Tempo' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="pembayaran.php?bayar=<?= $row['id'] ?>" class="btn btn-sm btn-success">
                                                    <i class="fas fa-money-bill-wave mr-1"></i> Bayar
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Riwayat Pinjaman -->
                    <div class="card card-hover">
                        <div class="card-header">
                            <h3>Riwayat Pinjaman</h3>
                            <a href="pinjaman.php" class="btn btn-sm btn-primary">Lihat Semua</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>ID Pinjaman</th>
                                            <th>Tanggal</th>
                                            <th>Jumlah</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $pinjaman = query("
                                            SELECT * FROM pinjaman 
                                            WHERE id_anggota='$member_id'
                                            ORDER BY tanggal_pinjam DESC
                                            LIMIT 5
                                        ");
                                        
                                        while ($row = $pinjaman->fetch_assoc()):
                                        ?>
                                        <tr>
                                            <td><?= $row['id'] ?></td>
                                            <td><?= tanggal_indonesia($row['tanggal_pinjam']) ?></td>
                                            <td><?= format_rupiah($row['jumlah']) ?></td>
                                            <td>
                                                <span class="badge badge-<?= 
                                                    $row['status'] == 'disetujui' ? 'success' : 
                                                    ($row['status'] == 'ditolak' ? 'danger' : 'warning')
                                                ?>">
                                                    <?= ucfirst($row['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="pinjaman.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
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

    <?php 
    $js = ["member.js"];
    include '../includes/member-footer.php'; 
    ?>
</body>
</html>