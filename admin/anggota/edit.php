<?php
include '../../config.php';
session_start();
if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

$id = $_GET['id'];
$sql = "SELECT * FROM anggota WHERE id = '$id'";
$result = $conn->query($sql);
$anggota = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $kontak = $_POST['kontak'];
    $pekerjaan = $_POST['pekerjaan'];
    $status = $_POST['status'];

    $conn->query("UPDATE anggota SET nama='$nama', alamat='$alamat', kontak='$kontak', pekerjaan='$pekerjaan', status='$status' WHERE id='$id'");
    header('Location: index.php');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Anggota</title>
    <link href="../../assets/css/anggota.css" rel="stylesheet">
</head>
<body>
    <h3>Edit Anggota</h3>
    <form method="post">
        <label>Nama:</label><input type="text" name="nama" value="<?= $anggota['nama'] ?>" required><br>
        <label>Alamat:</label><input type="text" name="alamat" value="<?= $anggota['alamat'] ?>" required><br>
        <label>Kontak:</label><input type="text" name="kontak" value="<?= $anggota['kontak'] ?>" required><br>
        <label>Pekerjaan:</label><input type="text" name="pekerjaan" value="<?= $anggota['pekerjaan'] ?>" required><br>
        <label>Status:</label>
        <select name="status">
            <option value="aktif" <?= $anggota['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
            <option value="nonaktif" <?= $anggota['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
        </select><br>
        <button type="submit">Simpan Perubahan</button>
    </form>
</body>
</html>
