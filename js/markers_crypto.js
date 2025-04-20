const cryptoSelect = document.getElementById("cryptoSelect");
const cryptoNameDisplay = document.getElementById("crypto_name");

// Restaure la valeur sauvegardée
const savedCrypto = localStorage.getItem("selectedCrypto");
if (savedCrypto) {
    cryptoSelect.value = savedCrypto;
}

// Met à jour le nom affiché
function updateCryptoName() {
    const selectedText = cryptoSelect.options[cryptoSelect.selectedIndex].text;
    cryptoNameDisplay.textContent = " " + selectedText;
}

// On met à jour le nom après avoir restauré le select
updateCryptoName();

// Sauvegarde à chaque changement et met à jour le nom
cryptoSelect.addEventListener("change", function () {
    localStorage.setItem("selectedCrypto", this.value);
    updateCryptoName();
});
