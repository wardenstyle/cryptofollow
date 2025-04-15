<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'factory.php';
existeSession();
$config = loadConfiguration();
$pdo = connexionPDO($config);

verifyPost('indicator_id', 'target_price', 'alert_type', 'content','email','percentage_');

$id_u = $_SESSION['id_u'];
$indicator_id = $_POST['indicator_id'];
$target_price = $_POST['target_price'];
$alert_type = $_POST['alert_type'];
$content = $_POST['content'];
$email = $_POST['email'];
$percentage = $_POST['percentage_'];

$sql = "INSERT INTO alerts (id_indicator, target_price, type, content, email, percentage_) 
        VALUES (:indicator_id, :target_price, :type, :content, :email, :percentage_)";
$params = [
    ':indicator_id' => $indicator_id,
    ':target_price' => $target_price,
    ':type' => $alert_type,
    ':content' => $content,
    ':email' => $email,
    ':percentage_' => $percentage
];

// Vérifier le nombre d'alertes existantes pour cet utilisateur et cet indicateur
$checkSql = "SELECT COUNT(*) FROM alerts WHERE id_indicator = :indicator_id";
$checkStmt = $pdo->prepare($checkSql);
$checkStmt->execute([
    ':indicator_id' => $indicator_id
]);
$alertCount = $checkStmt->fetchColumn();

if ($alertCount >= 5) {
    echo "Vous avez déjà enregistré 5 alertes pour cet indicateur.";
    exit;
}

try {
    request_execute($pdo, $sql, $params);
    echo "Alerte de type <strong>$alert_type</strong> enregistrée avec succès.";
} catch (Exception $e) {
    echo "Erreur lors de l'enregistrement : " . $e->getMessage();
}