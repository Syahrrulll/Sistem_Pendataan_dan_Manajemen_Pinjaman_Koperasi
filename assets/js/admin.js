// Admin Specific Functions
document.addEventListener('DOMContentLoaded', function() {
    // DataTable initialization
    const tables = document.querySelectorAll('.data-table');
    tables.forEach(table => {
        new simpleDatatables.DataTable(table, {
            searchable: true,
            fixedHeight: true,
            labels: {
                placeholder: "Cari...",
                perPage: "{select} data per halaman",
                noRows: "Tidak ada data ditemukan",
                info: "Menampilkan {start} sampai {end} dari {rows} data",
                noResults: "Tidak ada hasil yang cocok"
            }
        });
    });
    
    // Chart initialization is in charts.js
    
    // Approve/reject loan buttons
    const approveButtons = document.querySelectorAll('.approve-loan');
    approveButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const loanId = this.dataset.loanId;
            if (confirm('Apakah Anda yakin ingin menyetujui pinjaman ini?')) {
                fetch(`/admin/pinjaman/approve.php?id=${loanId}`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Pinjaman berhasil disetujui', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast('Gagal menyetujui pinjaman', 'danger');
                    }
                });
            }
        });
    });
    
    const rejectButtons = document.querySelectorAll('.reject-loan');
    rejectButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const loanId = this.dataset.loanId;
            const reason = prompt('Alasan penolakan:');
            if (reason) {
                fetch(`/admin/pinjaman/reject.php?id=${loanId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ reason: reason })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Pinjaman berhasil ditolak', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast('Gagal menolak pinjaman', 'danger');
                    }
                });
            }
        });
    });
    
    // Record payment
    const recordPaymentButtons = document.querySelectorAll('.record-payment');
    recordPaymentButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const installmentId = this.dataset.installmentId;
            showModal('paymentModal');
            document.getElementById('installment_id').value = installmentId;
        });
    });
    
    // Print report
    const printButtons = document.querySelectorAll('.print-report');
    printButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            window.print();
        });
    });
    
    // Export to Excel
    const exportButtons = document.querySelectorAll('.export-excel');
    exportButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const tableId = this.dataset.table;
            const table = document.getElementById(tableId);
            const html = table.outerHTML;
            
            // Create a Blob with the HTML content
            const blob = new Blob([html], { type: 'application/vnd.ms-excel' });
            
            // Create a download link
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'laporan.xls';
            link.click();
        });
    });
    
    // Dynamic stats counter
    const statValues = document.querySelectorAll('.stat-value[data-target]');
    statValues.forEach(stat => {
        const target = +stat.dataset.target;
        const count = +stat.textContent.replace(/\D/g, '');
        if (count < target) {
            animateValue(stat, count, target, 2000);
        }
    });
});

function animateValue(obj, start, end, duration) {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        const value = Math.floor(progress * (end - start) + start);
        obj.innerHTML = value.toLocaleString('id-ID');
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    window.requestAnimationFrame(step);
}