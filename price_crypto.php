<?php include('head.php'); ?>
<?php include('nav.php'); ?>
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

            <?php// if(isset($_SESSION)) { ?>
            <!-- Bloc 2 : Formulaire de Connexion -->
            <!-- <div class="col-lg-6 col-12">
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
            </div> -->

            <?php// } else { ?>
            <!-- Bloc 3 : Formulaire d'Indicateur -->
            <div class="col-lg-6 col-12">
                <h2><img class="" src="img/b.png" alt="">Etape 2</h2>
                <h4>Poser un indicateur</h4>
                <form id="indicatorForm">
                    <div class="d-flex gap-2">
                        <div class="flex-fill">
                            <label for="crypto" class="form-label">Crypto</label>
                            <input type="text" class="form-control" id="crypto" readonly>
                        </div>
                        <div class="flex-fill">
                            <label for="prix" class="form-label">Prix</label>
                            <input type="text" class="form-control" id="price" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="text" class="form-control" name="date" id="date" value="2025-12-12 00:00:00" required> 
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-success flex-fill" id="fillButton" type="button">Remplir</button>
                        <button class="btn btn-primary flex-fill" id="markButton" type="button">Marquer</button>
                    </div>
                </form>
            </div>
            <?php// } ?>
        </div>
    </div>
</div>

<script> // action sur les formulaires
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

        // Remplir le nom de la crypto et son prix
        document.getElementById('crypto').value = selectedCrypto;
        document.getElementById('price').value = cryptoPrice;

        // Obtenir la date et l'heure actuelles
//        let now = new Date();

        // Format d'affichage pour le formulaire (JJ/MM/AAAA HH:MM)
        // let formattedDateDisplay = now.toLocaleString('fr-FR', {
        //     day: '2-digit', month: '2-digit', year: 'numeric',
        //     hour: '2-digit', minute: '2-digit'
        // });

        // Traitement de date:  Format SQL DATETIME (AAAA-MM-JJ HH:MM:SS)

        // let formattedDateSQL = now.getFullYear() + "-" +
        //     String(now.getMonth() + 1).padStart(2, '0') + "-" +
        //     String(now.getDate()).padStart(2, '0') + " " +
        //     String(now.getHours()).padStart(2, '0') + ":" +
        //     String(now.getMinutes()).padStart(2, '0') + ":" +
        //     String(now.getSeconds()).padStart(2, '0');

        // Insérer la date affichée dans le champ texte
        //document.getElementById('date_display').value = formattedDateDisplay;
        // Insérer la date SQL cachée pour l'envoi au serveur
        //document.getElementById('date').value = formattedDateSQL;
    });

</script>

<script>
    document.getElementById('markButton').addEventListener('click', function(event) {
        event.preventDefault(); // Empêche le rechargement de la page
        console.log("Le bouton Marquer a été cliqué !");
        let crypto = document.getElementById('crypto').value;
        let price = document.getElementById('price').value;
        let date = document.getElementById('date').value;

        // Préparation des données pour l'envoi
        let formData = new FormData();
        formData.append("crypto", crypto);
        formData.append("price", price);
        formData.append("date", date);

        // Envoi des données à mark_crypto.php
        fetch("mark_crypto.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log("Réponse du serveur :", data);
            if (data.success) {
                alert("Marqueur envoyé avec succès à RabbitMQ !",data);
            } else {
                alert("Erreur : " + data.error);
            }
        })
        .catch(error => {
            console.error("Erreur lors de l'envoi :", error);
            alert("Une erreur est survenue.");
        });
    });

console.log(document.getElementById('crypto')); // Doit afficher l'élément ou 'null'
console.log(document.getElementById('price'));
console.log(document.getElementById('date'));
</script>

</body>

<?php include('my_script.js'); ?>
<script src="js/main.js"></script>
<?php include('footer.php'); ?>