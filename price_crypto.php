<?php 

session_start();
//session_destroy();var_dump($_SESSION);//echo password_hash("bonjour", PASSWORD_DEFAULT);
include('head.php'); 
include('nav.php'); 
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

<script> 
    //Gestion de la connexion
    document.getElementById('loginForm')?.addEventListener('submit', function(event) {
        event.preventDefault();

        let email = document.getElementById('email').value;
        let password = document.getElementById('password').value;

        // Préparation des données pour l'envoi
        let formData = new FormData();
        formData.append("email", email);
        formData.append("password", password);

        // Envoi des données à login.php
        fetch("login.php", {
            method: "POST",
            body: formData
        //    body: JSON.stringify({ email, password })
        })
        .then(response => response.text())
        .then(text => {
        console.log("Réponse brute du serveur :", text); // Affiche la réponse exacte reçue
        let data = JSON.parse(text);
        if (data.success) {
            //alert("Connexion réussi !");
            location.reload();
        } else {
            alert("Erreur : " + data.error);
        }
        })
        .catch(error => {
        console.error("Erreur lors de l'envoi :", formData);
        alert("Une erreur est survenue.");
        });

    });
    // action sur les formulaires
    if (isLoggedIn) {
    document.getElementById('cryptoForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Empêche le rechargement de la page

        let crypto = document.getElementById('cryptoSelect').value;
        let apiUrl = `https://api.coingecko.com/api/v3/simple/price?ids=${crypto}&vs_currencies=usd`;

        fetch(apiUrl)
            .then(response => response.json())
            .then(data => {
                let price = data[crypto]?.usd ?? "Prix non disponible";
                document.getElementById('cryptoPrice').textContent = price;
            })
            .catch(error => {
                document.getElementById('cryptoPrice').textContent = "Erreur lors de la récupération";
                console.error("Erreur API :", error);
            });
    });

    document.getElementById('fillButton').addEventListener('click', function() {
        let selectedCrypto = document.getElementById('cryptoSelect').value;
        let cryptoPrice = document.getElementById('cryptoPrice').textContent;
        let date = document.getElementById('date').value;

        // Remplir le nom de la crypto et son prix
        document.getElementById('crypto').value = selectedCrypto;
        document.getElementById('price').value = cryptoPrice;

        let now = new Date();
        let formattedDateSQL = now.getFullYear() + "-" +
            String(now.getMonth() + 1).padStart(2, '0') + "-" +
            String(now.getDate()).padStart(2, '0') + " " +
            String(now.getHours()).padStart(2, '0') + ":" +
            String(now.getMinutes()).padStart(2, '0') + ":" +
            String(now.getSeconds()).padStart(2, '0');

        // Insérer la date affichée dans le champ texte

        document.getElementById('date').value = formattedDateSQL;
    });


    document.getElementById('markButton').addEventListener('click', function(event) {
        event.preventDefault(); // Empêche le rechargement de la page
        console.log("Le bouton Marquer a été cliqué !");
        let crypto = document.getElementById('crypto').value;
        let price = document.getElementById('price').value;
        let date = document.getElementById('date').value;
        let user = document.getElementById('id_u').value;

        // Préparation des données pour l'envoi
        let formData = new FormData();
        formData.append("crypto", crypto);
        formData.append("price", price);
        formData.append("date", date);
        formData.append("id_u", user)

        // Envoi des données à mark_crypto.php
        fetch("mark_crypto.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(text => {
        console.log("Réponse brute du serveur :", text); // Affiche la réponse exacte reçue
        let data = JSON.parse(text);
        if (data.success) {
            alert("Marqueur envoyé avec succès !");
        } else {
            alert("Erreur : " + data.error);
        }
        })
        .catch(error => {
        console.error("Erreur lors de l'envoi :", error);
        alert("Une erreur est survenue.");
        });
    });

        console.log(document.getElementById('crypto'));
        console.log(document.getElementById('price'));
        console.log(document.getElementById('date'));
        console.log(document.getElementById('id_u'));
} else {
    console.log("L'utilisateur n'est pas connecté. Le script d'indicateur ne peut pas être exécuté.");
}
</script>

</body>

<?php include('my_script.js'); ?>
<script src="js/main.js"></script>
<?php include('footer.php'); ?>