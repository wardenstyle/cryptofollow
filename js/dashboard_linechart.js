document.addEventListener("DOMContentLoaded", function () {
    const labels = JSON.parse(document.getElementById('line-chart-labels').value);
    const gainData = JSON.parse(document.getElementById('line-chart-gain').value);
    const perteData = JSON.parse(document.getElementById('line-chart-perte').value);

    const ctx = document.getElementById('lineChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Gain',
                    data: gainData,
                    borderColor: 'rgba(76, 175, 80, 0.7)',
                    backgroundColor: 'rgba(76, 175, 80, 0.7)',
                    fill: false,
                    tension: 0.1
                },
                {
                    label: 'Perte',
                    data: perteData,
                    borderColor: 'rgba(255, 87, 34, 0.7)',
                    backgroundColor: 'rgba(255, 87, 34, 0.7)',
                    fill: false,
                    tension: 0.1
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Évolution Mensuelle : Gains vs Pertes'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Montant en $'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Mois'
                    }
                }
            }
        }
    });
});