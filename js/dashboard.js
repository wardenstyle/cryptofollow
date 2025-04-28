const ctx = document.getElementById('myPieChart').getContext('2d');
const chart_labels = JSON.parse(document.getElementById("chart-labels").value);
const chart_data = JSON.parse(document.getElementById("chart-data").value);
const myPieChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: chart_labels,
        datasets: [{
            label: 'Répartition crypto en %',
            data: chart_data,
            backgroundColor: [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                '#9966FF', '#FF9F40', '#66BB6A', '#BA68C8'
            ],
            borderColor: '#fff',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    color: '#333',
                    font: {
                        size: 14
                    }
                }
            }
        }
    }
});