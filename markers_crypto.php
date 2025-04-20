<script src="charts/chart.js"></script>
<script src="charts/moment.js"></script>
<script src="charts/chartjs-adapter-moment.js"></script>
<?php
require_once 'factory.php';

existeSession();


include('head.php');
include('nav-bar.php');

if (isset($_SESSION['id_u'])) {

    $config = loadConfiguration();
    $pdo = connexionPDO($config);
    //Data: nos crypto (remarque: on compare un id avec le nom de la crypto pour la jointure avec COLLATE)
    $cryptos = request_execute($pdo, "
    SELECT DISTINCT c.id_api
    FROM crypto c
    INNER JOIN indicators i ON i.crypto COLLATE utf8mb4_general_ci = c.id_api COLLATE utf8mb4_general_ci
    WHERE i.id_u = :id_u
    ORDER BY c.id_api ASC
", [':id_u' => $_SESSION['id_u']]);

?>

<body>

<div class="container-fluid hero-header bg-light">
    <div class="container py-3">
        <div class="row align-items-center">

            <div class="col-lg-12 col-12">
                <h5><img class="" src="img/hero-2.png" width="10%" alt=""> Sélectionnez une crypto-monnaie</h5>
                
                <form id="cryptoForm">

                        <select class="form-select" id="cryptoSelect" name="cryptoSelect">
                        <?php foreach ($cryptos as $crypto) { ?>
                                <option value="<?php echo htmlspecialchars($crypto['id_api']); ?>">
                                    <?php echo htmlspecialchars(ucwords(str_replace('-', ' ', $crypto['id_api']))); ?>
                                </option>
                            <?php } ?>
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
