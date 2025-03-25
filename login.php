<?php
session_start();
$config = include('config.php');
// Vérifier si les données POST sont reçues
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        echo json_encode(["success" => false, "error" => "Tous les champs sont requis."]);

        exit;
    }

    try {
        $pdo = new PDO("mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset=utf8", $config['db']['user'], $config['db']['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Vérifier si l'utilisateur existe
        $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = :email");
        $stmt->execute(["email" => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['id_u'] = $user['id']; // Stocker l'ID utilisateur en session
            echo json_encode(["success" => true, "message" => "Connexion réussie !"]);
        } else {
            echo json_encode(["success" => false, "error" => "Email ou mot de passe incorrect."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => "Erreur serveur : " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Méthode non autorisée."]);
}
?>