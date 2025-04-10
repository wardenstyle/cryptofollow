<?php
// consume_crypto.php - Consommation des messages de RabbitMQ et enregistrement en base de données
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die("Accès interdit.");
}
//ajouter ceci dans le .htaccess pour interdire l'accès à ce script
// <Files "consume_crypto.php">
//     Order Deny,Allow
//     Deny from all
// </Files>

require 'vendor/autoload.php';
require 'config.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$config = include 'config.php';

function connectRabbitMQ($config) {
    try {
        $connection = new AMQPStreamConnection(
            $config['rabbitmq']['host'],
            $config['rabbitmq']['port'],
            $config['rabbitmq']['user'],
            $config['rabbitmq']['pass']
        );
        $channel = $connection->channel();
        $channel->queue_declare($config['rabbitmq']['queue'], false, true, false, false);

        return [$connection, $channel];
    } catch (Exception $e) {
        echo "Erreur de connexion à RabbitMQ: " . $e->getMessage() . "\n";
        return [null, null];
    }
}

function connectDatabase($config) {
    try {
        return new PDO(
            "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']}",
            $config['db']['user'],
            $config['db']['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    } catch (Exception $e) {
        echo "Erreur de connexion à la base de données: " . $e->getMessage() . "\n";
        return null;
    }
}

[$connection, $channel] = connectRabbitMQ($config);
$pdo = connectDatabase($config);

if (!$connection || !$channel || !$pdo) {
    exit("Impossible de démarrer le consommateur.\n");
}

echo "En attente des messages...\n";

$callback = function (AMQPMessage $msg) use ($pdo) {
$indicator = json_decode($msg->body, true);

    if (isset($indicator['crypto'], $indicator['price'], $indicator['date'], $indicator['id_u'], $indicator['qte'], $indicator['type'])) {
        try {
            $stmt = $pdo->prepare("INSERT INTO indicators (crypto, price, date, id_u, qte, type) VALUES (:crypto, :price, :date, :id_u, :qte, :type)");
            $stmt->execute([
                'crypto' => $indicator['crypto'],
                'price' => $indicator['price'],
                'date' => $indicator['date'],
                'id_u' => intval($indicator['id_u']),
                'qte' => $indicator['qte'],
                'type' => $indicator['type']
            ]);

            echo "Indicateur enregistré: " . json_encode($indicator) . "\n";
            $msg->ack(); // Accuser réception du message

        } catch (Exception $e) {
            echo "Erreur lors de l'insertion en base: " . $e->getMessage() . "\n";
        }
    } else {
        echo "Message invalide reçu.\n";
        $msg->reject(false); // Rejeter le message sans le remettre en file d'attente
    }
};

// Mode "fair dispatch" pour éviter de traiter plusieurs messages à la fois si le serveur est lent
$channel->basic_qos(null, 1, null);
$channel->basic_consume($config['rabbitmq']['queue'], '', false, false, false, false, $callback);

while ($channel->is_consuming()) {
    try {
        $channel->wait();
    } catch (Exception $e) {
        echo "Erreur pendant la consommation: " . $e->getMessage() . "\n";
        sleep(5);
    }
}

$channel->close();
$connection->close();