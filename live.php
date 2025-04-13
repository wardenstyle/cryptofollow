<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Prix Bitcoin Live</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      margin: 30px;
    }
    canvas {
      max-width: 800px;
      margin: auto;
    }
  </style>
</head>
<body>
  <h1>📊 Prix Bitcoin en temps réel (USD)</h1>
  <canvas id="cryptoChart" height="100"></canvas>

  <script>
    const ctx = document.getElementById('cryptoChart').getContext('2d');

    const chart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: [], // horodatages
        datasets: [{
          label: 'Bitcoin (USD)',
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

    const ws = new WebSocket("ws://localhost:8080"); // adapte si besoin

    ws.onmessage = (event) => {
      const data = JSON.parse(event.data);
      console.log("Message reçu via WebSocket:", data);
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
  </script>
</body>
</html>

