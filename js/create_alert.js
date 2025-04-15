// curseur de %
document.getElementById('percentageRange').addEventListener('input', function () {
    const percentage = parseFloat(this.value);
    document.getElementById('selectedPercentage').textContent = percentage;

    const originalPrice = parseFloat(document.getElementById('originalPrice').textContent);
    const adjustedPrice = (originalPrice * (1 + percentage / 100)).toFixed(2);
    document.getElementById('targetPrice').textContent = adjustedPrice;
});

// formulaire d'alerte
document.getElementById('alertForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    const originalPrice = parseFloat(document.getElementById('originalPrice').textContent);
    const percentage = parseFloat(formData.get('percentage'));
    const targetPrice = (originalPrice * (1 + percentage / 100)).toFixed(2);
    const email = document.getElementById('alert_email').value;

    formData.append('target_price', targetPrice);
    formData.append('percentage_', percentage);
    formData.append('email', email);

    // Déterminer le message selon le type
    const alertType = formData.get('alert_type');
    let content = '';
    if (alertType === 'Achat') {
        content = "Il est temps d'acheter";
    } else if (alertType === 'Vente') {
        content = "Il est temps de vendre";
    }
    formData.append('content', content);

    fetch('save_alert.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('alertResponse').innerHTML = `<div class="alert alert-info">${data}</div>`;
        // rechargement de la page après 3 secondes
        if (!data.includes("Erreur") && !data.includes("déjà enregistré 5 alertes")) {
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        }
    })
    .catch(error => {
        document.getElementById('alertResponse').innerHTML = `<div class="alert alert-danger">Erreur : ${error}</div>`;
    });
});