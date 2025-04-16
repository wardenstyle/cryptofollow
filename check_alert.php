<?php
require_once 'factory.php';
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

existeSession();
$config = loadConfiguration();
$pdo = connexionPDO($config);

// Restitution des alerts des utilsateurs
$alerts = request_execute($pdo, "SELECT a.*, i.crypto 
    FROM alerts a 
    JOIN indicators i ON a.id_indicator = i.id 
    WHERE a.sent_at IS NULL");

$alertsGrouped = [];

// Regrouper les cryptos pour optimiser les appels à l'API
foreach ($alerts as $alert) {
    $crypto = strtolower($alert['crypto']);
    if (!isset($alertsGrouped[$crypto])) {
        $alertsGrouped[$crypto] = [];
    }
    $alertsGrouped[$crypto][] = $alert;
}

// regroupement des cryptos
foreach ($alertsGrouped as $crypto => $alertsList) {
    $url = "https://api.coingecko.com/api/v3/simple/price?ids={$crypto}&vs_currencies=usd";
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if (!isset($data[$crypto]['usd'])) {
        continue;
    }
    $currentPrice = $data[$crypto]['usd'];

    foreach ($alertsList as $alert) {
        $shouldSend = false;

        if (strtolower($alert['type']) === 'achat' && $currentPrice <= $alert['target_price']) {
            $shouldSend = true;
        }

        if (strtolower($alert['type']) === 'vente' && $currentPrice >= $alert['target_price']) {
            $shouldSend = true;
        }

        if ($shouldSend) {
            // Envoi email
            $mailConfig = include 'mail_config.php';
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = $_ENV['MAIL_USERNAME'];
                $mail->Password = $_ENV['MAIL_PASSWORD'];
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('no-reply@cryptofollow.group', 'CryptoFollow Alert System');
                $mail->addAddress($alert['email']);
                $mail->Subject = "Alerte Crypto : seuil atteint pour {$crypto}";
                $mail->Body = "Le prix actuel de {$crypto} est de \${$currentPrice}.\n
                Votre seuil de {$alert['type']} était fixé à \${$alert['target_price']}.";

                $mail->send();

                // Marquer comme envoyé
                request_prepared($pdo, "UPDATE alerts SET sent_at = NOW() WHERE id = :id", [":id" => $alert['id']]);
            } catch (Exception $e) {
                error_log("Erreur d'envoi : " . $mail->ErrorInfo);
            }
        }
    }
}
echo "Script exécuté à " . date('Y-m-d H:i:s') . "\n";
var_dump($alerts);
var_dump($alertsGrouped);
var_dump($shouldSend);
echo "Fin du traitement à " . date('Y-m-d H:i:s') . "\n";