<?php
$crypto = $_GET['crypto'] ?? 'bitcoin';

// Pour Linux
if (!$crypto) {
    http_response_code(400);
    echo "Crypto manquante.";
    exit;
}

$pidFile = "pids/producer_{$crypto}.pid";

// Vérifie si le fichier PID existe
if (file_exists($pidFile)) {
    $pid = file_get_contents($pidFile);
    
    if ($pid && posix_kill((int)$pid, 0)) { // Vérifie si le processus existe
        // Tuer le processus
        exec("kill $pid 2>&1", $output, $return_var);
        
        if ($return_var === 0) {
            unlink($pidFile); // Supprimer le fichier PID
            echo "Producteur $crypto arrêté.";
        } else {
            echo "Erreur lors de l'arrêt du producteur $crypto : " . implode("\n", $output);
        }
    } else {
        echo "Aucun processus actif pour $crypto.";
    }
} else {
    echo "Aucun producteur à arrêter pour $crypto.";
}