document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.member-sidebar');
    
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }
    
    // Form pengajuan pinjaman
    const pinjamanForm = document.getElementById('pinjamanForm');
    if (pinjamanForm) {
        pinjamanForm.addEventListener('submit', function(e) {
            const jumlah = document.getElementById('jumlah').value;
            const tenor = document.getElementById('tenor').value;
            
            if (jumlah < 1000000) {
                e.preventDefault();
                alert('Jumlah pinjaman minimal Rp1.000.000');
                return false;
            }
            
            if (tenor < 3 || tenor > 24) {
                e.preventDefault();
                alert('Tenor pinjaman harus antara 3-24 bulan');
                return false;
            }
            
            return true;
        });
    }
    
    // Hitung total pinjaman
    const jumlahInput = document.getElementById('jumlah');
    const tenorSelect = document.getElementById('tenor');
    const totalPinjaman = document.getElementById('totalPinjaman');
    
    if (jumlahInput && tenorSelect && totalPinjaman) {
        function hitungTotal() {
            const jumlah = parseFloat(jumlahInput.value) || 0;
            const tenor = parseInt(tenorSelect.value) || 0;
            const bunga = 1; // 1% per bulan
            
            const totalBunga = jumlah * (bunga / 100) * tenor;
            const total = jumlah + totalBunga;
            
            totalPinjaman.textContent = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(total);
        }
        
        jumlahInput.addEventListener('input', hitungTotal);
        tenorSelect.addEventListener('change', hitungTotal);
    }
});