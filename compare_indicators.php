<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Comparaison Indicateurs</title>
    <style>canvas { margin: auto; }</style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="js/reload_producer.js"></script>
</head>
<body>

<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);

require 'vendor/autoload.php';
$config = include 'config.php';
include('head.php');
include('nav-bar.php');

if (!isset($_SESSION['id_u'])) {
    header('Location: log-in.php');
    exit();
}

$id = $_SESSION['id_u'];
$selectedCrypto = $_GET['crypto'] ?? 'bitcoin';

try {
    $pdo = new PDO(
        "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset=utf8",
        $config['db']['user'],
        $config['db']['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $stmt = $pdo->prepare("
    SELECT 
        id,crypto,ROUND(price, 2) AS price,date,id_u,qte,type
    FROM indicators 
    WHERE id_u = :id AND crypto = :crypto AND type = 'Achat' 
    ORDER BY date DESC
    ");

$stmt->execute(['id' => $id, 'crypto' => $selectedCrypto]);
$indicators = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer prix en temps réel
    $realtime = json_decode(file_get_contents('consume_price.php'), true);
    $cryptoPrices = [];

    if (!isset($realtime['status']) || $realtime['status'] !== 'empty') {
        if (isset($realtime['data'])) {
            foreach ($realtime['data'] as $entry) {
                if (!empty($entry['symbol']) && isset($entry['price'])) {
                    $cryptoPrices[strtolower($entry['symbol'])] = $entry['price'];
                }
            }
        } elseif (isset($realtime['symbol'], $realtime['price'])) {
            $cryptoPrices[strtolower($realtime['symbol'])] = $realtime['price'];
        }
    }
    ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">
                Comparaison en temps réel des indicateurs d'achats avec le cours actuel du token <?php echo htmlspecialchars($selectedCrypto); ?>
            </h5>
            <a href="markers_crypto.php" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>

        <input type="hidden" id="crypto" value="<?php echo htmlspecialchars($selectedCrypto); ?>">
        <br>

        <canvas id="cryptoChart" height="100"></canvas>

        <?php if (empty($indicators)): ?>
            <div class="alert alert-info text-center" role="alert">
                Vous n'avez pas d'indicateur actuellement pour cette cryptomonnaie.
            </div>
        <?php else: ?>
            <table class="table table-bordered" style="width:80%; margin:0 auto; text-align:center;">
                <thead>
                    <tr>
                        <th>Prix Enregistré</th>
                        <th>Quantité</th>
                        <th>Date</th>
                        <th>Prix Actuel</th>
                        <th>Évolution</th>
                        <th>Gain/Perte</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($indicators as $indicator): ?>
                        <?php
                        $crypto = strtolower($indicator['crypto']);
                        $oldPrice = (float)$indicator['price'];
                        $currentPrice = isset($cryptoPrices[$crypto]) ? (float)$cryptoPrices[$crypto] : null;
                        $difference = ($currentPrice !== null) ? $currentPrice - $oldPrice : null;
                        $gainPerte = ($difference !== null) ? $difference * (float)$indicator['qte'] : null;
                        $color = ($gainPerte !== null && $gainPerte >= 0) ? 'green' : '#B71C1C';
                        ?>
                        <tr>
                            <td><?php echo number_format($oldPrice, 2); ?> $</td>
                            <td><?php echo number_format($indicator['qte']); ?></td>
                            <td><?php echo htmlspecialchars($indicator['date']); ?></td>
                            <td data-crypto="<?php echo $crypto; ?>">
                                <?php echo $currentPrice !== null ? number_format($currentPrice, 2) . ' $' : 'N/A'; ?>
                            </td>
                            <td class="evolution-value"
                                id="evolution-<?php echo $crypto; ?>"
                                data-old="<?php echo $oldPrice; ?>"
                                data-qte="<?php echo htmlspecialchars($indicator['qte']); ?>"
                                style="color:<?php echo $difference !== null && $difference >= 0 ? 'green' : '#B71C1C'; ?>">
                                <?php echo $difference !== null ? number_format($difference, 2) . ' $' : 'N/A'; ?>
                            </td>
                            <td class="gain-perte"
                                data-qte="<?php echo htmlspecialchars($indicator['qte']); ?>"
                                id="gain-<?php echo $crypto; ?>">
                                N/A
                            </td>
                        </tr>

                    <?php endforeach; ?>
                </tbody>
            </table>
            <table class="table table-bordered" style="width:80%; margin:0 auto; text-align:center;">
                <tr>
                    <td>Somme des Gains/Pertes :</td>
                    <td colspan="4" id="total-gain">0 $</td>
                </tr>
            </table>
        <?php endif; ?>
    </div>

    <!-- Bootstrap & FontAwesome -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>

    <!-- Real-time update & dark mode -->
    <script src="js/reload_page.js"></script>
    <script src="js/dark_mode.js"></script>

</body>
</html>

<?php
} catch (Exception $e) {
    echo "<div class='container mt-5 alert alert-danger'>Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>