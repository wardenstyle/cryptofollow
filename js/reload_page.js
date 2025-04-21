setInterval(() => {
    fetch('consume_price.php')
        .then(response => response.json())
        .then(data => {
            console.log(data); // Vérifier les données reçues
            
            if (data.crypto && data.price) {
                // Cibler la cellule "Prix Actuel" en utilisant le data-crypto
                const priceCell = document.querySelector(`[data-crypto="${data.crypto}"]`);
                if (priceCell) {
                    priceCell.textContent = data.price + ' $';  // Met à jour le prix actuel
                }

                // Mettre à jour l'évolution si nécessaire
                const evolutionCell = document.getElementById('evolution-' + data.crypto);
                if (evolutionCell) {
                    const oldPrice = parseFloat(evolutionCell.getAttribute('data-old')) || 0;
                    const newPrice = parseFloat(data.price);
                    const evolution = (newPrice - oldPrice).toFixed(2);
                    const color = evolution >= 0 ? 'green' : '#B71C1C';

                    // Met à jour l'évolution et la couleur
                    evolutionCell.textContent = evolution + ' $';
                    evolutionCell.style.color = color;
                }
            }
        })
        .catch(err => console.error("Erreur AJAX:", err));
}, 10000); // Mise à jour toutes les 10 secondes

let crypto = document.getElementById("crypto").value;
const ctx = document.getElementById('cryptoChart').getContext('2d');

    const chart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: [], // horodatages
        datasets: [{
          label: crypto+'(USD)',
          data: [],
          borderColor: 'orange',
          backgroundColor: 'rgba(255,165,0,0.2)',
          tension: 0.2,
          pointRadius: 3,
        }]
      },
      options: {
        responsive: true,
        scales: {
          x: { title: { display: true, text: 'Heure' } },
          y: { title: { display: true, text: 'Prix (USD)' } }
        }
      }
    });

    // Connexion au WebSocket
        const ws = new WebSocket("ws://localhost:8080"); // Assure-toi que l'URL est correcte

        // Lorsque le WebSocket est ouvert
        ws.onopen = () => {
            console.log("Connexion WebSocket établie !");
        };
        
        //Lorsque le WebSocket reçoit un message
        ws.onmessage = (event) => {
            const data = JSON.parse(event.data);  // Parsing du message JSON
            console.log("Message reçu via WebSocket:", data);
        
            // Si le graphique contient plus de 20 points, supprime le plus ancien
            if (chart.data.labels.length >= 20) {
                chart.data.labels.shift();
                chart.data.datasets[0].data.shift();
            }
        
            // Ajoute le nouveau point au graphique
            const heure = new Date(data.timestamp * 1000).toLocaleTimeString();  // Formatage de l'heure
            chart.data.labels.push(heure);
            chart.data.datasets[0].data.push(data.price.toFixed(2));
            chart.update();  // Met à jour le graphique
            console.log("Graphique mis à jour");
        
            // Mise à jour des lignes du tableau pour chaque indicateur
            const allRows = document.querySelectorAll('table tbody tr');
            allRows.forEach(row => {
                const cryptoCell = row.querySelector('[data-crypto]');
                if (cryptoCell) {
                    const crypto = cryptoCell.getAttribute('data-crypto');
        
                    // Comparer le prix actuel avec le prix enregistré pour chaque crypto
                    if (crypto === data.crypto) {
                        const priceCell = row.querySelector('[data-crypto="' + data.crypto + '"]');
                        if (priceCell) {
                            // Mettre à jour le prix actuel
                            priceCell.textContent = data.price + ' $';
                        }
        
                        // Récupérer le prix enregistré dans le tableau
                        const oldPriceCell = row.querySelector('td:first-child');  // La première cellule contient le prix enregistré
                        const oldPrice = parseFloat(oldPriceCell.textContent.replace('$', '').trim()) || 0;
        
                        // Calculer l'évolution
                        const evolutionCell = row.querySelector('#evolution-' + data.crypto);
                        if (evolutionCell) {
                            const evolution = (data.price - oldPrice).toFixed(2);  // Différence entre le prix actuel et le prix enregistré
                            const color = evolution >= 0 ? 'green' : '#B71C1C';
        
                            // Mettre à jour l'évolution et la couleur
                            evolutionCell.textContent = evolution + ' $';
                            evolutionCell.style.color = color;
                        }
        
                        // Gain/perte
                        const gainCell = row.querySelector('#gain-' + data.crypto);
                        if (gainCell) {
                            const qte = parseFloat(gainCell.getAttribute('data-qte')) || 0;
                            const gain = (data.price - oldPrice) * qte;
                            gainCell.textContent = gain.toFixed(2) + ' $';
                            gainCell.style.color = gain >= 0 ? 'green' : '#B71C1C';
                        }
                    }
                }
            });
        
            // Mise à jour des gains/pertes totaux
            updateTotalEvolution();
        };

    // Lorsque le WebSocket rencontre une erreur
    ws.onerror = (error) => {
        console.error("Erreur WebSocket :", error);
    };

    // Lorsque la connexion WebSocket est fermée
    ws.onclose = () => {
        console.log("Connexion WebSocket fermée !");
    };

// calcule des gains / pertes totaux
function updateTotalEvolution() {
    let total = 0;
    const gainCells = document.querySelectorAll('.gain-perte');
    gainCells.forEach(cell => {
        const value = parseFloat(cell.textContent.replace('$', '').trim());
        if (!isNaN(value)) {
            total += value;
        }
    });
    const totalGainCell = document.getElementById('total-gain');
    if (totalGainCell) {
        totalGainCell.textContent = total.toFixed(2) + ' $';
        totalGainCell.style.color = total >= 0 ? 'green' : '#B71C1C';
    }
}
