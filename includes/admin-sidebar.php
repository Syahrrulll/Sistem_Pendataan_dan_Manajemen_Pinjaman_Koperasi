<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <img src="<?= BASE_URL ?>/assets/images/logo.svg" alt="Logo">
            <h3>Koperasi Digital</h3>
        </div>
        <button class="sidebar-toggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <div class="sidebar-menu">
        <div class="menu-title">Menu Utama</div>
        <a href="<?= BASE_URL ?>/admin/dashboard.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        
        <div class="menu-title">Manajemen</div>
        <a href="<?= BASE_URL ?>/admin/anggota/index.php" class="menu-item <?= strpos($_SERVER['PHP_SELF'], 'anggota') !== false ? 'active' : '' ?>">
            <i class="fas fa-users"></i>
            <span>Anggota</span>
        </a>
        <a href="<?= BASE_URL ?>/admin/pinjaman/index.php" class="menu-item <?= strpos($_SERVER['PHP_SELF'], 'pinjaman') !== false ? 'active' : '' ?>">
            <i class="fas fa-hand-holding-usd"></i>
            <span>Pinjaman</span>
        </a>
        <a href="<?= BASE_URL ?>/admin/pembayaran/index.php" class="menu-item <?= strpos($_SERVER['PHP_SELF'], 'pembayaran') !== false ? 'active' : '' ?>">
            <i class="fas fa-cash-register"></i>
            <span>Pembayaran</span>
        </a>
        
        <div class="menu-title">Laporan</div>
        <a href="<?= BASE_URL ?>/admin/laporan/pinjaman.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'pinjaman.php' ? 'active' : '' ?>">
            <i class="fas fa-file-invoice-dollar"></i>
            <span>Laporan Pinjaman</span>
        </a>
        <a href="<?= BASE_URL ?>/admin/laporan/pembayaran.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'pembayaran.php' ? 'active' : '' ?>">
            <i class="fas fa-file-alt"></i>
            <span>Laporan Pembayaran</span>
        </a>
        
        <div class="menu-title">Pengaturan</div>
        <a href="#" class="menu-item">
            <i class="fas fa-cog"></i>
            <span>Pengaturan</span>
        </a>
        <a href="<?= BASE_URL ?>/logout.php" class="menu-item">
            <i class="fas fa-sign-out-alt"></i>
            <span>Keluar</span>
        </a>
    </div>
</div>