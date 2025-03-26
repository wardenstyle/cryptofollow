<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('head.php');
include('nav.php');

if (isset($_SESSION['id_u'])) {

    require 'vendor/autoload.php';
    require 'config.php';
    $config = include 'config.php';

    // Vérification de la configuration
    if (!isset($config) || !is_array($config)) {
        echo json_encode(["success" => false, "error" => "Erreur : Configuration non définie."]);
        exit;
    }

    try {
        $pdo = new PDO("mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset=utf8", $config['db']['user'], $config['db']['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => "Erreur serveur : " . $e->getMessage()]);
    }

?>

<body>

<div class="container-fluid hero-header bg-light">
    <div class="container py-3">
        <div class="row align-items-center">

            <div class="col-lg-12 col-12">
                <h5><img class="" src="img/hero-2.png" width="15%" alt=""> Sélectionnez une crypto-monnaie</h5>
                
                <form id="cryptoForm">

                        <select class="form-select" id="cryptoSelect">
                            <option value="bitcoin">Bitcoin</option>
                            <option value="theta-token">Theta Token</option>
                            <option value="quant-network">Quant Network</option>
                            <option value="injective-protocol">Injective Protocol</option>
                        </select>

                    <h5>Indicateurs : <button class="btn btn-primary" id="display">Afficher le graphique</button></h5>
                    <div id="indicatorsContainer">Sélectionnez une crypto pour voir les indicateurs.</div>
                    <canvas id="cryptoChart" style="display: none; width: 100%; max-height: 400px;"></canvas>
                </form>
            </div>            
        </div>
    </div>  
</div>

<script src="scripts-loader.js"></script>
<script src="js/fetch-indicators.js"></script>

</body>

<?php 

} else {
    header('Location: log-in.php');
    exit();
}
