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

try {
    request_execute($pdo, $sql, $params);
    echo "Alerte de type <strong>$alert_type</strong> enregistrée avec succès.";
} catch (Exception $e) {
    echo "Erreur lors de l'enregistrement : " . $e->getMessage();
}