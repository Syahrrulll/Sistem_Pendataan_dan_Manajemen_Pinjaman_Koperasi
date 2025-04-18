<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil input username dan password
    $username = $_POST['username'];
    $password = md5($_POST['password']);  // Di sini menggunakan MD5, Anda bisa mengganti dengan bcrypt untuk keamanan lebih tinggi

    // Query untuk mengecek username dan password
    $sql = "SELECT * FROM pengguna WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        // Jika login berhasil, simpan session user
        $_SESSION['user'] = $result->fetch_assoc();

        // Redirect berdasarkan role
        if ($_SESSION['user']['role'] === 'admin') {
            header("Location: index.php");  // Redirect ke dashboard admin
        } elseif ($_SESSION['user']['role'] === 'pengurus') {
            header("Location: pengurus_dashboard.php");  // Redirect ke dashboard pengurus
        } else {
            header("Location: anggota_dashboard.php");  // Redirect ke dashboard anggota
        }
    } else {
        // Jika login gagal
        echo "<p class='error-message'>Login gagal. Periksa username dan password.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Koperasi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="assets/css/login.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2>Login Koperasi</h2>
            <form method="post">
                <div class="textbox">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="textbox">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" class="btn-login">Login</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
