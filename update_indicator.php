<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$config = include('config.php');

if (isset($_SESSION['id_u']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id']) || !isset($data['qte'])) {
        echo json_encode(["success" => false, "error" => "Données manquantes"]);
        exit;
    }

    $id = $data['id'];
    $qte = $data['qte'];

    // Vérification que l'ID est un entier valide
    if (!filter_var($id, FILTER_VALIDATE_INT)) { 
        echo json_encode(["success" => false, "error" => "ID invalide"]);
        exit;
    }

    // Vérification que qte est un décimal valide (10 chiffres max, 5 après la virgule)
    if (!preg_match('/^\d{1,5}(\.\d{1,5})?$/', $qte)) {
        echo json_encode(["success" => false, "error" => "Quantité invalide (format décimal attendu)"]);
        exit;
    }

    try {
        $pdo = new PDO(
            "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset=utf8",
            $config['db']['user'],
            $config['db']['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        // Conversion explicite en float pour éviter tout problème
        $qte = (float) $qte;

        $stmt = $pdo->prepare("UPDATE indicators SET qte = :qte WHERE id = :id");
        $stmt->execute(['qte' => $qte, 'id' => $id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "Aucune mise à jour effectuée. ID introuvable ou valeur identique."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => "Erreur SQL : " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Accès interdit"]);
    exit();   
}