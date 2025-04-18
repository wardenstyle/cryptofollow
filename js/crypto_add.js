let isRequestPending = false;
let cooldownActive = false;
let cooldownSeconds = 5;
let cooldownInterval;

$(document).ready(function () {
    $('#toggleAddForm').on('click', function (e) {
        e.preventDefault();
        $('#addCryptoForm').slideToggle();
    });

    $('#newCryptoForm').on('submit', function (e) {
        e.preventDefault();

        const idApi = $('#newCrypto').val().trim();

        if (idApi === '') {
            $('#statusMsg').text('Veuillez saisir un ID API.');
            return;
        }

        if (isRequestPending || cooldownActive) {
            $('#MsgInfo').text(`Veuillez patienter (${cooldownSeconds}s) avant de réessayer.`);
            return;
        }

        $('#statusMsg').text('Vérification en cours, veuillez patienter...');
        $('#spinnerLoader').show(); // Affiche le spinner
        isRequestPending = true;

        $.ajax({
            url: 'create_crypto.php', // à remplacer par create_crypto.php quand tout est ok
            method: 'POST',
            dataType: 'json',
            data: { id_api: idApi },
            success: function (response) {
                console.log("Réponse du serveur:", response);  // Debug

                if (response.success) {
                    $('#MsgInfo').text(response.message || 'Crypto ajoutée avec succès !');
                } else {
                    $('#MsgInfo').text(response.error || response.message || 'Crypto introuvable sur CoinGecko.');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Erreur AJAX:", textStatus, errorThrown);
                $('#MsgInfo').text("Erreur lors de l'ajout. Veuillez réessayer.");
            },
            complete: function () {
                $('#spinnerLoader').hide(); // Masque le spinner
                isRequestPending = false;
                startCooldown();
            }
        });
    });

    function startCooldown() {
        cooldownActive = true;
        cooldownSeconds = 5;

        $('#statusMsg').text(`Cooldown : ${cooldownSeconds}s`);
        cooldownInterval = setInterval(() => {
            cooldownSeconds--;
            if (cooldownSeconds > 0) {
                $('#statusMsg').text(`Cooldown : ${cooldownSeconds}s`);
            } else {
                clearInterval(cooldownInterval);
                cooldownActive = false;
                $('#statusMsg').text('🔁 Vous pouvez relancer une vérification.');
            }
        }, 1000);
    }
});