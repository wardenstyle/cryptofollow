<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('head.php');
include('nav.php');

if (isset($_SESSION['id_u'])) {

    require 'vendor/autoload.php';
    require 'config.php';
    $config = include 'config.php';

    $selectedCrypto = isset($_GET['crypto']) ? htmlspecialchars($_GET['crypto']) : 'bitcoin';
    $id_u = $_SESSION['id_u'];

    // Vérification de la configuration
    if (!isset($config) || !is_array($config)) {
        echo json_encode(["success" => false, "error" => "Erreur : Configuration non définie."]);
        exit;
    }

    try {
        $pdo = new PDO("mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset=utf8", $config['db']['user'], $config['db']['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        try {
            
            $stmt = $pdo->prepare("SELECT * FROM indicators WHERE id_u = :id_u AND crypto = :crypto");
            $stmt->execute(["id_u" => $id_u, "crypto" => $selectedCrypto]);
            $indicators = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(["success" => true, "data" => $indicators]);

        } catch (PDOException $e) {
            echo json_encode(["success" => false, "error" => "Erreur lors de la récupération des indicateurs : " . $e->getMessage()]);
        }

    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => "Erreur serveur : " . $e->getMessage()]);
        exit();
    }
    
?>

<body>

<div class="container-fluid hero-header bg-light py-5 mb-5">
    <div class="container py-5">
        <div class="row g-5 align-items-center">

            <div class="col-lg-6 col-12">
                <h5><img class="" src="img/hero-2.png" width="15%" alt=""> Sélectionnez une crypto-monnaie</h5>
                <form id="cryptoForm">
                    <div class="mb-3">
                        <select class="form-select" id="cryptoSelect">
                            <option value="bitcoin" <?= $selectedCrypto === 'bitcoin' ? 'selected' : '' ?>>Bitcoin</option>
                            <option value="theta-token" <?= $selectedCrypto === 'theta-token' ? 'selected' : '' ?>>Theta Token</option>
                            <option value="quant-network" <?= $selectedCrypto === 'quant-network' ? 'selected' : '' ?>>Quant Network</option>
                            <option value="injective-protocol" <?= $selectedCrypto === 'injective-protocol' ? 'selected' : '' ?>>Injective Protocol</option>
                        </select>
                    </div>
                </form>
            </div>

            <div class="col-lg-6 col-12">
                <h5>Indicateurs :</h5>
                <div id="indicatorsContainer">Sélectionnez une crypto pour voir les indicateurs.</div>
                <?php// echo $indicators; ?>
            </div>

        </div>
    </div>
</div>

<script src="scripts-loader.js"></script>
<script src="js/fetch-indicators.js"></script>
</body>

<?php include('footer.php');

} else {
    header('Location: log-in.php');
    exit();
}
