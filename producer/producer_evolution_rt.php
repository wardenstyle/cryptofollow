<?php
require '../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Charger la config RabbitMQ
$config = include '../config.php';

// Récupération de la crypto passée en argument (ou 'bitcoin' par défaut)
$crypto = $argv[1] ?? 'bitcoin';

// Protection basique contre les caractères invalides
$crypto = preg_replace('/[^a-z0-9\-]/', '', strtolower($crypto));

if (!$crypto) {
    die("Aucune crypto fournie.\n");
}

// Construction de l'URL pour CoinGecko
$apiUrl = "https://api.coingecko.com/api/v3/coins/bitcoin/market_chart?vs_currency=usd&days=2";
echo "🔍 URL appelée : $apiUrl\n";

// Récupération des données depuis l'API
$response = @file_get_contents($apiUrl);

if (!$response) {
    die("Erreur API CoinGecko pour {$crypto} (pas de réponse)\n");
}

$data = json_decode($response, true);

if (!isset($data['prices']) || empty($data['prices'])) {
    die("Données invalides ou vides pour {$crypto}\n");
}

// Récupération du dernier prix connu
$prices = $data['prices'];
$dernier = end($prices);
$timestamp = $dernier[0]; // en millisecondes
$price = $dernier[1];

// Connexion RabbitMQ
$connection = new AMQPStreamConnection(
    $config['rabbitmq']['host'],
    $config['rabbitmq']['port'],
    $config['rabbitmq']['user'],
    $config['rabbitmq']['pass']
);
$channel = $connection->channel();
$channel->queue_declare('crypto_prices', false, true, false, false);

// Création et envoi du message
$msgData = json_encode([
    'crypto' => $crypto,
    'price' => $price,
    'timestamp' => intval($timestamp / 1000)
]);

$message = new AMQPMessage($msgData, ['delivery_mode' => 2]);
$channel->basic_publish($message, '', 'crypto_prices');

echo "Prix envoyé pour $crypto : $msgData\n";

$channel->close();
$connection->close();