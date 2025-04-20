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
            url: 'create_crypto.php',
            method: 'POST',
            dataType: 'json',
            data: {
                id_api: idApi,
                secret: 'ma-super-cle-ultrasecrete-98462' // clé
            },
            success: function (response) {
                console.log("Réponse du serveur:", response);  // Debug

                if (response.success) {
                    $('#MsgInfo').text(response.message || 'Crypto ajoutée avec succès !');

                    // Création du producer après succès
                    $.ajax({
                        url: 'register_crypto.php',
                        method: 'POST',
                        data: {
                            crypto: idApi,
                            secret: 'ma-super-cle-ultrasecrete-98462' // clé
                        },
                        success: function (r2) {
                            $('#statusMsg').text('Producer créé : ' + r2);
                            setTimeout(function() {
                                location.reload();  // Recharge la page
                            }, 2000);
                        },
                        error: function () {
                            $('#statusMsg').text('Erreur lors de la création du producer.');
                        }
                    });
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

        $('#statusMsg').text(`Compte à rebours : ${cooldownSeconds}s`);
        cooldownInterval = setInterval(() => {
            cooldownSeconds--;
            if (cooldownSeconds > 0) {
                $('#statusMsg').text(`Compte à rebours : ${cooldownSeconds}s`);
            } else {
                clearInterval(cooldownInterval);
                cooldownActive = false;
                $('#statusMsg').text(' Vous pouvez relancer une vérification.');
            }
        }, 1000);
    }
});