<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/functions.php';

if (!isLoggedIn() || $_SESSION['role'] != 'admin') {
    redirect('../login.php');
}

// Proses pembayaran
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bayar'])) {
    $id_pembayaran = generateKode('BYR', 'pembayaran', 'id_pembayaran');
    $id_cicilan = escape($_POST['id_cicilan']);
    $id_pinjaman = escape($_POST['id_pinjaman']);
    $jumlah_bayar = escape($_POST['jumlah_bayar']);
    $keterangan = escape($_POST['keterangan']);
    
    // Simpan pembayaran
    $query = query("INSERT INTO pembayaran VALUES (
        '$id_pembayaran', '$id_pinjaman', '$id_cicilan', 
        CURDATE(), '$jumlah_bayar', '$keterangan'
    )");
    
    if ($query) {
        // Update status cicilan
        query("UPDATE cicilan SET status='lunas' WHERE id_cicilan='$id_cicilan'");
        $_SESSION['success'] = "Pembayaran berhasil dicatat!";
        redirect('index.php');
    } else {
        $_SESSION['error'] = "Gagal mencatat pembayaran!";
    }
}

// Dapatkan daftar cicilan yang belum lunas
$cicilan = query("SELECT c.*, p.id_pinjaman, p.id_anggota, a.nama 
                 FROM cicilan c
                 JOIN pinjaman p ON c.id_pinjaman = p.id_pinjaman
                 JOIN anggota a ON p.id_anggota = a.id_anggota
                 WHERE c.status='belum lunas' AND c.tanggal <= CURDATE()
                 ORDER BY c.tanggal ASC");
?>

<?php include '../../includes/header.php'; ?>

<div class="content">
    <h2>Manajemen Pembayaran</h2>
    
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <div class="card">
        <h3>Daftar Cicilan Jatuh Tempo</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Pinjaman</th>
                    <th>Nama Anggota</th>
                    <th>Tanggal Jatuh Tempo</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while($row = $cicilan->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['id_pinjaman'] ?></td>
                    <td><?= $row['nama'] ?></td>
                    <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                    <td><?= formatRupiah($row['jumlah']) ?></td>
                    <td>
                        <button onclick="showBayarModal(
                            '<?= $row['id_cicilan'] ?>',
                            '<?= $row['id_pinjaman'] ?>',
                            '<?= $row['nama'] ?>',
                            '<?= date('d/m/Y', strtotime($row['tanggal'])) ?>',
                            '<?= $row['jumlah'] ?>'
                        )" class="btn">Bayar</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Pembayaran -->
<div id="bayarModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Form Pembayaran Cicilan</h3>
        <form method="POST" id="formBayar">
            <input type="hidden" name="id_cicilan" id="id_cicilan">
            <input type="hidden" name="id_pinjaman" id="id_pinjaman">
            
            <div class="form-group">
                <label>Nama Anggota</label>
                <input type="text" id="nama_anggota" readonly>
            </div>
            <div class="form-group">
                <label>Tanggal Jatuh Tempo</label>
                <input type="text" id="tanggal_tempo" readonly>
            </div>
            <div class="form-group">
                <label>Jumlah yang Harus Dibayar</label>
                <input type="text" id="jumlah_cicilan" readonly>
            </div>
            <div class="form-group">
                <label>Jumlah Dibayar</label>
                <input type="number" name="jumlah_bayar" id="jumlah_bayar" required>
            </div>
            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan"></textarea>
            </div>
            <button type="submit" name="bayar" class="btn">Simpan Pembayaran</button>
        </form>
    </div>
</div>

<script>
// Modal functions
var modal = document.getElementById("bayarModal");
var span = document.getElementsByClassName("close")[0];

function showBayarModal(id_cicilan, id_pinjaman, nama, tanggal, jumlah) {
    document.getElementById("id_cicilan").value = id_cicilan;
    document.getElementById("id_pinjaman").value = id_pinjaman;
    document.getElementById("nama_anggota").value = nama;
    document.getElementById("tanggal_tempo").value = tanggal;
    document.getElementById("jumlah_cicilan").value = jumlah;
    document.getElementById("jumlah_bayar").value = jumlah;
    document.getElementById("jumlah_bayar").min = jumlah;
    
    modal.style.display = "block";
}

span.onclick = function() {
    modal.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>

<style>
/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 10% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 50%;
    border-radius: 8px;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    color: black;
}
</style>

<?php include '../../includes/footer.php'; ?>