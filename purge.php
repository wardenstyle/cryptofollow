<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$config = include 'config.php';

$connection = new AMQPStreamConnection(
    $config['rabbitmq']['host'],
    $config['rabbitmq']['port'],
    $config['rabbitmq']['user'],
    $config['rabbitmq']['pass']
);
$channel = $connection->channel();

// Purge de la file
$channel->queue_purge('crypto_prices');
echo "Queue 'crypto_prices' purgée !\n";

$channel->close();
$connection->close();