<?php
require_once('factory.php');
existeSession();
include('head.php'); 
include('nav-bar.php'); 

if (isset($_SESSION['id_u'])) {
    $config = loadConfiguration();
    $pdo = connexionPDO($config);

    try {

        $indicators = request_execute($pdo, "
        SELECT * FROM indicators where id_u = :id_u
        ", [':id_u' => $_SESSION['id_u']]);
    
        $nb_indicators = request_execute($pdo, "
        SELECT COUNT(*) FROM indicators where id_u = :id_u
        ", [':id_u' => $_SESSION['id_u']]);

    } catch (PDOException $e) {
      error_log("Erreur lors de la vérification marqueurs: " . $e->getMessage());
      echo 'impossible de récuperer les marqueurs';
      header('Location: 404.php');
    exit();
    }

    if($nb_indicators) {
        // encodage des dates pour tous les indicateurs
        foreach($indicators as $key=>$value) {

            $selectedTime = $value['date'];
            // indiquer les paramètres du calendrier que l'on veut afficher
            $json = array();
            $json['event_id']=$value['id'];
            $json['title'] = $value['type'].' de '.number_format($value['qte'], 2).' '.$value['crypto'].' au prixU de : '.$value['price'].' pour un montant total de : '.number_format($value['qte'],2) * $value['price'] ;
            $json['start'] = $selectedTime;
            $tab = json_encode($json);

            echo "<input type='hidden' style='display:none' id='mes_indicateurs'value='$tab'>";
        }

    }else {
      echo "Vous n'avez aucun marqueur dans votre agenda";
    }

?> 

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script  type="text/javascript" src='fullcalendar-6.1.13/dist/index.global.min.js'></script>
<script  type="text/javascript" src='js/calendar.js'></script>

<body>
    <div class="container-fluid hero-header bg-light">
        <div class="container py-3">

            <div id="calendrier">
                <div id='calendar'></div>                       
            </div>

        </div>
    </div>

<!-- dark mode -->
<script src="js/dark_mode.js"></script>
</body>


<?php
} else {
    header('Location: log-in.php');
    exit();
}
?>