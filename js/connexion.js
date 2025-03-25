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