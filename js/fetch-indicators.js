document.addEventListener("DOMContentLoaded", function () {
    const cryptoSelect = document.getElementById("cryptoSelect");
    const indicatorsContainer = document.getElementById("indicatorsContainer");
    const cryptoChartCanvas = document.getElementById("cryptoChart");
    const cryptoBarChartCanvas = document.getElementById("cryptoBarChart");
    const displayButton = document.getElementById("display");
    const cryptoNameDisplay = document.getElementById("crypto_name");

    let myChart = null; // graphique des indicateurs
    let myBarChart = null; // graphique des quantités
    let isChartDisplayed = false; // Pour basculer entre le tableau et le graphique

    //Sauvegarder le choix du select à chaque changement de page
    const savedCrypto = localStorage.getItem("selectedCrypto");
    if (savedCrypto) {
        cryptoSelect.value = savedCrypto;
    }

    cryptoSelect.addEventListener("change", function () {
        localStorage.setItem("selectedCrypto", this.value);
    });

    // Fonction pour charger les indicateurs sous forme de tableau
    function loadIndicators(crypto, type = 'all') {
        fetch(`fetch_indicators.php?crypto=${encodeURIComponent(crypto)}&type=${type}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateIndicatorsTable(data.data); // Affiche le tableau => updateIndicatorsTable
                    updateBarChart(data.data); // Met à jour le graphique => BarChart
                } else {
                    indicatorsContainer.innerHTML = `<p style="color: red;">Erreur: ${data.error}</p>`;
                }
            })
            .catch(error => {
                console.error("Erreur lors du chargement des indicateurs:", error);
                indicatorsContainer.innerHTML = `<p style="color: red;">Erreur du chargement des données.</p>`;
            });
    }

    //affichage dans le tableau 
    function updateIndicatorsTable(data) {
        if (data.length > 0) {
            indicatorsContainer.innerHTML = `
             <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="display:none">#</th>
                            <th>Valeur</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Quantité</th>
                            <th>Alertes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.map(indicator => `
                            <tr data-id="${indicator.id}">
                                <td style="display:none">${indicator.id}</td>
                                <td>${indicator.price}</td>
                                <td>${indicator.date}</td>
                                <td>${indicator.type}</td>
                                <td>
                                    <span class="quantity-text">${indicator.qte}</span>
                                    <input type="number" class="quantity-input form-control" value="${indicator.qte}" style="display:none; width: 70px;">
                                </td>
                                <td>
                                    <span class="badge bg-info" title="nombre d'alerte active">${indicator.alert_count || 0}</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning edit-indicator" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-indicator" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button class="btn btn-sm btn-primary create-alert-btn" data-id="${indicator.id}" type="button" title="Créer une alerte">
                                        <i class="fas fa-bell"></i>
                                    </button>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
            `;
        } else {
            indicatorsContainer.innerHTML = "<p>Aucun indicateur trouvé pour cette crypto.</p>";
        }
    }
    // envoi de la crypto selectionné vers la page du comparateur
    document.getElementById('compare').addEventListener('click', function (e) {
        e.preventDefault();
    
        const selectedCrypto = document.getElementById('cryptoSelect').value;
    
        if (selectedCrypto) {
            // Rediriger vers compare_indicators.php avec la crypto en GET
            window.location.href = `compare_indicators.php?crypto=${encodeURIComponent(selectedCrypto)}`;
        } else {
            alert("Veuillez sélectionner une crypto.");
        }
    });
    // Gérer les boutons filtres (Achat,Vente,Tout)
    document.getElementById("btnAchat").addEventListener("click", function () {
        const crypto = document.getElementById("cryptoSelect").value;
        loadIndicators(crypto, "achat");
    });
    
    document.getElementById("btnVente").addEventListener("click", function () {
        const crypto = document.getElementById("cryptoSelect").value;
        loadIndicators(crypto, "vente");
    });
    
    document.getElementById("btnTout").addEventListener("click", function () {
        const crypto = document.getElementById("cryptoSelect").value;
        loadIndicators(crypto, "all");
    });

    // Gérer les événements des boutons Actions (modifier,sauvegarder,supprimer)
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

    // redirection vers la page de création d'alerte 
    document.addEventListener('click', function(e) {
        if (e.target.closest('.create-alert-btn')) {
          const btn = e.target.closest('.create-alert-btn');
          const id = btn.dataset.id;
      
          // Crée dynamiquement un formulaire POST
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = 'create_alert.php';
      
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'indicator_id';
          input.value = id;
      
          form.appendChild(input);
          document.body.appendChild(form);
          form.submit();
        }
      });

    // Fonction pour charger et afficher le graphique Chart.js
    function loadChart(crypto) {
        fetch(`https://api.coingecko.com/api/v3/coins/${crypto}/market_chart?vs_currency=usd&days=365`)
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
                        const achats = [];
                        const ventes = [];

                        markerData.data.forEach(m => {
                            const point = {
                                x: new Date(m.date),
                                y: m.price
                            };
                            if (m.type === "Achat") {
                                achats.push(point);
                            } else if (m.type === "Vente") {
                                ventes.push(point);
                            }
                        });

                        // Marqueurs Achat (verts)
                        datasets.push({
                            label: "Achat",
                            data: achats,
                            type: "scatter",
                            backgroundColor: "green",
                            pointBorderColor: "black",
                            pointRadius: 6,
                            pointHoverRadius: 8
                        });

                        // Marqueurs Vente (rouges)
                        datasets.push({
                            label: "Vente",
                            data: ventes,
                            type: "scatter",
                            backgroundColor: "red",
                            pointBorderColor: "black",
                            pointRadius: 6,
                            pointHoverRadius: 8
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
                                            tooltipFormat: 'YYYY-MM-DD'
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
                        // ---- Génération du graphique en barres (achats vs ventes) ----
                        const monthlyQuantitiesAchat = {};
                        const monthlyQuantitiesVente = {};

                        markerData.data.forEach(({ date, qte, type }) => {
                            const d = new Date(date);
                            const month = `${d.getFullYear()}-${(d.getMonth() + 1).toString().padStart(2, "0")}`;

                            if (type === "Achat") {
                                if (!monthlyQuantitiesAchat[month]) monthlyQuantitiesAchat[month] = 0;
                                monthlyQuantitiesAchat[month] += Number(qte);
                            } else if (type === "Vente") {
                                if (!monthlyQuantitiesVente[month]) monthlyQuantitiesVente[month] = 0;
                                monthlyQuantitiesVente[month] += Number(qte);
                            }
                        });

                        // Fusionne les labels (mois) uniques
                        const allMonths = Array.from(new Set([
                            ...Object.keys(monthlyQuantitiesAchat),
                            ...Object.keys(monthlyQuantitiesVente)
                        ])).sort();

                        const achatData = allMonths.map(month => monthlyQuantitiesAchat[month] || 0);
                        const venteData = allMonths.map(month => monthlyQuantitiesVente[month] || 0);

                        if (myBarChart) {
                            myBarChart.destroy();
                        }

                        const barCtx = cryptoBarChartCanvas.getContext("2d");
                        myBarChart = new Chart(barCtx, {
                            type: "bar",
                            data: {
                                labels: allMonths,
                                datasets: [
                                    {
                                        label: "Achats",
                                        data: achatData,
                                        backgroundColor: "green"
                                    },
                                    {
                                        label: "Ventes",
                                        data: venteData,
                                        backgroundColor: "red"
                                    }
                                ]
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
            displayButton.textContent = "Graphique";
        } else {
            indicatorsContainer.style.display = "none";
            cryptoChartCanvas.style.display = "block";
            cryptoBarChartCanvas.style.display = "block";
            loadChart(cryptoSelect.value);
            displayButton.textContent = "Indicateurs";
        }

        isChartDisplayed = !isChartDisplayed;
    });

    // Charger les indicateurs au démarrage
    loadIndicators(cryptoSelect.value);

    // Recharger les indicateurs lors du changement de crypto
    cryptoSelect.addEventListener("change", function () {
        loadIndicators(this.value);
    });

    function updateBarChart(data) {
        const monthlyQuantitiesAchat = {};
        const monthlyQuantitiesVente = {};
    
        data.forEach(({ date, qte, type }) => {
            const d = new Date(date);
            const month = `${d.getFullYear()}-${(d.getMonth() + 1).toString().padStart(2, "0")}`;
    
            if (type === "Achat") {
                if (!monthlyQuantitiesAchat[month]) monthlyQuantitiesAchat[month] = 0;
                monthlyQuantitiesAchat[month] += Number(qte);
            } else if (type === "Vente") {
                if (!monthlyQuantitiesVente[month]) monthlyQuantitiesVente[month] = 0;
                monthlyQuantitiesVente[month] += Number(qte);
            }
        });
    
        const allMonths = Array.from(new Set([
            ...Object.keys(monthlyQuantitiesAchat),
            ...Object.keys(monthlyQuantitiesVente)
        ])).sort();
    
        const achatData = allMonths.map(month => monthlyQuantitiesAchat[month] || 0);
        const venteData = allMonths.map(month => monthlyQuantitiesVente[month] || 0);
    
        if (myBarChart) {
            myBarChart.destroy();
        }
    
        const barCtx = cryptoBarChartCanvas.getContext("2d");
        myBarChart = new Chart(barCtx, {
            type: "bar",
            data: {
                labels: allMonths,
                datasets: [
                    {
                        label: "Achats",
                        data: achatData,
                        backgroundColor: "green"
                    },
                    {
                        label: "Ventes",
                        data: venteData,
                        backgroundColor: "red"
                    }
                ]
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
    }
    
});