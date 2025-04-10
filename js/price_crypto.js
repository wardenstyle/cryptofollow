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
            //let qte = document.getElementById('qte').value;
    
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
            console.log("Le bouton Marquer a été cliqué sur!");
            let crypto = document.getElementById('crypto').value;
            let price = document.getElementById('price').value;
            let date = document.getElementById('date').value;
            let user = document.getElementById('id_u').value;
            let qte = document.getElementById('qte').value;
            let type;
            if(document.getElementById('achat').checked) {type = document.getElementById('achat').value}
            else {type = document.getElementById('vente').value}

            // Préparation des données pour l'envoi
            let formData = new FormData();
            formData.append("crypto", crypto);
            formData.append("price", price);
            formData.append("date", date);
            formData.append("id_u", user);
            formData.append("qte", qte);
            formData.append("type", type);
    
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
            console.log(document.getElementById('qte'));
    } else {
        console.log("L'utilisateur n'est pas connecté. Le script d'indicateur ne peut pas être exécuté.");
    }