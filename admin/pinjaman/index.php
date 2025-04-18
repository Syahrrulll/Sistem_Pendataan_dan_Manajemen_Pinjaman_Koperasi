<?php
include '../../config.php';
session_start();
if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

$sql = "SELECT * FROM pinjaman";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pinjaman</title>
    <link href="../../assets/css/laporan.css" rel="stylesheet">
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
                    <li class="nav-item"><a class="nav-link" href="../../index.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="../anggota/index.php">Anggota</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php">Pinjaman</a></li>
                    <li class="nav-item"><a class="nav-link" href="../../laporan.php">Laporan</a></li>
                    <li class="nav-item"><a class="nav-link" href="../../logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h3>Daftar Pinjaman</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Anggota</th>
                    <th>Jumlah Pinjaman</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $no ?></td>
                    <td><?= $row['id_anggota'] ?></td>
                    <td>Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                    <td><?= $row['status'] ?></td>
                    <td>
                        <a href="persetujuan.php?id=<?= $row['id'] ?>">Persetujuan</a> | 
                        <a href="laporan.php?id=<?= $row['id'] ?>">Laporan</a>
                    </td>
                </tr>
                <?php $no++; } ?>
            </tbody>
        </table>
    </div>

    <footer class="footer mt-5">
        <p>&copy; 2025 Koperasi. Semua hak dilindungi.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
