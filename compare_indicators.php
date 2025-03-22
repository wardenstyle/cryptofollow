<?php
// compare_indicators.php - Affichage et comparaison des indicateurs

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

    // Récupération des prix actuels
    $cryptoPrices = [];
    foreach ($indicators as $indicator) {
        $crypto = $indicator['crypto'];
        if (!isset($cryptoPrices[$crypto])) {
            $apiUrl = "https://api.coingecko.com/api/v3/simple/price?ids={$crypto}&vs_currencies=usd";
            $response = file_get_contents($apiUrl);
            $data = json_decode($response, true);
            if (isset($data[$crypto]['usd'])) {
                $cryptoPrices[$crypto] = $data[$crypto]['usd'];
            }
        }
    }

    // Affichage des résultats
    echo "<h2>Comparaison des indicateurs avec le cours actuel</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Crypto</th><th>Prix Enregistré</th><th>Date</th><th>Prix Actuel</th><th>Évolution</th></tr>";
    foreach ($indicators as $indicator) {
        $crypto = $indicator['crypto'];
        $oldPrice = $indicator['price'];
        $currentPrice = $cryptoPrices[$crypto] ?? 'N/A';
        $difference = ($currentPrice !== 'N/A') ? $currentPrice - $oldPrice : 'N/A';
        $color = ($difference !== 'N/A' && $difference >= 0) ? 'green' : 'red';

        echo "<tr>";
        echo "<td>{$crypto}</td>";
        echo "<td>\$ {$oldPrice}</td>";
        echo "<td>{$indicator['date']}</td>";
        echo "<td>\$ {$currentPrice}</td>";
        echo "<td style='color:{$color}'>" . ($difference !== 'N/A' ? "\$ {$difference}" : 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage();
}
