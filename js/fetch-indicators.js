document.addEventListener("DOMContentLoaded", function () {
    const cryptoSelect = document.getElementById("cryptoSelect");
    const indicatorsContainer = document.getElementById("indicatorsContainer");
    const cryptoChartCanvas = document.getElementById("cryptoChart");
    const displayButton = document.getElementById("display");

    let myChart = null;
    let isChartDisplayed = false; // Pour basculer entre le tableau et le graphique

    // 🔹 Fonction pour charger les indicateurs sous forme de tableau
    function loadIndicators(crypto) {
        fetch(`fetch_indicators.php?crypto=${encodeURIComponent(crypto)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.data.length > 0) {
                        indicatorsContainer.innerHTML = `
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Crypto</th>
                                        <th>Valeur</th>
                                        <th>Date</th>
                                        <th>Quantité</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.data.map(indicator => `
                                        <tr>
                                            <td>${indicator.id}</td>
                                            <td>${indicator.crypto}</td>
                                            <td>${indicator.price}</td>
                                            <td>${indicator.date}</td>
                                            <td>${indicator.qte}</td>
                                            <td><a href="#" class="text-danger delete-indicator" data-id="${indicator.id}">Supprimer</a></td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        `;
                    } else {
                        indicatorsContainer.innerHTML = "<p>Aucun indicateur trouvé pour cette crypto.</p>";
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

    // 🔹 Fonction pour charger et afficher le graphique Chart.js
    function loadChart(crypto) {
        fetch(`https://api.coingecko.com/api/v3/coins/${crypto}/market_chart?vs_currency=usd&days=30`)
            .then(response => response.json())
            .then(data => {
                
                const labels = data.prices.map(entry => new Date(entry[0])); // Utilise un tableau d'objets Date
                const prices = data.prices.map(entry => entry[1]);  // Tous les prix

                let datasets = []; // Nous allons y ajouter les datasets

                // Ajoute les prix historiques dans le dataset
                datasets.push({
                    label: "Prix Historique",
                    data: prices.map((price, index) => ({ x: labels[index], y: price })),
                    borderColor: "blue",
                    fill: false,
                    tension: 0.4, // Pour lisser la courbe
                    pointRadius: 0 // Pas de point sur la ligne
                });

                // Récupération des marqueurs enregistrés
                fetch(`fetch_indicators.php?crypto=${crypto}`)
                    .then(response => response.json())
                    .then(markerData => {
                        const markerDates = markerData.data.map(m => new Date(m.date));
                        const markerPrices = markerData.data.map(m => m.price);

                        // Ajoute les marqueurs dans le dataset
                        datasets.push({
                            label: "Marqueurs",
                            data: markerPrices.map((price, index) => ({
                                x: markerDates[index],
                                y: price
                            })),
                            borderColor: "red",
                            backgroundColor: "red",
                            pointBackgroundColor: "red",
                            pointRadius: 6, // Taille des points rouges
                            pointBorderColor: "black",
                            pointHoverRadius: 8,
                            type: "scatter" // Type de graphique pour les marqueurs
                        });

                        // Détruit l'ancien graphique s'il existe
                        if (myChart) {
                            myChart.destroy();
                        }

                        // Création du graphique
                        const ctx = cryptoChartCanvas.getContext("2d");
                        myChart = new Chart(ctx, {
                            type: "line",
                            data: { datasets },
                            options: {
                                responsive: true,
                                scales: {
                                    x: {
                                        type: 'time',
                                        time: {
                                            unit: 'day',
                                            tooltipFormat: 'YYYY-MM-DD' // Format de la date dans l'info-bulle
                                        }
                                    },
                                    y: {
                                        beginAtZero: false
                                    }
                                },
                                plugins: {
                                    legend: {
                                        position: 'top'
                                    },
                                    tooltip: {
                                        backgroundColor: "rgba(0, 0, 0, 0.7)",
                                        titleFont: { size: 14 },
                                        bodyFont: { size: 12 }
                                    }
                                }
                            }
                        });
                    });
            })
            .catch(error => console.error("Erreur lors du chargement du graphique:", error));
    }

    // 🔹 Bascule entre tableau et graphique au clic sur le bouton
    displayButton.addEventListener("click", function (event) {
        event.preventDefault(); // Empêche le rechargement de la page

        if (isChartDisplayed) {
            indicatorsContainer.style.display = "block";
            cryptoChartCanvas.style.display = "none";
            displayButton.textContent = "Afficher le graphique";
        } else {
            indicatorsContainer.style.display = "none";
            cryptoChartCanvas.style.display = "block";
            loadChart(cryptoSelect.value);
            displayButton.textContent = "Afficher les indicateurs";
        }

        isChartDisplayed = !isChartDisplayed;
    });

    // 🔹 Charger les indicateurs au démarrage
    loadIndicators(cryptoSelect.value);

    // 🔹 Recharger les indicateurs lors du changement de crypto
    cryptoSelect.addEventListener("change", function () {
        loadIndicators(this.value);
    });
});
