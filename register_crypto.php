<?php

// clé secrete
$SECRET_KEY = 'ma-super-cle-ultrasecrete-98462';

// Autorise uniquement les POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("Méthode non autorisée");
}

// Vérifie la clé
$receivedKey = $_POST['secret'] ?? '';
if ($receivedKey !== $SECRET_KEY) {
    http_response_code(403);
    exit("Clé secrète invalide");
}

// Protection de l’entrée
$crypto = isset($_POST['crypto']) ? preg_replace('/[^a-z0-9\-]/', '', strtolower($_POST['crypto'])) : null;
if (!$crypto) {
    http_response_code(400);
    exit("Crypto manquante ou invalide");
}

// Dossier de destination
$producerDir = __DIR__ . "/producer";
if (!is_dir($producerDir)) {
    mkdir($producerDir, 0755, true); // Création récursive si nécessaire
}

// Nettoyer le nom (slug safe)
$slug = preg_replace('/[^a-z0-9\-]/', '', strtolower($crypto));
$fileName = "producer_rt_{$slug}.php";
$producerPath = "$producerDir/$fileName";

// Vérifier si le fichier existe déjà
if (file_exists($producerPath)) {
    exit("Le fichier $fileName existe déjà.");
}

// Générer le contenu du producer automatiquement
$template = <<<PHP
<?php
require '../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

\$config = include '../config.php';
\$crypto = '$crypto';
\$url = "https://api.coingecko.com/api/v3/coins/\$crypto/market_chart?vs_currency=usd&days=2";
\$response = @file_get_contents(\$url);
\$data = json_decode(\$response, true);
if (!isset(\$data['prices'])) {
    die("Erreur données CoinGecko pour \$crypto");
}
\$dernier = end(\$data['prices']);
\$timestamp = \$dernier[0];
\$price = \$dernier[1];
\$connection = new AMQPStreamConnection(
    \$config['rabbitmq']['host'],
    \$config['rabbitmq']['port'],
    \$config['rabbitmq']['user'],
    \$config['rabbitmq']['pass']
);
\$channel = \$connection->channel();
\$channel->queue_declare('crypto_prices', false, true, false, false);
\$msg = json_encode(['crypto' => \$crypto, 'price' => \$price, 'timestamp' => intval(\$timestamp / 1000)]);
\$channel->basic_publish(new AMQPMessage(\$msg, ['delivery_mode' => 2]), '', 'crypto_prices');
echo "Prix envoyé pour \$crypto : \$msg\\n";
\$channel->close();
\$connection->close();
PHP;

// Écrire le nouveau fichier producer
file_put_contents($producerPath, $template);

// Ajouter l'entrée dans le tableau de producer
$configFile = "$producerDir/producer_crypto.php";
$current = file_get_contents($configFile);

// S'assurer que le fichier existe et contient un tableau
if (!$current || strpos($current, 'return [') === false) {
    $current = "<?php\n\nreturn [\n];\n";
}

// Préparer la nouvelle ligne
$newEntry = "    '$crypto' => '$fileName',\n";

// Injecter juste avant la fin du tableau
$current = preg_replace('/(\];\s*)$/', $newEntry . '$1', $current);

// Écrire les modifications
file_put_contents($configFile, $current);

echo "Initialisation terminée";
