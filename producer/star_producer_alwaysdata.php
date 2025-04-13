<?php
$crypto = $_GET['crypto'] ?? 'bitcoin';

$producers = [
    'bitcoin' => 'producer_rt_btc.php',
    'theta-token' => 'producer_rt_theta.php',
    'injective-protocol' => 'producer_rt_inj.php',
    'quant-network' => 'producer_rt_qnt.php'
];

if (!array_key_exists($crypto, $producers)) {
    http_response_code(400);
    echo "Crypto non reconnue.";
    exit;
}

// Chemin ABSOLU correct basé sur le dossier actuel
$scriptPath = __DIR__ . '/' . $producers[$crypto];

if (!file_exists($scriptPath)) {
    http_response_code(500);
    echo "Script producteur introuvable : $scriptPath";
    exit;
}

$cmd = "php " . escapeshellarg($scriptPath) . " > /dev/null 2>&1 & echo $!";
$pid = shell_exec($cmd);

echo "Producteur lancé pour $crypto (PID: $pid)";