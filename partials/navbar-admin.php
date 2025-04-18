<nav class="navbar navbar-expand-lg navbar-admin">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">
            <img src="../assets/img/logo.png" alt="Logo Koperasi">
            Koperasi Pinjaman
        </a>
        
        <div class="navbar-user">
            <img src="../assets/img/user-avatar.png" alt="User" class="user-avatar">
            <span class="me-3"><?= $_SESSION['user']['nama'] ?? 'Admin'; ?></span>
            <a href="../logout.php" class="btn btn-sm btn-logout">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </div>
</nav>