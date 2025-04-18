document.addEventListener('DOMContentLoaded', function() {
    // Initialize Charts
    initLoansChart();
    initMembersChart();
    
    // Add animation to stat cards
    animateStatCards();
    
    // Add click event to notifications
    document.querySelector('.notifications').addEventListener('click', function() {
        alert('You have 3 new notifications');
    });
});

function initLoansChart() {
    const ctx = document.getElementById('loansCanvas').getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Total Pinjaman (juta)',
                data: [120, 190, 150, 220, 180, 250, 300, 280, 350, 400, 380, 450],
                backgroundColor: 'rgba(67, 97, 238, 0.1)',
                borderColor: '#4361ee',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

function initMembersChart() {
    const ctx = document.getElementById('membersCanvas').getContext('2d');
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Aktif', 'Non-Aktif', 'Baru'],
            datasets: [{
                data: [150, 50, 30],
                backgroundColor: [
                    '#4cc9f0',
                    '#f72585',
                    '#f8961e'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            cutout: '70%'
        }
    });
}

function animateStatCards() {
    const statCards = document.querySelectorAll('.stat-card');
    
    statCards.forEach((card, index) => {
        // Add delay based on index for staggered animation
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('animate__animated', 'animate__fadeInUp');
    });
}