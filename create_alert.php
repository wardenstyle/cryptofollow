<?php

require_once 'factory.php';

existeSession();

if (isset($_SESSION['id_u'])) {

    include('head.php');
    include('nav-bar.php');
    
    $config = loadConfiguration();
    $pdo = connexionPDO($config);
    verifyPostv2('indicator_id');
    // restitution de l'indicateur
    $id_indicator = $_POST['indicator_id'];
    $sql = "SELECT * FROM indicators WHERE id = :id";
    $params = [':id' => $id_indicator];
    $indicator = request_execute($pdo, $sql, $params, true);

    // restitution des alerts
    $sql1 = "SELECT id, id_indicator, target_price, type, created_at, email, percentage_ FROM alerts WHERE id_indicator = :id";
    $params1 = [':id' => $id_indicator];
    $alerts = request_execute($pdo, $sql1, $params1, false);
   //var_dump($alerts);
    if (!$indicator) {
        echo "<div class='alert alert-danger'>Indicateur introuvable</div>";
        exit;
    }

} else {
    header('Location: log-in.php');
    exit();
}
?>
<div class="container mt-4">
    <div class="row">
        <!-- Carte de l'indicateur -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    Détails de l'indicateur
                </div>
                <div class="card-body">
                    <p><strong>Cryptomonnaie :</strong> <?= htmlspecialchars($indicator['crypto']) ?></p>
                    <p><strong>Prix :</strong> <span id="originalPrice"><?= htmlspecialchars($indicator['price']) ?></span> $</p>
                    <p><strong>Date :</strong> <?= htmlspecialchars($indicator['date']) ?></p>
                    <p><strong>Type :</strong> <?= htmlspecialchars($indicator['type']) ?></p>
                    <p><strong>Quantité :</strong> <?= htmlspecialchars($indicator['qte']) ?></p>
                </div>
            </div>
            <a href="markers_crypto.php" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
        </div>

        <!-- Curseur & enregistrement -->
        <div class="col-md-6">
            <form id="alertForm">
                <input type="hidden" name="indicator_id" value="<?= $indicator['id'] ?>">

                <label for="percentageRange" class="form-label">Alerte à (en % du prix) :</label>
                <input type="range" class="form-range" id="percentageRange" name="percentage" min="-20" max="20" value="0" step="1">
                <p>Variation sélectionnée : <span id="selectedPercentage">0</span>%</p>
                <p>Prix cible : <span id="targetPrice"><?= htmlspecialchars($indicator['price']) ?></span> $</p>
                <p>Email : <input class="form-control" type="email" name="alert_email" id="alert_email" required></p>

                <label class="form-label mt-3">Type d'alerte :</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="alert_type" id="achat" value="Achat" checked>
                    <label class="form-check-label" for="achat">Achat</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="alert_type" id="vente" value="Vente">
                    <label class="form-check-label" for="vente">Vente</label>
                </div>

                <button type="submit" class="btn btn-success mt-3">Enregistrer l'alerte</button>
                <div id="alertResponse" class="mt-2"></div>
            </form>
        </div>

        <?php if (!empty($alerts)) { ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th style="display:none">ID</th> 
                    <th>Prix cible</th>
                    <th>%</th>
                    <th>Type d'alerte</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($alerts as $alert) {?>
                <tr data-alert-id="<?= htmlspecialchars($alert['id']) ?>">
                    <td><?php echo htmlspecialchars($alert['target_price']);?></td>
                    <td><?php echo htmlspecialchars($alert['percentage_']);?></td>
                    <td><?php echo htmlspecialchars($alert['type']);?></td>
                    <td><?php echo htmlspecialchars($alert['email']);?></td>
                    <td>
                    <button class="btn btn-sm btn-danger delete-alert" title="Supprimer">
                        <i class="fas fa-trash"></i>
                    </button>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

        <?php }?>

    </div>

</div>
<script src="js/create_alert.js"></script>
<script src="js/delete_alert.js"></script>