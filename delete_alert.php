<?php
require_once 'factory.php';

secureAjaxSession();
verifyPost('alert_id');

$config = loadConfiguration();
$pdo = connexionPDO($config);

// Data
$alertId = $_POST['alert_id'];
$userId = $_SESSION['id_u'];

// vérification que l'alerte appartient bien à l'utilisateur
$checkSQL = "SELECT id FROM alerts WHERE id = :id AND id IN (
    SELECT a.id FROM alerts a 
    JOIN indicators i ON a.id_indicator = i.id 
    WHERE i.id_u = :user_id
)";
$result = request_execute($pdo, $checkSQL, [
    ':id' => $alertId,
    ':user_id' => $userId
], true);

if (!$result) {
    echo json_encode(["success" => false, "error" => "Alerte introuvable ou accès non autorisé."]);
    exit;
}

$deleteSQL = "DELETE FROM alerts WHERE id = :id";
request_prepared($pdo, $deleteSQL, [':id' => $alertId]);

echo json_encode(["success" => true, "message" => "Alerte supprimée avec succès."]);