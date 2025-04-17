<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect jika belum login
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Format Rupiah
function format_rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Generate ID Unik
function generate_id($prefix, $table, $field) {
    $query = query("SELECT MAX($field) as max_code FROM $table");
    $data = $query->fetch_assoc();
    $max_code = $data['max_code'];
    
    $urutan = (int) substr($max_code, strlen($prefix));
    $urutan++;
    
    return $prefix . sprintf("%05s", $urutan);
}

// Hitung Bunga Pinjaman
function hitung_bunga($pokok, $bunga, $tenor) {
    return ($pokok * $bunga / 100) * $tenor;
}

// Cek Role
function check_role($roles = []) {
    if (!in_array($_SESSION['role'], $roles)) {
        redirect('unauthorized.php');
    }
}

// Set Flash Message
function set_flash_message($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

// Tampilkan Flash Message
function display_flash_message() {
    if (isset($_SESSION['flash'])) {
        $type = $_SESSION['flash']['type'];
        $message = $_SESSION['flash']['message'];
        echo '<div class="alert alert-' . $type . '">' . $message . '</div>';
        unset($_SESSION['flash']);
    }
}

// Tanggal Indonesia
function tanggal_indonesia($date) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $tanggal = date('j', strtotime($date));
    $bulan = $bulan[(int)date('n', strtotime($date))];
    $tahun = date('Y', strtotime($date));
    
    return $tanggal . ' ' . $bulan . ' ' . $tahun;
}
?>