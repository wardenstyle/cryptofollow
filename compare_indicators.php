<!DOCTYPE html>
<?php
// compare_indicators.php - Affichage et comparaison des indicateurs
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// Masquer l'affichage des erreurs
ini_set('display_errors', 0);
require 'vendor/autoload.php';
$config = include 'config.php';

include('head.php');

if (isset($_SESSION['id_u'])) {
    $id = $_SESSION['id_u'];
    try {
        // Connexion à la base de données
        $pdo = new PDO(
            "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset=utf8",
            $config['db']['user'],
            $config['db']['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        // Récupération des indicateurs stockés
        $stmt = $pdo->prepare("SELECT * FROM indicators WHERE id_u = :id ORDER BY date DESC");
        $stmt->execute(['id' => $id]);
        $indicators = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Récupération des prix actuels
        $cryptoPrices = [];
        foreach ($indicators as $indicator) {
            $crypto = $indicator['crypto'];
            if (!isset($cryptoPrices[$crypto])) {
                $apiUrl = "https://api.coingecko.com/api/v3/simple/price?ids={$crypto}&vs_currencies=usd";
                $response = file_get_contents($apiUrl);
                $data = json_decode($response, true);
                if (isset($data[$crypto]['usd'])) {
                    $cryptoPrices[$crypto] = $data[$crypto]['usd'];
                }
            }
        }

        // Affichage de l'API Injective Protocol
        $apiUrl = "https://api.coingecko.com/api/v3/simple/price?ids=injective-protocol&vs_currencies=usd";
        $cryptoPrice = null;

        // Essayer de récupérer les données de l'API
        $response = @file_get_contents($apiUrl); // Le "@" cache l'erreur de PHP

        if ($response === FALSE) {
            $cryptoPriceError = true;
        } else {
            $data = json_decode($response, true);

            // Vérifier si la donnée est présente
            if (isset($data['injective-protocol']['usd'])) {
                $cryptoPrice = $data['injective-protocol']['usd'];
                $cryptoPriceError = false;
            } else {
                $cryptoPriceError = true;
            }
        }

?>

<nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top p-0 px-4 px-lg-5">
    <a href="index.php" class="navbar-brand d-flex align-items-center">
        <h2 class="m-0 text-primary"><img class="img-fluid me-2" src="img/icon-1.png" alt="" style="width: 45px;">CryptoFollow</h2>
    </a>
    <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto py-4 py-lg-0">
            <a href="index.php" class="nav-item nav-link active">Home</a>
        <?php if(isset($_SESSION['id_u'])) { ?>
            <a href="logout.php" class="nav-item nav-link">Déconnexion</a>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Profil</a>
                <div class="dropdown-menu shadow-sm m-0">
                    <a href="price_crypto.php" class="dropdown-item">Nouveau marqueur</a>
                    <a href="markers_crypto.php" class="dropdown-item">Mes marqueurs</a>
                </div>
            </div>
        <?php } else { ?>
            <a href="log-in.php" class="nav-item nav-link">Se connecter / s'inscrire</a>
        <?php } ?>
            <a href="#" class="nav-item nav-link">à propos de nous</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="text-center">Comparaison des indicateurs avec le cours actuel</h2>

    <?php if (isset($cryptoPriceError) && $cryptoPriceError): ?>
        <p class="error-message" style="color:red; text-align:center;">Erreur de chargement des données, veuillez réessayer plus tard.</p>
    <?php elseif ($cryptoPrice !== null): ?>
        <!-- <p class="text-center">Le prix actuel de Injective Protocol est : <strong><?php // echo $cryptoPrice; ?> USD</strong></p> -->
    <?php endif; ?>

    <table class="table table-bordered" style="width:80%; margin:0 auto; text-align:center;">
        <thead>
            <tr>
                <th>Crypto</th>
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
                    <td><?php echo htmlspecialchars($crypto); ?></td>
                    <td>$ <?php echo htmlspecialchars($oldPrice); ?></td>
                    <td><?php echo htmlspecialchars($indicator['date']); ?></td>
                    <td>$ <?php echo htmlspecialchars($currentPrice); ?></td>
                    <td style="color:<?php echo $color; ?>"><?php echo ($difference !== 'N/A' ? "$ " . htmlspecialchars($difference) : 'N/A'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Script Bootstrap et FontAwesome pour les icônes -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js"></script>

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
