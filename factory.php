<?php
// vérification Ajax utilisateur
function secureAjaxSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Vérifie que la requête est AJAX
    if (
        !isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest'
    ) {
        http_response_code(403);
        die("Accès interdit.");
    }

    // Vérifie que l'utilisateur est connecté
    if (!isset($_SESSION['id_u'])) {
        http_response_code(403);
        die("Session utilisateur manquante.");
    }
}

//vérification de l'existence de la session 
function existeSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function loadConfiguration() {
    $config = include 'config.php';

    if (!isset($config) || !is_array($config)) {
        echo json_encode(["success" => false, "error" => "Erreur : Configuration non définie."]);
        exit;
    }

    return $config;
}

// verifier les données POST
function verifyPost(...$champs) {
    foreach ($champs as $champ) {
        if (!isset($_POST[$champ])) {
            echo json_encode(["success" => false, "error" => "Donnée POST manquante : $champ"]);
            exit;
        }
    }
}

//verifie POST avec redirection
function verifyPostv2(...$requiredFields) {
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field])) {
            http_response_code(400);
            // Redirection après un petit délai pour laisser apparaître le message
            echo "<h5 style='color:red;'>Erreur : Indicateur manquant</h5>";
            echo "<p>Redirection dans 2 secondes...</p>";
            header("refresh:2;url=markers_crypto.php");
            exit;
        }
    }
}

//vérifier les données GET
function verifyGet(...$champs) {
    foreach ($champs as $champ) {
        if (!isset($_GET[$champ])|| empty($_GET[$champ])) {
            echo json_encode(["success" => false, "error" => "Donnée GET manquante : $champ"]);
            exit;
        }
    }
}

// connexion PDO
function connexionPDO($config) {
    try {
        return new PDO(
            "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset=utf8",
            $config['db']['user'],
            $config['db']['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => "Erreur serveur : " . $e->getMessage()]);
        exit;
    }
}

// requête préparé pour les insert
function request_prepared($pdo, $sql, $params = []) {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => "Erreur SQL : " . $e->getMessage()]);
        exit;
    }
}

// requête pour les select exemple : $user = executerRequete($pdo, "SELECT * FROM users WHERE id = :id", [':id' => 1], true);
function request_execute($pdo, $sql, $params = [], $unique = false, $fetchMode = PDO::FETCH_ASSOC) {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $unique ? $stmt->fetch($fetchMode) : $stmt->fetchAll($fetchMode);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => "Erreur SQL : " . $e->getMessage()]);
        exit;
    }
}
?>
