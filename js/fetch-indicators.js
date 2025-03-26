document.addEventListener("DOMContentLoaded", function () {
    const cryptoSelect = document.getElementById("cryptoSelect");
    const indicatorsContainer = document.getElementById("indicatorsContainer");

    // Récupérer la crypto depuis l'URL (ou "bitcoin" par défaut)
    const urlParams = new URLSearchParams(window.location.search);
    const initialCrypto = urlParams.get("crypto") || "bitcoin";

    // Sélectionner la bonne valeur dans le <select>
    cryptoSelect.value = initialCrypto;

    // Fonction pour charger les indicateurs
    function loadIndicators(crypto) {
        fetch(`markers_crypto.php?crypto=${encodeURIComponent(crypto)}`)
            .then(response => {
            // Vérifier si la réponse est correcte (code HTTP 200)
            if (!response.ok) {
                throw new Error('Erreur de réseau');
            }
            return response.json(); // Parser la réponse en JSON
            })
            .then(data => {
                console.log(data);
                if (data.success) {
                    if (data.data.length > 0) {
                        indicatorsContainer.innerHTML = data.data.map(indicator => `
                            <div class="card my-2 p-3">
                                <strong>Crypto:</strong> ${indicator.crypto} <br>
                                <strong>Valeur:</strong> ${indicator.value} <br>
                                <strong>Date:</strong> ${indicator.date}
                            </div>
                        `).join('');
                    } else {
                        indicatorsContainer.innerHTML = "<p>Pas d'indicateur pour cette crypto.</p>";
                    }
                } else {
                    indicatorsContainer.innerHTML = `<p style="color: red;">Erreur: ${data.error}</p>`;
                }
            })
            .catch(error => {
                console.error("Erreur lors du chargement des indicateurs:", error);
                indicatorsContainer.innerHTML = `<p style="color: red;">Erreur de chargement.</p>`;
            });
        
    }

    // Charger les indicateurs au démarrage
    loadIndicators(initialCrypto);

    // Mettre à jour l'affichage lors du changement de sélection
    cryptoSelect.addEventListener("change", function () {
        const selectedCrypto = this.value;

        // Met à jour l'URL sans recharger la page
        const newUrl = new URL(window.location.href);
        newUrl.searchParams.set("crypto", selectedCrypto);
        window.history.pushState({}, "", newUrl);

        // Charger les indicateurs pour la nouvelle crypto
        loadIndicators(selectedCrypto);
    });
});
