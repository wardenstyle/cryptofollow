<script src="charts/chart.js"></script>
<script src="charts/moment.js"></script>
<script src="charts/chartjs-adapter-moment.js"></script>
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
                <h5><img class="" src="img/hero-2.png" width="10%" alt=""> Sélectionnez une crypto-monnaie</h5>
                
                <form id="cryptoForm">

                        <select class="form-select" id="cryptoSelect" name="cryptoSelect">
                            <option value="bitcoin">Bitcoin</option>
                            <option value="theta-token">Theta Token</option>
                            <option value="quant-network">Quant Network</option>
                            <option value="injective-protocol">Injective Protocol</option>
                        </select>

                        <div class="d-flex justify-content-between align-items-center mt-3 mb-2">
                            <h5 class="mb-0">Indicateurs enregistrés pour le <span id="crypto_name"></span></h5>
                                <div class="d-flex gap-2">
                                    <!-- <a href="live.php?" class="btn btn-primary" id="live" type="button"><i class="fas fa-arrow-right"></i>|En direct</a> -->
                                    <a href="compare_indicators.php?" class="btn btn-primary" id="compare" type="button"><i class="fas fa-exchange-alt"></i>|Comparateur</a>
                                    <button class="btn btn-primary" id="display" type="button"><i class="fas fa-chart-line"></i>|Graphique</button>
                                    <button class="btn btn-success" id="btnAchat" type="button">Achats</button>
                                    <button class="btn btn-danger" id="btnVente" type="button">Ventes</button>
                                    <button class="btn btn-secondary" id="btnTout" type="button">Tout</button>
                                </div>
                        </div>
                    <div id="indicatorsContainer">Sélectionnez une crypto pour voir les indicateurs.</div>
                    <canvas id="cryptoChart" style="display: none; width: 100%; max-height: 400px;"></canvas>
                    <canvas id="cryptoBarChart"></canvas>
                </form>
            </div>            
        </div>
    </div>  
</div>

<script src="scripts-loader.js"></script>
<script src="js/fetch-indicators.js"></script>
<script src="js/markers_crypto.js"></script>

</body>

<?php 

} else {
    header('Location: log-in.php');
    exit();
}
