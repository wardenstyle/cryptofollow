document.addEventListener("DOMContentLoaded", function () {
    const cryptoSelect = document.getElementById("cryptoSelect");
    const indicatorsContainer = document.getElementById("indicatorsContainer");
    const cryptoChartCanvas = document.getElementById("cryptoChart");
    const displayButton = document.getElementById("display");

    let myChart = null;
    let isChartDisplayed = false; // Pour basculer entre le tableau et le graphique

    // Fonction pour charger les indicateurs sous forme de tableau
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

    // Fonction pour charger et afficher le graphique Chart.js
    function loadChart(crypto) {
        fetch(`https://api.coingecko.com/api/v3/coins/${crypto}/market_chart?vs_currency=usd&days=30`)
            .then(response => response.json())
            .then(data => {
                const labels = data.prices.map(entry => new Date(entry[0]).toISOString().split("T")[0]);
                const prices = data.prices.map(entry => entry[1]);

                console.log("Dates API CoinGecko:", labels);

                // Récupération des marqueurs enregistrés
                fetch(`fetch_indicators.php?crypto=${crypto}`)
                    .then(response => response.json())
                    .then(markerData => {
                        const markerPoints = markerData.data.map(m => ({
                            x: new Date(m.date), // Positionne le marqueur à la bonne date
                            y: m.price            // Valeur correspondante
                        }));

                        console.log("Marqueurs enregistrés:", markerPoints);

                        // Détruit l'ancien graphique s'il existe
                        if (myChart) {
                            myChart.destroy();
                        }

                        // Création du graphique
                        const ctx = cryptoChartCanvas.getContext("2d");
                        myChart = new Chart(ctx, {
                            type: "line",
                            data: {
                                labels: labels,
                                datasets: [
                                    {
                                        label: "Prix Historique",
                                        data: prices,
                                        borderColor: "blue",
                                        fill: true,
                                        backgroundColor: "rgba(0, 0, 255, 0.2)",
                                        tension: 0.4
                                    },
                                    {
                                        label: "Marqueurs",
                                        data: markerPoints,
                                        borderColor: "red",
                                        pointBackgroundColor: "red",
                                        pointBorderColor: "black",
                                        pointRadius: 6,
                                        pointHoverRadius: 8,
                                        type: "scatter"
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    x: {
                                        type: "time",
                                        time: {
                                            unit: "day",
                                            tooltipFormat: "YYYY-MM-DD"
                                        },
                                        ticks: {
                                            maxRotation: 45,
                                            autoSkip: true
                                        },
                                        grid: {
                                            display: false // <-- Cache la grille verticale pour plus de lisibilité
                                        }
                                    },
                                    y: {
                                        beginAtZero: false,
                                        ticks: {
                                            callback: value => "$" + value.toFixed(2) // <-- Formate les prix en dollars
                                        },
                                        grid: {
                                            color: "rgba(200, 200, 200, 0.3)" // <-- Adoucit la grille horizontale
                                        }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        labels: {
                                            font: {
                                                size: 14
                                            }
                                        }
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

    // Bascule entre tableau et graphique au clic sur le bouton
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

    // Charger les indicateurs au démarrage
    loadIndicators(cryptoSelect.value);

    // Recharger les indicateurs lors du changement de crypto
    cryptoSelect.addEventListener("change", function () {
        loadIndicators(this.value);
    });
});
