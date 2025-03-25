<?php

//script de publication (publish/subscribe)
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Connexion à RabbitMQ
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// Déclaration de l'exchange "logs" en mode "fanout"
$channel->exchange_declare('logs', 'fanout', false, false, false);

// Récupération du message depuis les arguments de la ligne de commande
$data = implode(' ', array_slice($argv, 1));
if (empty($data)) {
    $data = "info: Hello World!";
}

// Création du message
$msg = new AMQPMessage($data);

// Publication du message dans l'exchange "logs"
$channel->basic_publish($msg, 'logs');

echo ' [x] Sent ', $data, "\n";

// Fermeture de la connexion
$channel->close();
$connection->close();

?>