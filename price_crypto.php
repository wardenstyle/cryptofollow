<?php 

require_once('factory.php');
existeSession();

include('head.php'); 
include('nav.php');

$config = loadConfiguration();
$pdo = connexionPDO($config);
$cryptos = request_execute($pdo, "SELECT id_api FROM crypto");

?>
<script>
    var isLoggedIn = <?php echo isset($_SESSION["id_u"]) ? "true" : "false"; ?>;
</script>

<style>
    #MsgInfo {
    font-size: 14px;
    color: red;
    margin-top: 10px;
    display: inline-block;
}
</style>

<body>

<div class="container-fluid hero-header bg-light py-5 mb-5">
    <div class="container py-5">
        <div class="row g-5 align-items-center">
            <!-- Bloc 1 : Sélection Crypto -->
            <div class="col-lg-6 col-12">
                <h2><img class="" src="img/a.png" alt="">Etape 1</h2>
                
                <h4>Sélectionnez une crypto-monnaie</h4>
                <form id="cryptoForm">
                    <div class="mb-3">
                        <select class="form-select" id="cryptoSelect">
                        <?php foreach ($cryptos as $crypto) { ?>
                                <option value="<?php echo htmlspecialchars($crypto['id_api']); ?>">
                                    <?php echo htmlspecialchars(ucwords(str_replace('-', ' ', $crypto['id_api']))); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Obtenir le prix</button>
                </form>
                <h3 class="mt-3">Prix actuel : <span id="cryptoPrice">-</span> USD</h3>
                <p>Ma crypto n'est pas dans la liste, ajoutez-la <a href="#" id="toggleAddForm">ici</a></p>
                <div id="addCryptoForm" style="display: none;">
                    <form id="newCryptoForm">
                        <label for="newCrypto">ID API de la crypto </label>
                        <input class="form-control "type="text" id="newCrypto" name="id_api" required>
                        <button class="btn btn-primary" type="submit">Ajouter</button>
                        <div id="MsgInfo" style="margin-left: 20px; display: inline-block;"></div>
                        <div id="statusMsg" class="mt-2 text-muted"></div>
                        <div id="spinnerLoader" class="text-center my-2" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php if(!isset($_SESSION['id_u'])) { ?>
            <!-- Bloc 2 : Formulaire de Connexion -->
            <div class="col-lg-6 col-12">
                <h4>Connectez-vous pour poser un indicateur</h4>
                <form id="loginForm">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" required>
                    </div>
                    <button class="btn btn-success w-100" type="submit">Se connecter</button>
                </form>
            </div>

            <?php } else { ?>
            <!-- Bloc 3 : Formulaire d'Indicateur -->
            <div class="col-lg-6 col-12">
                <h2><img class="" src="img/b.png" alt="">Etape 2</h2>
                
                <form id="indicatorForm">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                    <h4>Poser un indicateur</h4>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" id="achat" value="Achat" checked>
                            <label class="form-check-label" for="achat">Achat</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" id="vente" value="Vente">
                            <label class="form-check-label" for="vente">Vente</label>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="flex-fill">
                            <label for="crypto" class="form-label">Crypto</label>
                            <input type="text" name="crypto" class="form-control" id="crypto" readonly>
                        </div>
                        <div class="flex-fill">
                            <label for="prix" class="form-label">Prix</label>
                            <input type="text" name="price" class="form-control" id="price" required>
                        </div>

                        <div class="flex-fill">
                            <label for="qte" class="form-label">Quantité <i>(facultatif)</i></label>
                            <input type="text" name="qte" class="form-control" name="qte" id="qte">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="text" class="form-control" name="date" id="date" required> 
                    </div>

                    <input type="hidden" id="id_u" value="<?php echo $_SESSION['id_u']; ?>">

                    <div class="d-flex gap-2">
                        <button class="btn btn-success flex-fill" id="fillButton" type="button">Remplir</button>
                        <button class="btn btn-primary flex-fill" id="markButton" type="button">Marquer</button>
                    </div>
                </form>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="scripts-loader.js"></script> 
<script src="js/connexion.js"></script>
<script src="js/crypto_add.js"></script>
<script src="js/price_crypto.js"></script>

</body>

<script src="js/marquer.js"></script>
<?php include('footer.php'); ?>