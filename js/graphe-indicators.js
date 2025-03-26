document.addEventListener("DOMContentLoaded", function () {
    const cryptoSelect = document.getElementById("cryptoSelect");
    const indicatorsContainer = document.getElementById("indicatorsContainer");
    const cryptoChartCanvas = document.getElementById("cryptoChart");
    const displayButton = document.getElementById("display");

    let myChart = null;

    displayButton.addEventListener("click", function (event) {
        event.preventDefault(); // Empêche le rechargement de la page

        // Récupère la crypto sélectionnée
        const selectedCrypto = cryptoSelect.value;

        // Cache/affiche les éléments
        indicatorsContainer.style.display = "none";
        cryptoChartCanvas.style.display = "block";

        // Charge le graphique
        loadChart(selectedCrypto);
    });

    function loadChart(crypto) {
        fetch(`https://api.coingecko.com/api/v3/coins/${crypto}/market_chart?vs_currency=usd&days=30`)
            .then(response => response.json())
            .then(data => {
                const labels = data.prices.map(entry => new Date(entry[0]).toISOString().split("T")[0]);
                const prices = data.prices.map(entry => entry[1]);

                // Récupération des marqueurs enregistrés en base
                fetch(`markers_crypto.php?crypto=${crypto}`)
                    .then(response => response.json())
                    .then(markerData => {
                        const markerDates = markerData.data.map(m => new Date(m.date).toISOString().split("T")[0]);
                        const markerPrices = markerData.data.map(m => m.value);
                        console.log("Dates des marqueurs enregistrés:", markerDates);
                        console.log("Valeurs des marqueurs enregistrés:", markerPrices);
                        // Détruit le graphique précédent si existant
                        if (myChart) {
                            myChart.destroy();
                        }

                        // Création du graphique avec Chart.js
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
                                        fill: false
                                    },
                                    {
                                        label: "Marqueurs",
                                        data: markerPrices,
                                        borderColor: "red",
                                        pointBackgroundColor: "red",
                                        pointRadius: 5,
                                        type: "scatter"
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: "top"
                                    }
                                }
                            }
                        });
                    });
            })
            .catch(error => console.error("Erreur lors du chargement du graphique:", error));
    }
});
