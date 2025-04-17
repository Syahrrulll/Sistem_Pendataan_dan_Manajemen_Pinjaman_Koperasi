<?php
require_once 'config/database.php';
require_once 'config/functions.php';

if (is_logged_in()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean_input($_POST['username']);
    $password = clean_input($_POST['password']);
    
    $query = query("SELECT * FROM pengguna WHERE username='$username'");
    
    if ($query->num_rows == 1) {
        $user = $query->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];
            
            if ($user['role'] == 'admin') {
                redirect('admin/dashboard.php');
            } else {
                redirect('member/dashboard.php');
            }
        } else {
            $error = "Username atau password salah!";
        }
    } else {
        $error = "Username atau password salah!";
    }
}

$title = "Login - Koperasi Digital";
$css = ["auth.css", "animations.css"];
include 'includes/head.php';
?>

<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card fade-in">
            <div class="auth-hero">
                <div class="hero-content">
                    <img src="<?= BASE_URL ?>/assets/images/logo.svg" alt="Logo" class="logo">
                    <h1>Sistem Koperasi Digital</h1>
                    <p>Manajemen pinjaman modern untuk kemajuan bersama</p>
                </div>
            </div>
            
            <div class="auth-form">
                <div class="form-header">
                    <h2>Masuk ke Akun</h2>
                    <p>Silakan masuk untuk mengakses dashboard</p>
                </div>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="form-body">
                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-icon"><i class="fas fa-user"></i></span>
                            <input type="text" id="username" name="username" class="form-control" placeholder="username" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-icon"><i class="fas fa-lock"></i></span>
                            <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg btn-block pulse">
                        <i class="fas fa-sign-in-alt mr-2"></i> Masuk Sekarang
                    </button>
                </form>
                
                <div class="form-footer">
                    <p>Belum punya akun? <a href="register.php">Daftar disini</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= BASE_URL ?>/assets/js/core.js"></script>
</body>
</html>