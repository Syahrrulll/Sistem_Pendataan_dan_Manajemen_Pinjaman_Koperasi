<?php
include 'config.php';
session_start();
if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Koperasi</title>
    <link href="assets/css/dashboard.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Koperasi</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin/anggota/index.php">Anggota</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin/pinjaman/index.php">Pinjaman</a></li>
                    <li class="nav-item"><a class="nav-link" href="laporan.php">Laporan</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h3 class="text-center">Dashboard Koperasi</h3>
        <div class="row">
            <div class="col-md-3">
                <div class="card animated-card p-4">
                    <div class="card-header">Total Anggota</div>
                    <div class="card-body">
                        <h3>200</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card animated-card p-4">
                    <div class="card-header">Total Pinjaman</div>
                    <div class="card-body">
                        <h3>Rp 1.500.000.000</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card animated-card p-4">
                    <div class="card-header">Anggota Aktif</div>
                    <div class="card-body">
                        <h3>150</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card animated-card p-4">
                    <div class="card-header">Tunggakan</div>
                    <div class="card-body">
                        <h3>Rp 250.000.000</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer mt-5">
        <p>&copy; 2025 Koperasi. Semua hak dilindungi.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
