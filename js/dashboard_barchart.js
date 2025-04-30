let chartInstance = null;

const cryptoColors = [
    'rgba(76, 175, 80, 0.7)',
    'rgba(244, 67, 54, 0.7)',
    'rgba(33, 150, 243, 0.7)',
    'rgba(255, 193, 7, 0.7)',
    'rgba(156, 39, 176, 0.7)',
    'rgba(255, 87, 34, 0.7)',
    'rgba(0, 188, 212, 0.7)',
    'rgba(121, 85, 72, 0.7)',
    'rgba(63, 81, 181, 0.7)',
    'rgba(205, 220, 57, 0.7)'
];

function getCryptoColor(crypto) {
    if (!cryptoColorMap[crypto]) {
        const hash = Array.from(crypto).reduce((acc, char) => acc + char.charCodeAt(0), 0);
        const hue = hash % 360; // Teinte
        const color = `hsla(${hue}, 65%, 55%, 0.7)`; // couleur pastel
        cryptoColorMap[crypto] = color;
    }
    return cryptoColorMap[crypto];
}

// Associer une couleur unique à chaque crypto
// function getCryptoColor(crypto) {
//     const keys = Object.keys(cryptoColorMap);
//     if (!cryptoColorMap[crypto]) {
//         cryptoColorMap[crypto] = cryptoColors[keys.length % cryptoColors.length];
//     }
//     return cryptoColorMap[crypto];
// }

const cryptoColorMap = {};

function groupData(data, selectedType) {
    const grouped = {};
    const labelsSet = new Set();
    const cryptosSet = new Set();

    data.forEach(entry => {
        if (selectedType !== 'Tous' && entry.type !== selectedType) return;

        const mois = entry.mois;
        const crypto = entry.crypto;
        const qte = parseFloat(entry.total_qte);
        const key = crypto;

        if (!grouped[key]) grouped[key] = {};
        grouped[key][mois] = (grouped[key][mois] || 0) + qte;

        labelsSet.add(mois);
        cryptosSet.add(crypto);
    });

    const labels = Array.from(labelsSet).sort();
    const datasets = Array.from(cryptosSet).sort().map((crypto, index) => {
        return {
            label: crypto,
            data: labels.map(label => grouped[crypto][label] || 0),
            backgroundColor: getCryptoColor(crypto),
            borderColor: getCryptoColor(crypto).replace('0.7', '1'),
            borderWidth: 1
        };
    });

    return { labels, datasets };
}

function renderChart(type = 'Tous') {
    const { labels, datasets } = groupData(barChartDataRaw, type);

    const ctx = document.getElementById('barChart').getContext('2d');
    if (chartInstance) chartInstance.destroy();

    chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            responsive: false,
            plugins: {
                title: {
                    display: true,
                    text: `Quantité par crypto / mois (${type})`
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                },
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Mois'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Quantité'
                    }
                }
            }
        }
    });
}

function filterType(type) {
    renderChart(type);
}

document.addEventListener('DOMContentLoaded', () => {
    renderChart('Tous');
});
