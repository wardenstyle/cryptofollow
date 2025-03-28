<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$config = include('config.php');

if (isset($_SESSION['id_u']) && isset($_GET['id'])) {
    $id = $_GET['id'];

    if (!filter_var($id, FILTER_VALIDATE_INT)) { 
        echo json_encode(["success" => false, "error" => "ID invalide"]);
        exit;
    }

    try {
        $pdo = new PDO(
            "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset=utf8",
            $config['db']['user'],
            $config['db']['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $stmt = $pdo->prepare("DELETE FROM indicators WHERE id = :id");
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "Aucun enregistrement supprimé. ID introuvable."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => "Erreur SQL : " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Accès interdit"]);
    header("Location: markers_crypto.php");
    exit();   
}



