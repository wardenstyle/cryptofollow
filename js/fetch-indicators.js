document.addEventListener("DOMContentLoaded", function () {
    const cryptoSelect = document.getElementById("cryptoSelect");
    const indicatorsContainer = document.getElementById("indicatorsContainer");
    const cryptoChartCanvas = document.getElementById("cryptoChart");
    const cryptoBarChartCanvas = document.getElementById("cryptoBarChart");
    const displayButton = document.getElementById("display");

    let myChart = null; // graphique des indicateurs
    let myBarChart = null; // graphique des quantités
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
                                        <th style="display:none">#</th>
                                        <th>Crypto</th>
                                        <th>Valeur</th>
                                        <th>Date</th>
                                        <th>Quantité</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.data.map(indicator => `
                                        <tr data-id="${indicator.id}">
                                            <td style="display:none">${indicator.id}</td>
                                            <td>${indicator.crypto}</td>
                                            <td>${indicator.price}</td>
                                            <td>${indicator.date}</td>
                                            <td>
                                                <span class="quantity-text">${indicator.qte}</span>
                                                <input type="number" class="quantity-input form-control" value="${indicator.qte}" style="display:none; width: 70px;">
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-warning edit-indicator">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger delete-indicator">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
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

    // Gérer les événements des boutons
    indicatorsContainer.addEventListener("click", function (event) {
        const target = event.target;
        const row = target.closest("tr");
        const id = row.getAttribute("data-id");

        // Suppression
        if (target.closest(".delete-indicator")) {
            if (confirm("Voulez-vous vraiment supprimer cet indicateur ?")) {
                fetch(`delete_indicator.php?id=${id}`, { method: "DELETE" })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            loadIndicators(cryptoSelect.value);
                        } else {
                            alert("Erreur lors de la suppression !");
                        }
                    })
                    .catch(error => console.error("Erreur suppression:", error));
            }
        }

        // Modification de la quantité
        if (target.closest(".edit-indicator")) {
            event.preventDefault(); // Empêche un rechargement involontaire

            const quantityText = row.querySelector(".quantity-text");
            const quantityInput = row.querySelector(".quantity-input");
            const editButton = target.closest(".edit-indicator");

            if (quantityInput.style.display === "none") {
                // Passer en mode édition
                quantityText.style.display = "none";
                quantityInput.style.display = "inline-block";
                quantityInput.focus();
                editButton.innerHTML = '<button title="sauvegarder" class="btn btn-success"><i class="fas fa-save"></i></button>'; // Changer l'icône
            } else {
                // Enregistrer la modification
                const newQuantity = quantityInput.value;

                fetch(`update_indicator.php`, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ id, qte: newQuantity })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        quantityText.textContent = newQuantity;
                        quantityText.style.display = "inline";
                        quantityInput.style.display = "none";
                        editButton.innerHTML = '<i class="fas fa-edit"></i>'; // Remet l'icône d'édition
                    } else {
                        alert("Erreur lors de la modification !");
                    }
                })
                .catch(error => console.error("Erreur modification:", error));
            }
        }
    });

    // Fonction pour charger et afficher le graphique Chart.js
    function loadChart(crypto) {
        fetch(`https://api.coingecko.com/api/v3/coins/${crypto}/market_chart?vs_currency=usd&days=30`)
            .then(response => response.json())
            .then(data => {
                
                const labels = data.prices.map(entry => new Date(entry[0])); // Utilise un tableau d'objets Date
                const prices = data.prices.map(entry => entry[1]);  // Tous les prix

                let datasets = [];

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
                        // ---- Génération du graphique en barres ----
                        const monthlyQuantities = {};
                        const markerQuantities = markerData.data.map(m => ({
                            date: new Date(m.date),
                            qte: Number(m.qte) // Convertir en nombre au cas où
                        }));

                        markerQuantities.forEach(({ date, qte }) => {
                            const month = `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, "0")}`;
                            if (!monthlyQuantities[month]) {
                                monthlyQuantities[month] = 0;
                            }
                            monthlyQuantities[month] += qte;
                        });

                        const barLabels = Object.keys(monthlyQuantities);
                        const barData = Object.values(monthlyQuantities);

                        if (myBarChart) {
                            myBarChart.destroy();
                        }

                        const barCtx = cryptoBarChartCanvas.getContext("2d");
                        myBarChart = new Chart(barCtx, {
                            type: "bar",
                            data: {
                                labels: barLabels,
                                datasets: [{
                                    label: "Quantité Totale par Mois",
                                    data: barData,
                                    backgroundColor: "green"
                                }]
                            },
                            options: {
                                scales: {
                                    x: {
                                        title: {
                                            display: true,
                                            text: "Mois"
                                        }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: "Quantité"
                                        }
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
            cryptoBarChartCanvas.style.display = "none";
            displayButton.textContent = "Afficher le graphique";
        } else {
            indicatorsContainer.style.display = "none";
            cryptoChartCanvas.style.display = "block";
            cryptoBarChartCanvas.style.display = "block";
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