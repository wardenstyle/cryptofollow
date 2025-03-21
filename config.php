<?php
// config.php - Configuration des paramètres de connexion entre MySQL & RabbitMQ

return [
    'db' => [
        'host' => 'localhost',
        'dbname' => 'crypto_db',
        'user' => 'root',
        'pass' => ''
    ],
    'rabbitmq' => [
        'host' => 'localhost',
        'port' => 5672,
        'user' => 'guest',
        'pass' => 'guest',
        'queue' => 'crypto_indicators'
    ]
];

//@TODO

// Vérifier si l'utilisation de variables d'environnement (dotenv) est préférable pour éviter d'exposer des identifiants en clair.

// Ajouter une validation pour s'assurer que les valeurs de configuration sont bien définies avant utilisation.

// dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
// dotenv->load();

// return [
//     'db' => [
//         'host' => getenv('DB_HOST') ?: 'localhost',
//         'dbname' => getenv('DB_NAME') ?: 'crypto_db',
//         'user' => getenv('DB_USER') ?: 'root',
//         'pass' => getenv('DB_PASS') ?: ''
//     ],
//     'rabbitmq' => [
//         'host' => getenv('RABBITMQ_HOST') ?: 'localhost',
//         'port' => getenv('RABBITMQ_PORT') ?: 5672,
//         'user' => getenv('RABBITMQ_USER') ?: 'guest',
//         'pass' => getenv('RABBITMQ_PASS') ?: 'guest',
//         'queue' => getenv('RABBITMQ_QUEUE') ?: 'crypto_indicators'
//     ]
// ];
?>