<?php
require_once '../config/database.php';
require_once '../config/functions.php';

if (!is_logged_in()) {
    redirect('../login.php');
}

if ($_SESSION['role'] != 'admin') {
    redirect('../unauthorized.php');
}

// Hitung total anggota
$total_anggota = query("SELECT COUNT(*) as total FROM anggota")->fetch_assoc()['total'];

// Hitung pinjaman aktif
$pinjaman_aktif = query("SELECT COUNT(*) as total FROM pinjaman WHERE status='disetujui'")->fetch_assoc()['total'];

// Hitung pembayaran bulan ini
$pembayaran_bulan_ini = query("SELECT SUM(jumlah) as total FROM pembayaran WHERE MONTH(tanggal) = MONTH(CURRENT_DATE())")->fetch_assoc()['total'];

// Hitung tunggakan
$tunggakan = query("SELECT SUM(jumlah) as total FROM cicilan WHERE status='belum lunas' AND tanggal < CURRENT_DATE()")->fetch_assoc()['total'];

// Data untuk chart
$pinjaman_per_bulan = [];
for ($i = 1; $i <= 12; $i++) {
    $query = query("SELECT SUM(jumlah) as total FROM pinjaman WHERE MONTH(tanggal_pinjam) = $i AND YEAR(tanggal_pinjam) = YEAR(CURRENT_DATE())");
    $pinjaman_per_bulan[] = $query->fetch_assoc()['total'] ?? 0;
}

$title = "Dashboard Admin";
$css = ["admin.css", "animations.css"];
include '../includes/admin-head.php';
?>

<body class="admin-dashboard">
    <div class="dashboard-container">
        <?php include '../includes/admin-sidebar.php'; ?>
        
        <div class="main-content">
            <?php include '../includes/admin-header.php'; ?>
            
            <div class="page-content">
                <div class="container">
                    <div class="page-header">
                        <h1 class="page-title">Dashboard Overview</h1>
                        <div class="breadcrumb">
                            <a href="#">Home</a> / <span>Dashboard</span>
                        </div>
                    </div>
                    
                    <?php display_flash_message(); ?>
                    
                    <!-- Stats Grid -->
                    <div class="stats-grid">
                        <div class="stat-card card-hover">
                            <div class="stat-icon bg-primary">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Total Anggota</h3>
                                <span class="stat-value"><?= $total_anggota ?></span>
                                <span class="stat-change up">
                                    <i class="fas fa-arrow-up"></i> 12% dari bulan lalu
                                </span>
                            </div>
                        </div>
                        
                        <div class="stat-card card-hover">
                            <div class="stat-icon bg-success">
                                <i class="fas fa-hand-holding-usd"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Pinjaman Aktif</h3>
                                <span class="stat-value"><?= $pinjaman_aktif ?></span>
                                <span class="stat-change up">
                                    <i class="fas fa-arrow-up"></i> 5% dari bulan lalu
                                </span>
                            </div>
                        </div>
                        
                        <div class="stat-card card-hover">
                            <div class="stat-icon bg-warning">
                                <i class="fas fa-cash-register"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Pembayaran Bulan Ini</h3>
                                <span class="stat-value"><?= format_rupiah($pembayaran_bulan_ini) ?></span>
                                <span class="stat-change down">
                                    <i class="fas fa-arrow-down"></i> 3% dari bulan lalu
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
                                <span class="stat-change down">
                                    <i class="fas fa-arrow-down"></i> 8% dari bulan lalu
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Charts Row -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card card-hover">
                                <div class="card-header">
                                    <h3>Statistik Pinjaman</h3>
                                    <div class="chart-actions">
                                        <select class="form-select">
                                            <option>12 Bulan Terakhir</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <canvas id="loansChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card card-hover">
                                <div class="card-header">
                                    <h3>Distribusi Pinjaman</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="loanDistribution" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Activity -->
                    <div class="card card-hover">
                        <div class="card-header">
                            <h3>Aktivitas Terakhir</h3>
                            <a href="#" class="btn btn-sm btn-primary">Lihat Semua</a>
                        </div>
                        <div class="card-body">
                            <div class="activity-list">
                                <?php
                                $activities = query("
                                    SELECT p.*, a.nama 
                                    FROM pinjaman p
                                    JOIN anggota a ON p.id_anggota = a.id
                                    ORDER BY p.tanggal_pinjam DESC 
                                    LIMIT 5
                                ");
                                
                                while ($activity = $activities->fetch_assoc()):
                                ?>
                                <div class="activity-item">
                                    <div class="activity-avatar">
                                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($activity['nama']) ?>" alt="User">
                                    </div>
                                    <div class="activity-content">
                                        <p><strong><?= $activity['nama'] ?></strong> mengajukan pinjaman sebesar <strong><?= format_rupiah($activity['jumlah']) ?></strong></p>
                                        <small class="text-muted"><?= tanggal_indonesia($activity['tanggal_pinjam']) ?></small>
                                    </div>
                                    <div class="activity-action">
                                        <span class="badge badge-primary"><?= ucfirst($activity['status']) ?></span>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php 
    $js = ["charts.js", "admin.js"];
    include '../includes/admin-footer.php'; 
    ?>
    
    <script>
    // Data untuk chart
    const pinjamanData = <?= json_encode($pinjaman_per_bulan) ?>;
    </script>
</body>
</html>