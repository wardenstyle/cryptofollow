<?php
// consume_crypto.php - Consommation des messages de RabbitMQ et enregistrement en base de données

require 'vendor/autoload.php';
require 'config.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$config = include 'config.php';

// création du consommateur
try {
    // Connexion à RabbitMQ
    $connection = new AMQPStreamConnection(
        $config['rabbitmq']['host'],
        $config['rabbitmq']['port'],
        $config['rabbitmq']['user'],
        $config['rabbitmq']['pass'],
    //    $config['rabbitmq']['vhost'], // pour la prod
    );
    $channel = $connection->channel();
    $channel->queue_declare($config['rabbitmq']['queue'], false, true, false, false);

    // Connexion à MySQL

    $pdo = new PDO("mysql:host={$config['db']['host']};dbname={$config['db']['dbname']}", $config['db']['user'], $config['db']['pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]

    );

    // si l'application est fermée, les indicateurs seront enregistrés en queue et en base à la prochaine ouverture

    echo "En attente des messages...\n";

    $callback = function ($msg) use ($pdo) {
        $indicator = json_decode($msg->body, true);

        if (isset($indicator['crypto'], $indicator['price'], $indicator['date'])) {
            $stmt = $pdo->prepare("INSERT INTO indicators (crypto, price, date) VALUES (:crypto, :price, :date)");
            $stmt->execute([
                'crypto' => $indicator['crypto'],
                'price' => $indicator['price'],
                'date' => $indicator['date']
            ]);

            echo "Indicateur enregistré: " . json_encode($indicator) . "\n";
        } else {
            echo "Message invalide reçu.\n";
        }
    };

    $channel->basic_consume($config['rabbitmq']['queue'], '', false, true, false, false, $callback);

    while ($channel->is_consuming()) {
        $channel->wait();
    }

    $channel->close();
    $connection->close();
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
