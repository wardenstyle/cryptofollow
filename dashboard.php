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
    // Redirection si pas d'indicateur
    if (empty($cryptos)) {
        header('Location: price_crypto.php');
        exit();
    }

    // Récupérer la répartition des cryptos en fonction des quantités
    $repartition = request_execute($pdo, "
    SELECT 
        i.crypto, 
        SUM(i.qte * i.price) AS total_investi
    FROM indicators i
    WHERE i.id_u = :id_u
    GROUP BY i.crypto
", [':id_u' => $_SESSION['id_u']]);

    // Calcul du total général
    $total_investi = 0;
    foreach ($repartition as $r) {
        $total_investi += $r['total_investi'];
    }

    // Calcul des pourcentages
    $crypto_percentages = [];
    foreach ($repartition as $r) {
        $crypto = $r['crypto'];
        $percentage = ($r['total_investi'] / $total_investi) * 100;
        $crypto_percentages[$crypto] = round($percentage, 2);
    }

    // conversion pour Chart.js
    $chart_labels = json_encode(array_map(function($c) {
        return ucwords(str_replace('-', ' ', $c));
    }, array_keys($crypto_percentages)));

    $chart_data = json_encode(array_values($crypto_percentages));
    echo "
    <input type='hidden' id='chart-data' value='$chart_data'>
    <input type='hidden' id='chart-labels' value='$chart_labels'>
    ";

?>

<body>

<div class="container-fluid hero-header bg-light">
    <div class="container py-3">
        <div class="row align-items-center">

            <div class="col-lg-12 col-12">
                <h5>Vue d'ensemble</h5>
                
                <?php foreach ($cryptos as $crypto) { ?>
                    <?php echo htmlspecialchars(ucwords(str_replace('-', ' ', $crypto['id_api']))); ?>
                    
                <?php }?>
                <canvas id="myPieChart" style="width:200px; height:200px;"></canvas>
            </div>            
        </div>
    </div>  
</div>

<script src="js/dashboard.js"></script>
<script src="scripts-loader.js"></script>
<!-- dark mode -->
<script src="js/dark_mode.js"></script>

</body>

<?php

} else {
    header('Location: log-in.php');
    exit();
}