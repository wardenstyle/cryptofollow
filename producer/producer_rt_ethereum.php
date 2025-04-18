<?php
require '../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$config = include '../config.php';
$crypto = 'ethereum';
$url = "https://api.coingecko.com/api/v3/coins/$crypto/market_chart?vs_currency=usd&days=2";
$response = @file_get_contents($url);
$data = json_decode($response, true);
if (!isset($data['prices'])) {
    die("Erreur données CoinGecko pour $crypto");
}
$dernier = end($data['prices']);
$timestamp = $dernier[0];
$price = $dernier[1];
$connection = new AMQPStreamConnection(
    $config['rabbitmq']['host'],
    $config['rabbitmq']['port'],
    $config['rabbitmq']['user'],
    $config['rabbitmq']['pass']
);
$channel = $connection->channel();
$channel->queue_declare('crypto_prices', false, true, false, false);
$msg = json_encode(['crypto' => $crypto, 'price' => $price, 'timestamp' => intval($timestamp / 1000)]);
$channel->basic_publish(new AMQPMessage($msg, ['delivery_mode' => 2]), '', 'crypto_prices');
echo "Prix envoyé pour $crypto : $msg\n";
$channel->close();
$connection->close();