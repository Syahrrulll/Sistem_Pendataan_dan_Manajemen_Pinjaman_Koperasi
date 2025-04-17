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
    $bulan = escape($_POST['bulan']);
    $tahun = escape($_POST['tahun']);
    
    if ($bulan != 'semua') {
        $filter['bulan'] = $bulan;
        $where .= " AND MONTH(p.tanggal_bayar)='$bulan'";
    }
    
    if ($tahun != 'semua') {
        $filter['tahun'] = $tahun;
        $where .= " AND YEAR(p.tanggal_bayar)='$tahun'";
    }
}

// Query data pembayaran
$query = query("SELECT p.*, a.nama, pm.id_pinjaman, pm.jumlah as jumlah_pinjaman 
               FROM pembayaran p
               JOIN cicilan c ON p.id_cicilan = c.id_cicilan
               JOIN pinjaman pm ON p.id_pinjaman = pm.id_pinjaman
               JOIN anggota a ON pm.id_anggota = a.id_anggota
               WHERE 1=1 $where
               ORDER BY p.tanggal_bayar DESC");

// Hitung total pembayaran
$total = query("SELECT SUM(p.jumlah_bayar) as total FROM pembayaran p WHERE 1=1 $where")->fetch_assoc();
?>

<?php include '../../../includes/header.php'; ?>

<div class="content">
    <h2>Laporan Pembayaran</h2>
    
    <div class="card">
        <h3>Filter Laporan</h3>
        <form method="POST">
            <div class="form-row">
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
            <a href="pembayaran.php" class="btn btn-danger">Reset</a>
            <button type="button" onclick="window.print()" class="btn">Cetak Laporan</button>
        </form>
    </div>
    
    <div class="card">
        <h3>Data Pembayaran</h3>
        <div class="total-summary">
            <p>Total Pembayaran: <strong><?= formatRupiah($total['total'] ?? 0) ?></strong></p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Pembayaran</th>
                    <th>Nama Anggota</th>
                    <th>ID Pinjaman</th>
                    <th>Tanggal Bayar</th>
                    <th>Jumlah Bayar</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while($row = $query->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['id_pembayaran'] ?></td>
                    <td><?= $row['nama'] ?></td>
                    <td><?= $row['id_pinjaman'] ?></td>
                    <td><?= date('d/m/Y', strtotime($row['tanggal_bayar'])) ?></td>
                    <td><?= formatRupiah($row['jumlah_bayar']) ?></td>
                    <td><?= $row['keterangan'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../../includes/footer.php'; ?>