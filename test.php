<?php
// test.php

// Indique au navigateur que la réponse est du JSON
header('Content-Type: application/json');

// Simule une réponse JSON de succès
echo json_encode([
    'success' => true,
    'message' => 'Crypto ajoutée (test) avec succès !'
]);