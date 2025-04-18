<?php
include 'auth.php';
checkLogin('anggota');
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pengajuan Pinjaman</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-5">
    <h3>Pengajuan Pinjaman</h3>
    <form method="post">
      <div class="mb-3">
        <label for="jumlah" class="form-label">Jumlah Pinjaman</label>
        <input type="number" class="form-control" id="jumlah" name="jumlah" required>
      </div>
      <div class="mb-3">
        <label for="bunga" class="form-label">Bunga (%)</label>
        <input type="number" class="form-control" id="bunga" name="bunga" required>
      </div>
      <div class="mb-3">
        <label for="tenor" class="form-label">Tenor (bulan)</label>
        <input type="number" class="form-control" id="tenor" name="tenor" required>
      </div>
      <button type="submit" class="btn btn-custom">Ajukan</button>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
