const cryptoSelect = document.getElementById("cryptoSelect");
const cryptoNameDisplay = document.getElementById("crypto_name");
// Fonction pour afficher la crypto sélectionnée
function updateCryptoName() {
    const selectedText = cryptoSelect.options[cryptoSelect.selectedIndex].text;
    cryptoNameDisplay.textContent = " " + selectedText;
}
updateCryptoName();
cryptoSelect.addEventListener("change", updateCryptoName);