<?php

require_once 'factory.php';

existeSession();
$config = loadConfiguration();
$pdo = connexionPDO($config);

header('Content-Type: application/json');

// Récupérer l'ID API envoyé en POST
$id_api = $_POST['id_api'] ?? '';

// Si l'ID API est vide, retournez une réponse d'erreur
if (empty($id_api)) {
    echo json_encode(['success' => false, 'error' => 'ID API vide']);
    exit;
}

// Vérifie si la crypto est déjà présente dans la base
$check = $pdo->prepare("SELECT id_api FROM crypto WHERE id_api = :id");
$check->execute([':id' => $id_api]);
$existingCrypto = $check->fetch(PDO::FETCH_ASSOC);

if ($existingCrypto) {
    echo json_encode(["success" => false, "message" => "Cette crypto est déjà enregistrée."]);
    exit;
}

// Vérifie sur CoinGecko si l'ID API existe
$data = @file_get_contents("https://api.coingecko.com/api/v3/coins/$id_api");

if ($data === FALSE) {
    echo json_encode(['success' => false, 'error' => 'Introuvable sur CoinGecko']);
    exit;
}

// Décoder les données JSON de CoinGecko
$json = json_decode($data, true);

if (json_last_error() !== JSON_ERROR_NONE || !isset($json['id'])) {
    echo json_encode(['success' => false, 'error' => 'Réponse mal formée de CoinGecko']);
    exit;
}

// Ajoute la crypto dans la base de données
$insert = $pdo->prepare("INSERT INTO crypto (id_api) VALUES (:id)");
$insert->execute([':id' => $id_api]);

echo json_encode(["success" => true, "message" => "Crypto ajoutée avec succès !"]);

