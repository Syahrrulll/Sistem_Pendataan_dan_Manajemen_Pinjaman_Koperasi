<?php
include '../../config.php';
session_start();
if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $kontak = $_POST['kontak'];
    $pekerjaan = $_POST['pekerjaan'];
    $status = $_POST['status'];

    $sql = "INSERT INTO anggota (nama, alamat, kontak, pekerjaan, status) 
            VALUES ('$nama', '$alamat', '$kontak', '$pekerjaan', '$status')";
    if ($conn->query($sql)) {
        header('Location: index.php');
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Anggota</title>
    <link href="../../assets/css/anggota.css" rel="stylesheet">
</head>
<body>
    <h3>Tambah Anggota</h3>
    <form method="post">
        <label>Nama:</label><input type="text" name="nama" required><br>
        <label>Alamat:</label><input type="text" name="alamat" required><br>
        <label>Kontak:</label><input type="text" name="kontak" required><br>
        <label>Pekerjaan:</label><input type="text" name="pekerjaan" required><br>
        <label>Status:</label>
        <select name="status">
            <option value="aktif">Aktif</option>
            <option value="nonaktif">Nonaktif</option>
        </select><br>
        <button type="submit">Simpan</button>
    </form>
</body>
</html>
