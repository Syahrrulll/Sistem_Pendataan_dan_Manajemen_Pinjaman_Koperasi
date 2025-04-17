<?php
session_start();
require_once '../../../config/database.php';
require_once '../../../config/functions.php';

if (!isLoggedIn() || $_SESSION['role'] != 'admin') {
    redirect('../login.php');
}

// Filter laporan
$filter = [];
$where = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = escape($_POST['status']);
    $bulan = escape($_POST['bulan']);
    $tahun = escape($_POST['tahun']);
    
    if ($status != 'semua') {
        $filter['status'] = $status;
        $where .= " AND p.status='$status'";
    }
    
    if ($bulan != 'semua') {
        $filter['bulan'] = $bulan;
        $where .= " AND MONTH(p.tanggal_pinjam)='$bulan'";
    }
    
    if ($tahun != 'semua') {
        $filter['tahun'] = $tahun;
        $where .= " AND YEAR(p.tanggal_pinjam)='$tahun'";
    }
}

// Query data pinjaman
$query = query("SELECT p.*, a.nama FROM pinjaman p 
               JOIN anggota a ON p.id_anggota = a.id_anggota
               WHERE 1=1 $where
               ORDER BY p.tanggal_pinjam DESC");

// Hitung total pinjaman
$total = query("SELECT SUM(jumlah) as total FROM pinjaman WHERE 1=1 $where")->fetch_assoc();
?>

<?php include '../../../includes/header.php'; ?>

<div class="content">
    <h2>Laporan Pinjaman</h2>
    
    <div class="card">
        <h3>Filter Laporan</h3>
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="semua">Semua Status</option>
                        <option value="menunggu" <?= isset($filter['status']) && $filter['status'] == 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                        <option value="disetujui" <?= isset($filter['status']) && $filter['status'] == 'disetujui' ? 'selected' : '' ?>>Disetujui</option>
                        <option value="ditolak" <?= isset($filter['status']) && $filter['status'] == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Bulan</label>
                    <select name="bulan">
                        <option value="semua">Semua Bulan</option>
                        <?php for($i=1; $i<=12; $i++): ?>
                        <option value="<?= $i ?>" <?= isset($filter['bulan']) && $filter['bulan'] == $i ? 'selected' : '' ?>>
                            <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tahun</label>
                    <select name="tahun">
                        <option value="semua">Semua Tahun</option>
                        <?php for($i=date('Y'); $i>=2020; $i--): ?>
                        <option value="<?= $i ?>" <?= isset($filter['tahun']) && $filter['tahun'] == $i ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn">Filter</button>
            <a href="pinjaman.php" class="btn btn-danger">Reset</a>
            <button type="button" onclick="window.print()" class="btn">Cetak Laporan</button>
        </form>
    </div>
    
    <div class="card">
        <h3>Data Pinjaman</h3>
        <div class="total-summary">
            <p>Total Pinjaman: <strong><?= formatRupiah($total['total'] ?? 0) ?></strong></p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Pinjaman</th>
                    <th>Nama Anggota</th>
                    <th>Jumlah</th>
                    <th>Bunga</th>
                    <th>Tenor</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while($row = $query->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['id_pinjaman'] ?></td>
                    <td><?= $row['nama'] ?></td>
                    <td><?= formatRupiah($row['jumlah']) ?></td>
                    <td><?= $row['bunga'] ?>%</td>
                    <td><?= $row['tenor'] ?> bulan</td>
                    <td><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?></td>
                    <td><?= ucfirst($row['status']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.form-row {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.form-row .form-group {
    flex: 1;
}

.total-summary {
    background-color: #f8f9fa;
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 4px;
    text-align: right;
    font-size: 1.1rem;
}

@media print {
    .card:first-child, nav, header, .btn {
        display: none;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    table th, table td {
        border: 1px solid #ddd;
        padding: 8px;
    }
    
    table th {
        background-color: #f2f2f2;
    }
}
</style>

<?php include '../../../includes/footer.php'; ?>