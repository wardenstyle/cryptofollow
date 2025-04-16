document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.delete-alert').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const row = this.closest('tr');
            const alertId = row.dataset.alertId || row.cells[0].textContent.trim(); // Assure-toi que l'ID est là

            if (!alertId) {
                alert("ID de l'alerte introuvable.");
                return;
            }

            if (confirm("Voulez-vous vraiment supprimer cette alerte ?")) {
                const formData = new FormData();
                formData.append('alert_id', alertId);

                fetch('delete_alert.php', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        row.remove();
                    } else {
                        alert("Erreur : " + data.error);
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("Erreur réseau ou serveur.");
                });
            }
        });
    });
});
