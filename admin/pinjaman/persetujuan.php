<?php
include '../../config.php';
session_start();
if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

// Ambil data pinjaman berdasarkan ID
$id = $_GET['id'];
$sql = "SELECT * FROM pinjaman WHERE id = '$id'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan Pinjaman</title>
    <link href="../../assets/css/persetujuan.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Koperasi</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="../../index.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="../anggota/index.php">Anggota</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php">Pinjaman</a></li>
                    <li class="nav-item"><a class="nav-link" href="../../laporan.php">Laporan</a></li>
                    <li class="nav-item"><a class="nav-link" href="../../logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container mt-5">
        <h3 class="text-center mb-4">Persetujuan Pinjaman</h3>
        <div class="card">
            <div class="card-header">
                <strong>Detail Pinjaman</strong>
            </div>
            <div class="card-body">
                <form action="proses_persetujuan.php" method="POST">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Anggota</label>
                        <input type="text" class="form-control" id="nama" value="<?= $row['id_anggota'] ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah_pinjaman" class="form-label">Jumlah Pinjaman</label>
                        <input type="text" class="form-control" id="jumlah_pinjaman" value="Rp <?= number_format($row['jumlah'], 0, ',', '.') ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="disetujui" <?= ($row['status'] == 'disetujui') ? 'selected' : '' ?>>Disetujui</option>
                            <option value="ditolak" <?= ($row['status'] == 'ditolak') ? 'selected' : '' ?>>Ditolak</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-success">Simpan</button>
                        <a href="index.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer mt-5 bg-light text-center py-3">
        <p>&copy; 2025 Koperasi. Semua hak dilindungi.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
