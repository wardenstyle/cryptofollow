<?php
// // Récupère la crypto depuis les paramètres GET
// $crypto = $_GET['crypto'] ?? 'bitcoin';

// // Tableau associatif des scripts producteurs
// $producers = [
//     'bitcoin' => 'producer_rt_btc.php',
//     'theta-token' => 'producer_rt_theta.php',
//     'injective-protocol'=> 'producer_rt_inj.php',
//     'quant-network'=>'producer_rt_qnt.php'
//     // Ajoute d'autres cryptos ici si nécessaire
// ];

// if (!$crypto || !array_key_exists($crypto, $producers)) {
//     http_response_code(400);
//     echo "Crypto non reconnue.";
//     exit;
// }

// // Nom du script à exécuter
// $script = escapeshellcmd($producers[$crypto]);

// // Lancer le script en arrière-plan et stocker le PID
// $cmd = "php /$script > /dev/null 2>&1 & echo $!";
// $pid = shell_exec($cmd);

// // Sauvegarder le PID par crypto (ex: producer_bitcoin.pid)
// file_put_contents("pids/producer_{$crypto}.pid", $pid);

// pour windows :

$crypto = $_GET['crypto'] ?? 'bitcoin';

// $producers = [
//     'bitcoin' => 'producer_rt_btc.php',
//     'theta-token' => 'producer_rt_theta.php',
//     'injective-protocol'=> 'producer_rt_inj.php',
//     'quant-network'=>'producer_rt_qnt.php'
// ];
$producers = include 'producer_crypto.php';

if (!array_key_exists($crypto, $producers)) {
    http_response_code(400);
    echo "Crypto non reconnue.";
    exit;
}

$script = $producers[$crypto];
$cmd = "start /B php " . escapeshellarg($script);
pclose(popen($cmd, "r"));

echo "Lecture lancé pour $crypto";

// Sauvegarde du PID dans un fichier
//file_put_contents("pids/producer_{$crypto}.pid", $pid);

// $crypto = $_GET['crypto'] ?? 'bitcoin';

// // Protection basique
// $crypto = preg_replace('/[^a-z0-9\-]/', '', strtolower($crypto));

// // Le chemin vers le producteur unique
// $script = 'producer_evolution.php';

// // Exécute le script en arrière-plan avec l’argument GET simulé
// $cmd = "start /B php " . escapeshellarg($script) . " " . escapeshellarg($crypto);
// pclose(popen($cmd, "r"));

// echo "Lecture lancée pour $crypto";


