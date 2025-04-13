<?php
require '../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Liste des cryptos à surveiller
$cryptos = ['bitcoin', 'theta-token', 'quant-network','injective-protocol'];

// Connexion RabbitMQ
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->queue_declare('crypto_prices', false, true, false, false);

foreach ($cryptos as $crypto) {
    $apiUrl = "https://api.coingecko.com/api/v3/simple/price?ids={$crypto}&vs_currencies=usd";
    $response = @file_get_contents($apiUrl);
    
    if ($response !== false) {
        $data = json_decode($response, true);
        if (isset($data[$crypto]['usd'])) {
            $price = $data[$crypto]['usd'];
            $msgData = json_encode([
                'crypto' => $crypto,
                'price' => $price,
                'timestamp' => time()
            ]);
            $message = new AMQPMessage($msgData, ['delivery_mode' => 2]); // mode persistant
            $channel->basic_publish($message, '', 'prix_crypto');
            echo "Envoyé : $crypto => $price USD\n";
            echo "Prix envoyé depuis CoinGecko : $msgData\n";
        } else {
            echo "Erreur : réponse inattendue pour $crypto\n";
        }
    } else {
        echo "Erreur : CoinGecko inaccessible pour $crypto\n";
    }
}

$channel->close();
$connection->close();
?>