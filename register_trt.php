<?php
session_start();
include('config.php'); // Assure-toi que config.php contient la connexion à la BDD

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        // Vérifie que l'email est valide
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["success" => false, "error" => "Email invalide."]);
            exit();
        }

        // Vérifie que le mot de passe contient au moins 6 caractères
        if (strlen($password) < 6) {
            echo json_encode(["success" => false, "error" => "Le mot de passe doit contenir au moins 6 caractères."]);
            exit();
        }

        // Hash du mot de passe
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $pdo = new PDO("mysql:host=localhost;dbname=crypto_db;charset=utf8", 'root', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            // Vérifie si l'utilisateur existe déjà
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            
            if ($stmt->fetch()) {
                echo json_encode(["success" => false, "error" => "Cet email est déjà utilisé."]);
                exit();
            }

            // Insère le nouvel utilisateur
            $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (:email, :password)");
            $stmt->execute([
                'email' => $email,
                'password' => $hashedPassword
            ]);

            echo json_encode(["success" => true, "message" => "Utilisateur inscrit avec succès."]);
            
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "error" => "Erreur SQL : " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Données manquantes."]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Méthode non autorisée."]);
}
?>