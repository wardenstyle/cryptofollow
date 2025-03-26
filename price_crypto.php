<?php 

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
//session_destroy();var_dump($_SESSION);//echo password_hash("bonjour", PASSWORD_DEFAULT);
include('head.php'); 
include('nav.php');
//var_dump($_SESSION['id_u']);
?>
<script>
    var isLoggedIn = <?php echo isset($_SESSION["id_u"]) ? "true" : "false"; ?>;
</script>

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
                            <option value="bitcoin">Bitcoin</option>
                            <option value="theta-token">Theta Token</option>
                            <option value="quant-network">Quant Network</option>
                            <option value="injective-protocol">Injective Protocol</option>
                        </select>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Obtenir le prix</button>
                </form>
                <h3 class="mt-3">Prix actuel : <span id="cryptoPrice">-</span> USD</h3>
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
                <h4>Poser un indicateur</h4>
                <form id="indicatorForm">
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
                            <label for="qte" class="form-label">Quantité</label>
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
<script src="js/connexion.js"></script>
<script src="js/price_crypto.js"></script>
<script src="scripts-loader.js"></script> 
</body>

<script src="js/marquer.js"></script>
<?php include('footer.php'); ?>