<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'config.php';
$config = include 'config.php';

// Vérification de la configuration
if (!isset($config) || !is_array($config)) {
    echo json_encode(["success" => false, "error" => "Erreur : Configuration non définie."]);
    exit;
}

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset=utf8", $config['db']['user'], $config['db']['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Erreur serveur : " . $e->getMessage()]);
    exit;
}

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['id_u'])) {
    echo json_encode(["success" => false, "error" => "Utilisateur non connecté"]);
    exit;
}

// Vérifie si une crypto a été sélectionnée
if (!isset($_GET['crypto']) || empty($_GET['crypto'])) {
    echo json_encode(["success" => false, "error" => "Aucune crypto sélectionnée"]);
    exit;
}

$crypto = $_GET['crypto'];
$id_u = $_SESSION['id_u'];

try {
    // Préparation et exécution de la requête
    $stmt = $pdo->prepare("SELECT * FROM indicators WHERE id_u = :id_u AND crypto = :crypto");
    $stmt->execute(["id_u" => $id_u, "crypto" => $crypto]);
    $indicators = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retourne les résultats en JSON
    echo json_encode(["success" => true, "data" => $indicators]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Erreur lors de la récupération des indicateurs : " . $e->getMessage()]);
}
?>