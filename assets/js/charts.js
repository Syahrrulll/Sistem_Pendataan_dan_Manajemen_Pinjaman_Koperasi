// Chart Initialization
document.addEventListener('DOMContentLoaded', function() {
    // Loans Chart
    const loansCtx = document.getElementById('loansChart');
    if (loansCtx) {
        // Get data from PHP variable
        const pinjamanData = window.pinjamanData || Array(12).fill(0);
        
        new Chart(loansCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Pinjaman Disetujui (juta)',
                    data: pinjamanData.map(val => val / 1000000),
                    backgroundColor: 'rgba(67, 97, 238, 0.7)',
                    borderColor: 'rgba(67, 97, 238, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += 'Rp' + context.parsed.y + ' juta';
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp' + value + 'jt';
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Loan Distribution Chart
    const distCtx = document.getElementById('loanDistribution');
    if (distCtx) {
        new Chart(distCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pendidikan', 'Usaha', 'Kesehatan', 'Rumah Tangga', 'Lainnya'],
                datasets: [{
                    data: [35, 25, 15, 15, 10],
                    backgroundColor: [
                        '#4361ee',
                        '#4cc9f0',
                        '#4895ef',
                        '#3f37c9',
                        '#f72585'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${percentage}% (${value} pinjaman)`;
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Member Payment History Chart
    const paymentCtx = document.getElementById('paymentHistory');
    if (paymentCtx) {
        new Chart(paymentCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Pembayaran per Bulan (juta)',
                    data: [1.5, 2.3, 1.8, 2.5, 2.1, 2.7, 2.5, 3.0, 2.8, 3.2, 3.0, 3.5],
                    borderColor: '#4361ee',
                    backgroundColor: 'rgba(67, 97, 238, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp' + value + 'jt';
                            }
                        }
                    }
                }
            }
        });
    }
});