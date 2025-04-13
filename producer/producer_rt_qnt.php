<?php
require '../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
$config = include '../config.php';

// Récupération du prix via API CoinGecko
$apiUrl = "https://api.coingecko.com/api/v3/coins/quant-network/market_chart?vs_currency=usd&days=2";
$response = file_get_contents($apiUrl);
$data = json_decode($response, true);

// On récupère le dernier point de la série de prix
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

// Création du message
$msgData = json_encode([
    'crypto' => 'quant-network',
    'price' => $price,
    'timestamp' => intval($timestamp / 1000)
]);

$message = new AMQPMessage($msgData, ['delivery_mode' => 2]);
$channel->basic_publish($message, '', 'crypto_prices');

echo "Prix envoyé depuis CoinGecko : $msgData\n";

$channel->close();
$connection->close();
