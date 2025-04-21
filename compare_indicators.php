<!DOCTYPE html>
<style>canvas { margin: auto; }</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!--script pour lancer le producer de la scrypto seléctionné en get a interval régulier -->
<script src="js/reload_producer.js"></script>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);
require 'vendor/autoload.php';
$config = include 'config.php';
include('head.php');
include('nav-bar.php');

if (isset($_SESSION['id_u'])) {
    if (isset($_GET['crypto'])) {
        $selectedCrypto = $_GET['crypto'] ?? 'bitcoin';
    }

    $id = $_SESSION['id_u'];

    try {
        $pdo = new PDO(
            "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset=utf8",
            $config['db']['user'],
            $config['db']['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        // Récupération des indicateurs
        $stmt = $pdo->prepare("SELECT * FROM indicators WHERE id_u = :id AND crypto = :crypto AND type = 'Achat' ORDER BY date DESC");
        $stmt->execute(['id' => $id, 'crypto' => $selectedCrypto]);
        $indicators = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lire les prix en temps réel depuis RabbitMQ via consume_price.php
        $realtime = json_decode(file_get_contents('consume_price.php'), true);
        if ($realtime) {
            $crypto = $realtime['crypto'];
            $currentPrice = $realtime['price']; // Prix actuel
            $timestamp = $realtime['timestamp']; 
        } else {
            //echo "Aucune donnée reçue.";
        }        
        $cryptoPrices = [];

        if (!isset($realtime['status']) || $realtime['status'] !== 'empty') {
            if (isset($realtime['data'])) {
                foreach ($realtime['data'] as $entry) {
                    if (isset($entry['symbol']) && isset($entry['price'])) {
                        $cryptoPrices[strtolower($entry['symbol'])] = $entry['price'];
                    }
                }
            } elseif (isset($realtime['symbol']) && isset($realtime['price'])) {
                $cryptoPrices[strtolower($realtime['symbol'])] = $realtime['price'];
            }
        }
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">
        Comparaison en temps réel des indicateurs d'achats avec le cours actuel du token <?php echo $selectedCrypto; ?>
    </h5>
    <a href="markers_crypto.php" class="btn btn-primary">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
    </div>
    <input name="crypto" type="hidden" id="crypto" value="<?php echo $selectedCrypto?>">
    </br>
        <!--Graphique -->
    <canvas id="cryptoChart" height="100"></canvas>
    <!-- <script src="js/price_real_time.js"></script> -->

    <?php if (count($indicators) === 0): ?>
        <div class="alert alert-info text-center" role="alert">
            Vous n'avez pas d'indicateur actuellement pour cette cryptomonnaie.
        </div>
    <?php else: ?>
        <table class="table table-bordered" style="width:80%; margin:0 auto; text-align:center;">
            <thead>
                <tr>
                    <th>Prix Enregistré</th>
                    <th>Date</th>
                    <th>Prix Actuel</th>
                    <th>Évolution</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($indicators as $indicator): ?>
                    <?php
                        $crypto = $indicator['crypto'];
                        $oldPrice = $indicator['price'];
                        $currentPrice = $cryptoPrices[$crypto] ?? 'N/A';
                        $difference = ($currentPrice !== 'N/A') ? $currentPrice - $oldPrice : 'N/A';
                        $color = ($difference !== 'N/A' && $difference >= 0) ? 'green' : 'red';
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($oldPrice); ?>$</td>
                        <td><?php echo htmlspecialchars($indicator['date']); ?></td>
                        <td data-crypto="<?php echo $crypto; ?>"><?php echo htmlspecialchars($currentPrice); ?>$</td>
                        <td id="evolution-<?php echo $crypto; ?>" data-old="<?php echo $oldPrice; ?>" style="color:<?php echo $color; ?>">
                            <?php echo ($difference !== 'N/A' ? htmlspecialchars($difference) . " $" : 'N/A'); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Bootstrap & FontAwesome -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js"></script>

<!-- Actualisation en temps réel -->
<script src="js/reload_page.js"></script>
<!-- dark mode -->
<script src="js/dark_mode.js"></script>

</body>
</html>

<?php
    } catch (Exception $e) {
        echo "Erreur: " . $e->getMessage();
    }
} else {
    header('Location: log-in.php');
    exit();
}
?>

