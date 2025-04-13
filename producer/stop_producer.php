<?php
$crypto = $_GET['crypto'] ?? 'bitcoin';

// pour linux
// if (!$crypto) {
//     http_response_code(400);
//     echo "Crypto manquante.";
//     exit;
// }

// $pidFile = "pids/producer_{$crypto}.pid";

// if (file_exists($pidFile)) {
//     $pid = file_get_contents($pidFile);
//     if ($pid) {
//         exec("kill $pid");
//     }
//     unlink($pidFile);
// }

//pour windows
$crypto = $_GET['crypto'] ?? 'bitcoin';

// Vérifie si le fichier PID existe
$pidFile = "pids/producer_{$crypto}.pid";
if (file_exists($pidFile)) {
    $pid = file_get_contents($pidFile);
    
    // Arrête le processus avec taskkill sur Windows
    $cmd = "taskkill /F /PID $pid";
    shell_exec($cmd);
    
    // Supprime le fichier PID
    unlink($pidFile);
    
    echo "Producteur $crypto arrêté.";
} else {
    echo "Aucun producteur à arrêter pour $crypto.";
}
