<?php
$title = "Manajemen Pinjaman";
$css = ['admin.css', 'data-tables.css'];
include 'includes/admin-header.php';

// Proses Persetujuan Pinjaman
if (isset($_GET['setuju'])) {
    $id = escapeStr($_GET['setuju']);
    
    // Update status pinjaman
    if (query("UPDATE pinjaman SET status = 'disetujui' WHERE id_pinjaman = '$id'")) {
        // Buat jadwal cicilan
        $pinjaman = query("SELECT * FROM pinjaman WHERE id_pinjaman = '$id'")->fetch_assoc();
        $jumlahCicilan = $pinjaman['tenor'];
        $totalPinjaman = $pinjaman['jumlah'] + ($pinjaman['jumlah'] * $pinjaman['bunga'] / 100);
        $jumlahPerCicilan = $totalPinjaman / $jumlahCicilan;
        
        $tanggal = date('Y-m-d');
        for ($i = 1; $i <= $jumlahCicilan; $i++) {
            $tanggal = date('Y-m-d', strtotime("+$i month", strtotime($tanggal)));
            query("INSERT INTO cicilan VALUES (
                NULL, '$id', '$tanggal', '$jumlahPerCicilan', 'belum lunas'
            )");
        }
        
        setFlash('success', 'Pinjaman disetujui dan jadwal cicilan dibuat');
        redirect('pinjaman/index.php');
    } else {
        setFlash('error', 'Gagal menyetujui pinjaman');
        redirect('pinjaman/index.php');
    }
}

// Proses Penolakan Pinjaman
if (isset($_GET['tolak'])) {
    $id = escapeStr($_GET['tolak']);
    
    if (query("UPDATE pinjaman SET status = 'ditolak' WHERE id_pinjaman = '$id'")) {
        setFlash('success', 'Pinjaman ditolak');
        redirect('pinjaman/index.php');
    } else {
        setFlash('error', 'Gagal menolak pinjaman');
        redirect('pinjaman/index.php');
    }
}

$pinjaman = query("SELECT p.*, a.nama, a.foto FROM pinjaman p JOIN anggota a ON p.id_anggota = a.id_anggota ORDER BY p.tanggal_pengajuan DESC");
?>

<div class="dashboard-container">
    <?php include 'includes/admin-sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/admin-navbar.php'; ?>
        
        <div class="page-content">
            <div class="container">
                <div class="page-header">
                    <h1 class="page-title">Manajemen Pinjaman</h1>
                    <div class="breadcrumb">
                        <a href="dashboard.php">Home</a> / <span>Pinjaman</span>
                    </div>
                </div>
                
                <div class="card card-hover">
                    <div class="card-header">
                        <h3>Daftar Pengajuan Pinjaman</h3>
                        <div class="card-actions">
                            <a href="laporan.php" class="btn btn-secondary">
                                <i class="fas fa-file-export mr-2"></i> Export Laporan
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="pinjamanTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Anggota</th>
                                        <th>Jumlah</th>
                                        <th>Bunga</th>
                                        <th>Tenor</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $pinjaman->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <div class="user-info">
                                                <img src="assets/images/avatars/<?= $row['foto'] ?>" alt="<?= $row['nama'] ?>" class="avatar">
                                                <div>
                                                    <strong><?= $row['nama'] ?></strong><br>
                                                    <small><?= $row['id_pinjaman'] ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= formatRupiah($row['jumlah']) ?></td>
                                        <td><?= $row['bunga'] ?>%</td>
                                        <td><?= $row['tenor'] ?> bulan</td>
                                        <td><?= formatTanggal($row['tanggal_pengajuan'], 'd M Y') ?></td>
                                        <td>
                                            <span class="badge badge-<?= 
                                                $row['status'] == 'disetujui' ? 'success' : 
                                                ($row['status'] == 'ditolak' ? 'danger' : 'warning') 
                                            ?>">
                                                <?= ucfirst($row['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="detail.php?id=<?= $row['id_pinjaman'] ?>" class="btn btn-sm btn-info" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                <?php if ($row['status'] == 'menunggu'): ?>
                                                    <a href="index.php?setuju=<?= $row['id_pinjaman'] ?>" class="btn btn-sm btn-success" title="Setujui">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                    <a href="index.php?tolak=<?= $row['id_pinjaman'] ?>" class="btn btn-sm btn-danger" title="Tolak">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$js = ['data-tables.js'];
$inlineJs = "
    $(document).ready(function() {
        $('#pinjamanTable').DataTable({
            responsive: true,
            language: {
                url: 'assets/json/id.json'
            },
            columnDefs: [
                { orderable: false, targets: [6] }
            ]
        });
    });
    
    // Konfirmasi sebelum menyetujui atau menolak
    document.querySelectorAll('[href*=\"setuju\"], [href*=\"tolak\"]').forEach(link => {
        link.addEventListener('click', function(e) {
            const action = this.href.includes('setuju') ? 'menyetujui' : 'menolak';
            if (!confirm(`Anda yakin ingin ${action} pinjaman ini?`)) {
                e.preventDefault();
            }
        });
    });
";
include 'includes/admin-footer.php';
?>