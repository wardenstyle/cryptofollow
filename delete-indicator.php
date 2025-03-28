<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$config = include('config.php');

if (isset($_SESSION['id_u'])) {

    header("Location: index.php");
    exit(); // Arret du script après la redirection

}else{

    try {

        $pdo = new PDO("mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset=utf8", $config['db']['user'], $config['db']['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    
        $stmt = $pdo->prepare("DELETE FROM indicators WHERE id = :id");
        $stmt->execute(['id' => $id]);
        
        
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => "Erreur SQL : " . $e->getMessage()]);
    }

}



