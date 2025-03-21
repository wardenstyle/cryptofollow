<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// Déclarer une queue durable
$channel->queue_declare('task_queue', false, true, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
    echo ' [x] Received ', $msg->getBody(), "\n";
    sleep(substr_count($msg->getBody(), '.')); // Simule le temps de traitement
    echo " [x] Done\n";
    
    // Acquitter le message
    $msg->ack();
};

// Assurer une distribution équitable des tâches
$channel->basic_qos(null, 1, false);
$channel->basic_consume('task_queue', '', false, false, false, false, $callback);

try {
    $channel->consume();
} catch (\Throwable $exception) {
    echo $exception->getMessage();
}

$channel->close();
$connection->close();
