window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    const macrypto = urlParams.get('crypto');

    if (macrypto) {
        // Démarrer le producteur à chaque 10 secondes
        const intervalId = setInterval(function() {
            fetch(`producer/start_producer.php?crypto=${crypto}`)
                .then(response => response.text())
                .then(data => {
                    console.log(data); // Affiche la réponse du serveur (success ou erreur)
                })
                .catch(error => console.error('Erreur:', error));
        }, 10000);

        // quand la page est fermée ou rechargée
        window.addEventListener('beforeunload', function() {
            clearInterval(intervalId); // Arrête l'intervalle
        });
    }
}