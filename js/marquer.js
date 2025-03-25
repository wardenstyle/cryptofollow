document.addEventListener("DOMContentLoaded", function () {
    const markButton = document.getElementById("markButton");
    const cryptoInput = document.getElementById("crypto");
    const priceInput = document.getElementById("price");
    const dateInput = document.getElementById("date");

    if (markButton) {
        markButton.addEventListener("click", function () {
            // Vérifie si l'utilisateur est connecté
            const userId = document.getElementById("id_u")?.value;
            if (!userId) {
                alert("Vous devez être connecté pour marquer un indicateur.");
                return;
            }

            // Création et affichage de l'étape 3
            let step3 = document.getElementById("step3");
            if (!step3) {
                step3 = document.createElement("div");
                step3.id = "step3";
                step3.classList.add("col-lg-6", "col-12", "mt-4");
                step3.innerHTML = `
                    <h2><img class="" src="img/c.png" alt=""> Etape 3</h2>
                    <h4>Consulter les marqueurs</h4>
                    <a href="markers.php" style="color:white" class="btn btn-info w-100">Voir les marqueurs</a>
                `;
                document.querySelector(".row.g-5").appendChild(step3);
            }
        });
    }
    markButton.disabled = true;
    // Quand on clique sur "Remplir", on remplit la date et active "Marquer"
    fillButton.addEventListener("click", function () {
        const currentDate = new Date();
        const formattedDate = currentDate.toISOString().slice(0, 19).replace("T", " ");
        dateInput.value = formattedDate;
        
        // Activer le bouton "Marquer"
        markButton.disabled = false;
    });
});