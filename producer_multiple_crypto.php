<?php
require 'vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Liste des cryptos à surveiller
$cryptos = ['bitcoin', 'theta-token', 'quant-network','injective-protocol'];

// Connexion RabbitMQ
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->queue_declare('crypto_prices', false, true, false, false);

foreach ($cryptos as $crypto) {
    // API URL dynamique pour chaque crypto
    $apiUrl = "https://api.coingecko.com/api/v3/coins/$crypto/market_chart?vs_currency=usd&days=1";

    $response = @file_get_contents($apiUrl); // Le @ masque les erreurs réseau
    if ($response === false) {
        echo "Erreur de récupération pour $crypto\n";
        continue;
    }

    $data = json_decode($response, true);
    if (!isset($data['prices'])) {
        echo "Format invalide pour $crypto\n";
        continue;
    }

    // Dernier prix
    $last = end($data['prices']);
    $timestamp = intval($last[0] / 1000);
    $price = $last[1];

    $msgData = json_encode([
        'crypto' => $crypto,
        'price' => $price,
        'timestamp' => $timestamp
    ]);

    $message = new AMQPMessage($msgData, ['delivery_mode' => 2]);
    $channel->basic_publish($message, '', 'crypto_prices');

    echo "Prix publié pour $crypto : $price USD (timestamp: $timestamp)\n";
}

$channel->close();
$connection->close();
?>