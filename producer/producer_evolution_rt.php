<?php
require '../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Crypto choisie dynamiquement (via URL ?crypto=bitcoin)
$crypto = isset($_GET['crypto']) ? htmlspecialchars($_GET['crypto']) : 'bitcoin';

if (!$crypto) {
    die("Aucune crypto fournie");
}

// Protection basique contre les caractères invalides
$crypto = preg_replace('/[^a-z0-9\-]/', '', strtolower($crypto));

$apiUrl = "https://api.coingecko.com/api/v3/coins/{$crypto}/market_chart?vs_currency=usd&days=2";
$response = @file_get_contents($apiUrl);

if (!$response) {
    die("Erreur API CoinGecko pour {$crypto}");
}

$data = json_decode($response, true);
if (!isset($data['prices'])) {
    die("Données invalides pour {$crypto}");
}

$dernier = end($data['prices']);
$timestamp = $dernier[0]; // ms
$price = $dernier[1];

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->queue_declare('crypto_prices', false, true, false, false);

$msgData = json_encode([
    'crypto' => $crypto,
    'price' => $price,
    'timestamp' => intval($timestamp / 1000)
]);

$message = new AMQPMessage($msgData, ['delivery_mode' => 2]);
$channel->basic_publish($message, '', 'crypto_prices');

echo "Prix envoyé pour $crypto : $msgData";

$channel->close();
$connection->close();
