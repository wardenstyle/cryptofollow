let crypto = document.getElementById("crypto").value;
const ctx = document.getElementById('cryptoChart').getContext('2d');
if (chart) {
  chart.destroy();
}
    const chart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: [], // horodatages
        datasets: [{
          label: crypto+'(USD)',
          data: [],
          borderColor: 'orange',
          backgroundColor: 'rgba(255,165,0,0.2)',
          tension: 0.2,
          pointRadius: 3,
        }]
      },
      options: {
        responsive: true,
        scales: {
          x: { title: { display: true, text: 'Heure' } },
          y: { title: { display: true, text: 'Prix (USD)' } }
        }
      }
    });

    const ws = new WebSocket("ws://localhost:8080");

    ws.onmessage = (event) => {
      const data = JSON.parse(event.data);
      const heure = new Date(data.timestamp * 1000).toLocaleTimeString();

      // On limite à 20 points
      if (chart.data.labels.length >= 20) {
        chart.data.labels.shift();
        chart.data.datasets[0].data.shift();
      }

      chart.data.labels.push(heure);
      chart.data.datasets[0].data.push(data.price.toFixed(2));
      chart.update();
    };
