<?php
include 'auth.php';
checkLogin('anggota');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Anggota</title>
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
                    <li class="nav-item"><a class="nav-link" href="anggota_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="pinjaman.php">Pinjaman</a></li>
                    <li class="nav-item"><a class="nav-link" href="pembayaran.php">Pembayaran</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h3 class="text-center">Dashboard Anggota</h3>
        <div class="row">
            <div class="col-md-6">
                <div class="card animated-card p-4">
                    <div class="card-header">Total Pinjaman</div>
                    <div class="card-body">
                        <h3>Rp 2.000.000</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card animated-card p-4">
                    <div class="card-header">Status Pembayaran</div>
                    <div class="card-body">
                        <h3>Lunas</h3>
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
