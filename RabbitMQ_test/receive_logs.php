<?php

// script de souscription (publish/subcribe)
// tous les terminals utilisants receive_logs recevront les messages émis par emi_log

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// Déclaration de l'exchange "logs" en mode "fanout"
$channel->exchange_declare('logs', 'fanout', false, false, false);

// Création d'une queue temporaire (nom aléatoire, supprimée à la déconnexion)
list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);

// Liaison de la queue à l'exchange "logs"
$channel->queue_bind($queue_name, 'logs');

echo " [*] Waiting for logs. To exit press CTRL+C\n";

// Callback pour afficher les messages reçus
$callback = function ($msg) {
    echo ' [x] ', $msg->getBody(), "\n";
};

// Consommation des messages
$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

try {
    $channel->consume();
} catch (\Throwable $exception) {
    echo $exception->getMessage();
}

// Fermeture de la connexion
$channel->close();
$connection->close();
