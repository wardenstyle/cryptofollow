<?php //but : lire le dernier message de RabbitMQ et renvoie JSON
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

header('Content-Type: application/json');

try {
    $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
    $channel = $connection->channel();

    $channel->queue_declare('prix_crypto', false, false, false, false);

    $msg = $channel->basic_get('prix_crypto');
    if ($msg) {
        $data = json_decode($msg->body, true);
        echo json_encode($data);
        $channel->basic_ack($msg->delivery_info['delivery_tag']);
    } else {
        echo json_encode(['status' => 'empty']);
    }

    $channel->close();
    $connection->close();
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
