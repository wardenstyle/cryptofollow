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
    // Data: nos crypto (remarque: on compare un id avec le nom de la crypto pour la jointure avec COLLATE)
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

    // Récupérer les quantités pour chaque crypto
    $qte_par_crypto = request_execute($pdo, "
    SELECT i.crypto, SUM(i.qte) AS total_qte
    FROM indicators i
    WHERE i.id_u = :id_u AND type= 'Achat'
    GROUP BY i.crypto
    ORDER BY i.crypto ASC
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
    // données lisible pour charts
    $chart_data = json_encode(array_values($crypto_percentages));
    echo "
    <input type='hidden' id='chart-data' value='$chart_data'>
    <input type='hidden' id='chart-labels' value='$chart_labels'>
    ";

    // Récupérer les achats/ventes sur le mois pour toutes les cryptomonnaies
    $data_par_type = request_execute($pdo, "
    SELECT 
    DATE_FORMAT(date, '%Y-%m') AS mois,
    crypto,
    type,
    SUM(qte) AS total_qte
    FROM indicators
    WHERE id_u = :id_u
    GROUP BY mois, crypto, type
    ORDER BY mois ASC, crypto ASC;
    ", [':id_u' =>$_SESSION['id_u']]);

    // récupérer les ventes pour chaques crypto
    $qte_vente_par_crypto = request_execute($pdo, "
    SELECT i.crypto, SUM(i.qte) AS total_qte
    FROM indicators i
    WHERE i.id_u = :id_u AND type= 'Vente'
    GROUP BY i.crypto
    ORDER BY i.crypto ASC
", [':id_u' => $_SESSION['id_u']]);

?>

<body>
<div class="container-fluid hero-header bg-light">
    <div class="container py-3">
        <div class="row d-flex flex-wrap justify-content-between">

            <!-- Tableau Achat -->
            <div class="col-lg-4 col-md-12 mb-4">
                <h5>Achat</h5>
                <table class="table table-bordered">
                    <thead><tr><th>Cryptomonnaie</th><th>Quantité totale</th></tr></thead>
                    <tbody>
                        <?php
                        foreach ($qte_par_crypto as $crypto) {
                            $nom = ucwords(str_replace('-', ' ', $crypto['crypto']));
                            $qte = number_format($crypto['total_qte'], 2);
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($nom) . '</td>';
                            echo '<td>' . htmlspecialchars($qte) . '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Tableau Vente -->
            <div class="col-lg-4 col-md-12 mb-4">
                <h5>Vente</h5>
                <table class="table table-bordered">
                    <thead><tr><th>Cryptomonnaie</th><th>Quantité totale</th></tr></thead>
                    <tbody>
                        <?php
                        foreach ($qte_vente_par_crypto as $crypto) {
                            $nom = ucwords(str_replace('-', ' ', $crypto['crypto']));
                            $qte = number_format($crypto['total_qte'], 2);
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($nom) . '</td>';
                            echo '<td>' . htmlspecialchars($qte) . '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Camembert -->
            <div class="col-lg-4 col-md-12 d-flex justify-content-center align-items-center mb-4">
                <canvas id="myPieChart" style="width:300px; height:300px;"></canvas>
            </div>
        </div>

        <!-- Bar chart en dessous, sur toute la largeur -->
        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                <canvas id="barChart" style="width:100%; max-width:900px; height:450px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Boutons de filtre -->
    <div class="text-center py-4">
        <button class="btn btn-success" onclick="filterType('Achat')">Achat</button>
        <button class="btn btn-danger" onclick="filterType('Vente')">Vente</button>
        <button class="btn btn-warning" onclick="filterType('Tous')">Tous</button>
    </div>
</div>

<script>
    const barChartDataRaw = <?php echo json_encode($data_par_type); ?>;
</script>
<script src="js/dashboard_barchart.js"></script>
<script src="js/dashboard.js"></script>
<script src="scripts-loader.js"></script>
<script src="js/dark_mode.js"></script>
</body>


<?php

} else {
    header('Location: log-in.php');
    exit();
} 
