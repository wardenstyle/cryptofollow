<?php
// fetch_crypto.php - Restitution des données de l'api et envoi à RabbitMQ

require 'vendor/autoload.php';
require 'config.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$config = include 'config.php';
$apiUrl = "https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=usd";

//création de l'émetteur

try {
    // Récupération des données depuis CoinGecko
    $response = file_get_contents($apiUrl);
    if ($response === FALSE) {
        throw new Exception("Erreur lors de la récupération des données.");
    }
    
    $data = json_decode($response, true);
    if (!isset($data['bitcoin']['usd'])) {
        throw new Exception("Données invalides reçues.");
    }
    
    $price = $data['bitcoin']['usd'];
    $indicator = [
        'crypto' => 'bitcoin',
        'price' => $price,
        'date' => date('Y-m-d H:i:s')
    ];
    
    // Connexion à RabbitMQ
    $connection = new AMQPStreamConnection(
        $config['rabbitmq']['host'],
        $config['rabbitmq']['port'],
        $config['rabbitmq']['user'],
        $config['rabbitmq']['pass'],
    //  $config['rabbitmq']['vhost'], pour la prod
    );
    $channel = $connection->channel();
    $channel->queue_declare($config['rabbitmq']['queue'], false, true, false, false);
    
    $message = new AMQPMessage(json_encode($indicator), [
        'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
    ]);
    $channel->basic_publish($message, '', $config['rabbitmq']['queue']);
    
    echo "Données envoyées avec succès à RabbitMQ: " . json_encode($indicator) . "\n";
    
    $channel->close();
    $connection->close();
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
