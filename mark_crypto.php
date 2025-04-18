<?php
// mark_crypto.php - Envoi des marqueurs à RabbitMQ
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json"); // Forcer le JSON
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

if(isset($_SESSION['id_u'])){

    require 'vendor/autoload.php';
    $config = include 'config.php';

    //Vérification de la configuration
    if (!isset($config) || !is_array($config)) {
        echo json_encode(["success" => false, "error" => "Erreur : Configuration non définie."]);
        exit;
    }

    // Vérifier que les données POST sont présentes
    if (!isset($_POST['crypto'], $_POST['price'], $_POST['date'],$_POST['id_u'],$_POST['qte'],$_POST['type'])) {
        echo json_encode(["success" => false, "error" => "Données manquantes"]);
        exit;
    }

    // Récupérer et sécuriser les données
    $crypto = htmlspecialchars($_POST['crypto']);
    $price = floatval($_POST['price']);
    $date = date("Y-m-d H:i:s", strtotime($_POST['date'])); // Format attendu : YYYY-MM-DD HH:MM:SS
    $user = $_POST['id_u'];
    $qte = $_POST['qte'];
    $type = $_POST['type'];
    // Debug : enregistrer les marqueurs dans un fichier log
    file_put_contents('debug_mark_crypto.log', print_r($_POST, true), FILE_APPEND);

    $marker = [
        'crypto' => $crypto,
        'price' => $price,
        'date' => $date,
        'id_u' => $user,
        'qte' => $qte,
        'type' => $type
    ];

    try {
        // Connexion à RabbitMQ
        $connection = new AMQPStreamConnection(
            $config['rabbitmq']['host'],
            $config['rabbitmq']['port'],
            $config['rabbitmq']['user'],
            $config['rabbitmq']['pass']
            //$config['rabbitmq']['vhost'], pour la prod
        );

        $channel = $connection->channel();
        $channel->queue_declare($config['rabbitmq']['queue'], false, true, false, false);

        $message = new AMQPMessage(json_encode($marker), [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
        ]);

        $channel->basic_publish($message, '', $config['rabbitmq']['queue']);

        // Fermer la connexion
        $channel->close();
        $connection->close();

        echo json_encode(['success' => true, 'message' => 'Message envoyé à RabbitMQ', 'data' => $marker]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Erreur RabbitMQ : ' . $e->getMessage()]);
    }

}else{
    header('Location: log-in.php');
}

