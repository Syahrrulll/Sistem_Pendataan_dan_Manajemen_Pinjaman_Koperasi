// Member Specific Functions
document.addEventListener('DOMContentLoaded', function() {
    // Loan calculator
    const loanForm = document.getElementById('loanForm');
    if (loanForm) {
        const amountInput = document.getElementById('amount');
        const tenorSelect = document.getElementById('tenor');
        const interestRate = 1; // 1% per month
        const totalLoanElement = document.getElementById('totalLoan');
        const monthlyPaymentElement = document.getElementById('monthlyPayment');
        
        function calculateLoan() {
            const amount = parseFloat(amountInput.value.replace(/\D/g, '')) || 0;
            const tenor = parseInt(tenorSelect.value) || 0;
            
            const totalInterest = amount * (interestRate / 100) * tenor;
            const totalLoan = amount + totalInterest;
            const monthlyPayment = totalLoan / tenor;
            
            totalLoanElement.textContent = formatMoney(totalLoan);
            monthlyPaymentElement.textContent = formatMoney(monthlyPayment);
        }
        
        function formatMoney(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        }
        
        amountInput.addEventListener('input', calculateLoan);
        tenorSelect.addEventListener('change', calculateLoan);
        
        // Initial calculation
        calculateLoan();
    }
    
    // Payment confirmation
    const paymentButtons = document.querySelectorAll('.payment-button');
    paymentButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const installmentId = this.dataset.installmentId;
            
            if (confirm('Konfirmasi pembayaran ini?')) {
                fetch(`/member/pembayaran/confirm.php?id=${installmentId}`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Pembayaran berhasil dikonfirmasi', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast('Gagal mengkonfirmasi pembayaran', 'danger');
                    }
                });
            }
        });
    });
    
    // Loan application form validation
    const applicationForm = document.getElementById('applicationForm');
    if (applicationForm) {
        applicationForm.addEventListener('submit', function(e) {
            const amount = parseFloat(document.getElementById('amount').value.replace(/\D/g, '')) || 0;
            
            if (amount < 1000000) {
                e.preventDefault();
                showToast('Jumlah pinjaman minimal Rp1.000.000', 'danger');
                document.getElementById('amount').focus();
            }
        });
    }
});