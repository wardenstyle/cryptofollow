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

// Récupérer les alertes des utilisateurs
$alerts = request_execute($pdo, "SELECT a.*, i.crypto 
    FROM alerts a 
    JOIN indicators i ON a.id_indicator = i.id 
    WHERE a.sent_at IS NULL");

$alertsGrouped = [];

// Regrouper les alertes par crypto-monnaie
foreach ($alerts as $alert) {
    $crypto = strtolower($alert['crypto']);
    if (!isset($alertsGrouped[$crypto])) {
        $alertsGrouped[$crypto] = [];
    }
    $alertsGrouped[$crypto][] = $alert;
}

// Regrouper les cryptos pour récupérer les prix en une seule requête
foreach ($alertsGrouped as $crypto => $alertsList) {
    $url = "https://api.coingecko.com/api/v3/simple/price?ids={$crypto}&vs_currencies=usd";
    $response = file_get_contents($url);
    
    if ($response === false) {
        echo "Erreur de récupération des prix pour {$crypto}\n";
        continue;
    }
    
    $data = json_decode($response, true);

    if (!isset($data[$crypto]['usd'])) {
        echo "Prix non disponible pour {$crypto}\n";
        continue;
    }

    $currentPrice = $data[$crypto]['usd'];

    // Vérifier chaque alerte pour cette crypto-monnaie
    foreach ($alertsList as $alert) {
        $shouldSend = false;

        // Déclenchement de l'alerte selon le type (achat ou vente)
        if (strtolower($alert['type']) === 'achat' && $currentPrice <= $alert['target_price']) {
            $shouldSend = true;
        }

        if (strtolower($alert['type']) === 'vente' && $currentPrice >= $alert['target_price']) {
            $shouldSend = true;
        }

        if ($shouldSend) {
            // Envoi de l'email
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

                // Mettre à jour la base de données pour marquer l'alerte comme envoyée
                request_prepared($pdo, "UPDATE alerts SET sent_at = NOW() WHERE id = :id", [":id" => $alert['id']]);
            } catch (Exception $e) {
                error_log("Erreur d'envoi : " . $mail->ErrorInfo);
            }
        }
    }
}

echo "Script exécuté à " . date('Y-m-d H:i:s') . "\n";
echo "Fin du traitement à " . date('Y-m-d H:i:s') . "\n";
?>