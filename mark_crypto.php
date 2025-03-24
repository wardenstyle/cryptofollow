<?php
// mark_crypto.php - Envoi des marqueurs à RabbitMQ

require 'vendor/autoload.php';
require 'config.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

header('Content-Type: application/json');

// Vérifier si les données POST sont bien envoyées 
if (!isset($_POST['crypto']) || !isset($_POST['price'])|| !isset($_POST['date'])) {
    echo json_encode(['success' => false, 'error' => 'Données manquantes']);
    exit;
}

// enregistrer les marqueur dans un fichier LOG
file_put_contents('debug_mark_crypto.log', print_r($_POST, true), FILE_APPEND);

echo json_encode(['success' => true, 'message' => 'Données bien reçues']);
exit;

try {
    // Vérification des données POST , 
    if (!isset($_POST['crypto'], $_POST['price'],$_POST['date'])) {
        throw new Exception("Données incomplètes.");
    }

    $crypto = htmlspecialchars($_POST['crypto']);
    $price = floatval($_POST['price']);
    $date = $_POST['date']; // Format SQL attendu : YYYY-MM-DD HH:MM:SS
    
    // Création du marqueur
    $marker = [
        'crypto' => $crypto,
        'price' => $price,
        'date' => $date
    ];

    // Connexion à RabbitMQ
    try {
        
        $connection = new AMQPStreamConnection(
            $config['rabbitmq']['host'],
            $config['rabbitmq']['port'],
            $config['rabbitmq']['user'],
            $config['rabbitmq']['pass'],
        //  $config['rabbitmq']['vhost'], pour la prod
        );
        $channel = $connection->channel();
        $channel->queue_declare($config['rabbitmq']['queue'], false, true, false, false);
    
        $messageData = [
            'crypto' => $_POST['crypto'],
            'price' => $_POST['price'],
            'date' => $_POST['date']
        ];
        
        $message = new AMQPMessage(json_encode($messageData), [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
        ]);
        $channel->basic_publish($message, '', $config['rabbitmq']['queue']);
        echo "Données envoyées avec succès à RabbitMQ: " . json_encode($messageData) . "\n";
        echo json_encode(['success' => true, 'message' => 'Envoyé à RabbitMQ']);
    
        $channel->close();
        $connection->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Erreur RabbitMQ : ' . $e->getMessage()]);
    }

    echo json_encode(["success" => true, "message" => "Marqueur envoyé avec succès:".$messageData]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}