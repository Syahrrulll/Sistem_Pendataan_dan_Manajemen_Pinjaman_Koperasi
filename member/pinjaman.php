<?php
$title = "Pengajuan Pinjaman";
$css = ['member.css', 'form-wizard.css'];
include 'includes/member-header.php';

$idAnggota = $_SESSION['user_id'];

// Proses Pengajuan Pinjaman
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idPinjaman = generateKode('PJM', 'pinjaman', 'id_pinjaman');
    $jumlah = str_replace(['Rp', '.', ' '], '', $_POST['jumlah']);
    $tenor = $_POST['tenor'];
    $tujuan = escapeStr($_POST['tujuan']);
    $bunga = 1; // Bunga 1% per bulan
    
    // Validasi
    if ($jumlah < 1000000) {
        setFlash('error', 'Jumlah pinjaman minimal Rp1.000.000');
        redirect('pinjaman.php');
    }
    
    if ($tenor < 3 || $tenor > 24) {
        setFlash('error', 'Tenor pinjaman harus antara 3-24 bulan');
        redirect('pinjaman.php');
    }
    
    // Hitung total pinjaman
    $totalBunga = hitungBunga($jumlah, $bunga, $tenor);
    $totalPinjaman = $jumlah + $totalBunga;
    
    // Simpan ke database
    $query = "INSERT INTO pinjaman VALUES (
        '$idPinjaman', '$idAnggota', '$jumlah', '$bunga', '$tenor', 
        '$totalPinjaman', '$tujuan', 'menunggu', NOW()
    )";
    
    if (query($query)) {
        setFlash('success', 'Pengajuan pinjaman berhasil dikirim!');
        redirect('pinjaman.php');
    } else {
        setFlash('error', 'Gagal mengajukan pinjaman');
        redirect('pinjaman.php');
    }
}

// Riwayat Pinjaman
$riwayatPinjaman = query("SELECT * FROM pinjaman WHERE id_anggota = '$idAnggota' ORDER BY tanggal_pengajuan DESC");
?>

<div class="member-dashboard">
    <?php include 'includes/member-sidebar.php'; ?>
    
    <div class="member-main">
        <?php include 'includes/member-navbar.php'; ?>
        
        <div class="member-content">
            <div class="container">
                <div class="page-header">
                    <h1 class="page-title">Pengajuan Pinjaman</h1>
                    <div class="breadcrumb">
                        <a href="dashboard.php">Home</a> / <span>Pinjaman</span>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card card-hover">
                            <div class="card-header">
                                <h3>Form Pengajuan Pinjaman</h3>
                            </div>
                            <div class="card-body">
                                <form id="pinjamanForm" method="POST">
                                    <div class="form-group">
                                        <label class="form-label">Jumlah Pinjaman</label>
                                        <input type="text" name="jumlah" class="form-control money-input" placeholder="Rp 1.000.000" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label">Tenor (bulan)</label>
                                        <select name="tenor" class="form-control" required>
                                            <option value="">Pilih Tenor</option>
                                            <?php for($i=3; $i<=24; $i++): ?>
                                            <option value="<?= $i ?>"><?= $i ?> Bulan</option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label">Tujuan Pinjaman</label>
                                        <textarea name="tujuan" class="form-control" rows="3" placeholder="Contoh: Modal usaha, biaya pendidikan, renovasi rumah" required></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label">Total Pinjaman</label>
                                        <div class="total-pinjaman">
                                            <span id="totalPinjaman">Rp 0</span>
                                            <small id="detailBunga">Termasuk bunga 0%</small>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-paper-plane mr-2"></i> Ajukan Pinjaman
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <div class="card card-hover">
                            <div class="card-header">
                                <h3>Simulasi Cicilan</h3>
                            </div>
                            <div class="card-body">
                                <div id="simulasiContainer" class="simulasi-cicilan">
                                    <div class="empty-simulasi">
                                        <i class="fas fa-calculator"></i>
                                        <p>Masukkan jumlah dan tenor pinjaman untuk melihat simulasi cicilan</p>
                                    </div>
                                    <div id="simulasiContent" style="display: none;">
                                        <div class="simulasi-header">
                                            <h4>Rincian Cicilan</h4>
                                            <div class="total-cicilan">
                                                <small>Total Pinjaman</small>
                                                <span id="simulasiTotal">Rp 0</span>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table" id="tabelSimulasi">
                                                <thead>
                                                    <tr>
                                                        <th>Cicilan Ke</th>
                                                        <th>Tanggal</th>
                                                        <th>Jumlah</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="simulasiBody">
                                                    <!-- Diisi oleh JavaScript -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Riwayat Pinjaman -->
                <div class="card card-hover">
                    <div class="card-header">
                        <h3>Riwayat Pinjaman Anda</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($riwayatPinjaman->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID Pinjaman</th>
                                        <th>Jumlah</th>
                                        <th>Tenor</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th>Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $riwayatPinjaman->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['id_pinjaman'] ?></td>
                                        <td><?= formatRupiah($row['jumlah']) ?></td>
                                        <td><?= $row['tenor'] ?> bulan</td>
                                        <td>
                                            <span class="badge badge-<?= 
                                                $row['status'] == 'disetujui' ? 'success' : 
                                                ($row['status'] == 'ditolak' ? 'danger' : 'warning') 
                                            ?>">
                                                <?= ucfirst($row['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= formatTanggal($row['tanggal_pengajuan']) ?></td>
                                        <td>
                                            <a href="pinjaman-detail.php?id=<?= $row['id_pinjaman'] ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-info-circle"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-history"></i>
                            <h4>Belum ada riwayat pinjaman</h4>
                            <p>Anda belum pernah mengajukan pinjaman sebelumnya</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$js = ['form-wizard.js', 'member-pinjaman.js'];
$inlineJs = "
    // Hitung total pinjaman
    function hitungTotal() {
        const jumlah = parseInt($('input[name=\"jumlah\"]').val().replace(/[^0-9]/g, '')) || 0;
        const tenor = parseInt($('select[name=\"tenor\"]').val()) || 0;
        const bunga = 1; // 1% per bulan
        
        if (jumlah > 0 && tenor > 0) {
            const totalBunga = (jumlah * (bunga / 100)) * tenor;
            const totalPinjaman = jumlah + totalBunga;
            const cicilanPerBulan = totalPinjaman / tenor;
            
            // Update tampilan
            $('#totalPinjaman').text('Rp ' + totalPinjaman.toLocaleString('id-ID'));
            $('#detailBunga').text('Termasuk bunga ' + (bunga * tenor) + '%');
            
            // Update simulasi
            $('#simulasiTotal').text('Rp ' + totalPinjaman.toLocaleString('id-ID'));
            
            // Generate tabel simulasi
            let simulasiHTML = '';
            const today = new Date();
            
            for (let i = 1; i <= tenor; i++) {
                const nextMonth = new Date(today.getFullYear(), today.getMonth() + i, today.getDate());
                const bulan = nextMonth.toLocaleString('id-ID', { month: 'long' });
                const tahun = nextMonth.getFullYear();
                
                simulasiHTML += `
                    <tr>
                        <td>${i}</td>
                        <td>${bulan} ${tahun}</td>
                        <td>Rp ' + new Intl.NumberFormat('id-ID').format(cicilanPerBulan) + '</td>
                    </tr>
                `;
            }
            
            $('#simulasiBody').html(simulasiHTML);
            $('#simulasiContent').show();
            $('.empty-simulasi').hide();
        } else {
            $('#simulasiContent').hide();
            $('.empty-simulasi').show();
        }
    }
    
    // Event listeners
    $('input[name=\"jumlah\"]').on('input', hitungTotal);
    $('select[name=\"tenor\"]').on('change', hitungTotal);
    
    // Format input uang
    $('.money-input').on('input', function() {
        let value = $(this).val().replace(/[^0-9]/g, '');
        value = new Intl.NumberFormat('id-ID').format(value);
        $(this).val('Rp ' + value);
    });
    
    // Form validation
    $('#pinjamanForm').on('submit', function(e) {
        const jumlah = parseInt($('input[name=\"jumlah\"]').val().replace(/[^0-9]/g, '')) || 0;
        const tenor = parseInt($('select[name=\"tenor\"]').val()) || 0;
        
        if (jumlah < 1000000) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Jumlah terlalu kecil',
                text: 'Jumlah pinjaman minimal Rp1.000.000'
            });
            return false;
        }
        
        if (tenor < 3 || tenor > 24) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Tenor tidak valid',
                text: 'Tenor pinjaman harus antara 3-24 bulan'
            });
            return false;
        }
        
        return true;
    });
";
include 'includes/member-footer.php';
?>