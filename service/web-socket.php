<?php
require '../vendor/autoload.php';
$config = include '../config.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class CryptoStream implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "Nouvelle connexion WebSocket : {$conn->resourceId}\n";
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Connexion fermée : {$conn->resourceId}\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Erreur : " . $e->getMessage() . "\n";
        $conn->close();
    }

    public function onMessage(ConnectionInterface $from, $msg) {
    // réponse coté client ici
    }

    public function pushToClients($message) {
        foreach ($this->clients as $client) {
            $client->send($message);
        }
    }
}

$app = new CryptoStream();

// WebSocket + RabbitMQ
$loop = React\EventLoop\Factory::create();
$webSock = new React\Socket\SocketServer('0.0.0.0:8080', [], $loop);
$webServer = new Ratchet\Server\IoServer(
    new Ratchet\Http\HttpServer(
        new Ratchet\WebSocket\WsServer($app)
    ),
    $webSock,
    $loop
);

// Connexion à RabbitMQ
$amqpConnection = new AMQPStreamConnection(
    $config['rabbitmq']['host'],
    $config['rabbitmq']['port'],
    $config['rabbitmq']['user'],
    $config['rabbitmq']['pass']
);
$channel = $amqpConnection->channel();
$channel->queue_declare('crypto_prices', false, true, false, false);

// Chaque message reçu → envoi via WebSocket
$channel->basic_consume('crypto_prices', '', false, true, false, false, function($msg) use ($app) {
    $app->pushToClients($msg->body);
});

$loop->addPeriodicTimer(0.1, function() use ($channel) {
    $channel->wait(null, true);
});

echo "WebSocket Server + RabbitMQ prêt sur ws://localhost:8080\n";
$loop->run();
