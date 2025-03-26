// Liste des scripts à charger
const scripts = [
    "https://code.jquery.com/jquery-3.4.1.min.js",
    "https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js",
    "lib/wow/wow.min.js",
    "lib/easing/easing.min.js",
    "lib/waypoints/waypoints.min.js",
    "lib/owlcarousel/owl.carousel.min.js",
    "lib/counterup/counterup.min.js",
    "js/main.js"
];

// Fonction pour charger les scripts dynamiquement
function loadScripts(scriptUrls) {
    scriptUrls.forEach(url => {
        let script = document.createElement("script");
        script.src = url;
        script.async = false; // Important pour garder l'ordre de chargement
        document.body.appendChild(script);
    });
}

// Charger les scripts après que la page soit chargée
document.addEventListener("DOMContentLoaded", function() {
    loadScripts(scripts);
});
