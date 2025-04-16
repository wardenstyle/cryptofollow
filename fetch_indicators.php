<?php

require_once 'factory.php';
existeSession();

// Vérifie si une crypto a été sélectionnée
verifyGet("crypto");
//data
$crypto = $_GET['crypto'];
$type = $_GET['type'] ?? 'all'; // Par défaut : "all"
$id_u = $_SESSION['id_u'];

// Connexion à la base de données
$config = loadConfiguration();
$pdo = connexionPDO($config);

//construction de la requête
$baseSQL = "
    SELECT indicators.*, COUNT(alerts.id) AS alert_count
    FROM indicators
    LEFT JOIN alerts ON indicators.id = alerts.id_indicator
    WHERE indicators.id_u = :id_u AND indicators.crypto = :crypto
";

$params = [
    "id_u" => $id_u,
    "crypto" => $crypto
];

//conditionnement + concaténation
if ($type === 'achat') {
    $baseSQL .= " AND indicators.type = 'achat'";
} elseif ($type === 'vente') {
    $baseSQL .= " AND indicators.type = 'vente'";
}
$baseSQL .= " GROUP BY indicators.id ORDER BY indicators.date DESC";

// GOOOOOOOOOOOO
$indicators = request_execute($pdo, $baseSQL, $params);
echo json_encode(["success" => true, "data" => $indicators]);
