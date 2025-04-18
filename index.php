<?php
include 'config.php';
session_start();

// Enhanced security check
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Fetch dashboard data
$stats = [];
try {
    // Total members
    $result = $conn->query("SELECT COUNT(*) as total FROM anggota");
    $stats['total_anggota'] = $result->fetch_assoc()['total'];
    
    // Active members
    $result = $conn->query("SELECT COUNT(*) as total FROM anggota WHERE status = 'aktif'");
    $stats['anggota_aktif'] = $result->fetch_assoc()['total'];
    
    // Total approved loans
    $result = $conn->query("SELECT SUM(jumlah) as total FROM pinjaman WHERE status = 'disetujui'");
    $stats['total_pinjaman'] = $result->fetch_assoc()['total'] ?? 0;
    
    // Total overdue payments (assuming terlambat is tracked in pembayaran table)
    $result = $conn->query("
        SELECT SUM(p.jumlah_bayar) as total 
        FROM pembayaran p
        JOIN pinjaman pm ON p.id_pinjaman = pm.id
        WHERE pm.status = 'disetujui' AND p.tanggal_bayar < CURDATE()
    ");
    $stats['tunggakan'] = $result->fetch_assoc()['total'] ?? 0;
    
    // Pending loan applications
    $result = $conn->query("SELECT COUNT(*) as total FROM pinjaman WHERE status = 'menunggu'");
    $stats['pinjaman_menunggu'] = $result->fetch_assoc()['total'] ?? 0;
    
    // Total users
    $result = $conn->query("SELECT COUNT(*) as total FROM pengguna");
    $stats['total_pengguna'] = $result->fetch_assoc()['total'];
    
} catch (Exception $e) {
    // Log error and show safe message
    error_log("Database error: " . $e->getMessage());
    $stats = [
        'total_anggota' => 'N/A',
        'anggota_aktif' => 'N/A',
        'total_pinjaman' => 'N/A',
        'tunggakan' => 'N/A',
        'pinjaman_menunggu' => 'N/A',
        'total_pengguna' => 'N/A'
    ];
}

// Format numbers
function formatNumber($num) {
    return is_numeric($num) ? number_format($num) : 'N/A';
}

function formatCurrency($amount) {
    return is_numeric($amount) ? 'Rp ' . number_format($amount, 2, ',', '.') : 'N/A';
}

function formatDate($dateString) {
    return $dateString ? date('d M Y', strtotime($dateString)) : '-';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Dashboard Admin Koperasi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="sidebar-header">
                    <div class="logo-container">
                        <i class="bi bi-coin logo-icon"></i>
                        <h1 class="logo-text">KoperasiKu</h1>
                    </div>
                </div>
                <nav class="sidebar-nav">
                    <ul class="nav-menu">
                        <li class="nav-item">
                            <a href="index.php" class="nav-link">
                                <i class="bi bi-speedometer2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin/anggota/index.php" class="nav-link">
                                <i class="bi bi-people-fill"></i>
                                Manajemen Anggota
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin/pinjaman/index.php" class="nav-link">
                                <i class="bi bi-cash-stack"></i>
                                Manajemen Pinjaman
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="laporan.php" class="nav-link">
                                <i class="bi bi-file-earmark-bar-graph"></i>
                                Laporan Keuangan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="pengaturan.php" class="nav-link">
                                <i class="bi bi-gear"></i>
                                Pengaturan Sistem
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="logout.php" class="nav-link">
                                <i class="bi bi-box-arrow-right"></i>
                                Keluar
                            </a>
                        </li>
                    </ul>
                </nav>
                <div class="sidebar-footer">
                    <p class="system-version">v2.5.1</p>
                    <p class="copyright">&copy; <?php echo date('Y'); ?> KoperasiKu</p>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <header class="main-header">
                    <h2 class="page-title">Dashboard</h2>
                    <button class="sidebar-toggle">
                        <i class="bi bi-list"></i>
                    </button>
                </header>

                <!-- Stats Cards -->
                <div class="row stats-grid">
                    <div class="col-md-4">
                        <div class="stat-card card-total-members">
                            <div class="stat-icon">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="stat-info">
                                <h3 class="stat-value"><?php echo formatNumber($stats['total_anggota']); ?></h3>
                                <p class="stat-label">Total Anggota</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="stat-card card-total-loans">
                            <div class="stat-icon">
                                <i class="bi bi-cash-coin"></i>
                            </div>
                            <div class="stat-info">
                                <h3 class="stat-value"><?php echo formatCurrency($stats['total_pinjaman']); ?></h3>
                                <p class="stat-label">Total Pinjaman</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="stat-card card-active-members">
                            <div class="stat-icon">
                                <i class="bi bi-person-check"></i>
                            </div>
                            <div class="stat-info">
                                <h3 class="stat-value"><?php echo formatNumber($stats['anggota_aktif']); ?></h3>
                                <p class="stat-label">Anggota Aktif</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Feed -->
                <div class="activity-container">
                    <div class="activity-header">
                        <h3>Aktivitas Terkini</h3>
                    </div>
                    <div class="activity-list" id="recentActivity">
                        <div class="activity-item">
                            <div class="activity-icon success">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div class="activity-content">
                                <p>Pinjaman baru disetujui untuk Budi Santoso</p>
                                <span class="activity-time">2 jam yang lalu</span>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon primary">
                                <i class="bi bi-person-plus"></i>
                            </div>
                            <div class="activity-content">
                                <p>Anggota baru terdaftar: Siti Rahayu</p>
                                <span class="activity-time">5 jam yang lalu</span>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon warning">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                            <div class="activity-content">
                                <p>Pembayaran terlambat dari Ahmad Fauzi</p>
                                <span class="activity-time">1 hari yang lalu</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <footer class="main-footer">
                    <div class="footer-left">
                        <p>&copy; <?php echo date('Y'); ?> <strong>KoperasiKu</strong>. All rights reserved.</p>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/index.js"></script>
</body>
</html>
