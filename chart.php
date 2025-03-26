<?php
// chart.php - Génération d'un graphique pour visualiser l'évolution

require 'vendor/autoload.php';
require 'config.php';

$config = include 'config.php';

try {
    // Connexion à la base de données
    $pdo = new PDO(
        "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset=utf8",
        $config['db']['user'],
        $config['db']['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Récupération des indicateurs stockés
    $stmt = $pdo->query("SELECT * FROM indicators ORDER BY date DESC");
    $indicators = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupération de l'historique des prix actuels sur 30 jours pour chaque crypto enregistrée
    $cryptoHistory = [];
    foreach ($indicators as $indicator) {
        $crypto = $indicator['crypto'];
        if (!isset($cryptoHistory[$crypto])) {
            $apiUrl = "https://api.coingecko.com/api/v3/coins/{$crypto}/market_chart?vs_currency=usd&days=30";
            $response = file_get_contents($apiUrl);
            $data = json_decode($response, true);
            if (isset($data['prices'])) {
                $cryptoHistory[$crypto] = $data['prices'];
            }
        }
    }

    // Transformation des données en JSON pour le graphique
    $chartData = [
        'indicators' => $indicators,
        'history' => $cryptoHistory
    ];
} catch (Exception $e) {
    die("Erreur: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graphique Crypto</title>
    <script src="charts/chart.js"></script>
    <script src="charts/moment.js"></script>
    <script src="charts/chartjs-adapter-moment"></script>
</head>
<body>
    <h2>Évolution des cryptos et indicateurs enregistrés</h2>
    <canvas id="cryptoChart"></canvas>

    <script>
        const chartData = <?php echo json_encode($chartData); ?>;
        const ctx = document.getElementById('cryptoChart').getContext('2d');

        let datasets = [];
        Object.keys(chartData.history).forEach(crypto => {
            let prices = chartData.history[crypto].map(item => ({ x: new Date(item[0]), y: item[1] }));
            datasets.push({
                label: `Cours ${crypto}`,
                data: prices,
                borderColor: 'blue',
                fill: false,
            });
        });

        chartData.indicators.forEach(indicator => {
            datasets.push({
                label: `Indicateur ${indicator.crypto} (${indicator.date})`,
                data: [{ x: new Date(indicator.date), y: indicator.price }],
                borderColor: 'red',
                pointRadius: 5,
                pointBackgroundColor: 'red',
            });
        });
console.log(datasets);
        new Chart(ctx, {
            type: 'line',
            data: { datasets },
            options: {
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'day',
                            tooltipFormat: 'YYYY-MM-DD'
                        }
                    },
                    y: {
                        beginAtZero: false
                    }
                }
            }
        });
        
    </script>

</body>
</html>
