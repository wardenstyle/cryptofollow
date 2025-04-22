document.addEventListener('DOMContentLoaded', function() {
    let today = new Date();

    // Récupère l'année, le mois et le jour
    let year = today.getFullYear();
    let month = today.getMonth() + 1; // Les mois commencent à 0, donc ajoutez 1
    let day = today.getDate();

    // Formate la date en une chaîne de caractères (par exemple, '2024-06-10')
    let formattedDate = year + '-' + (month < 10 ? '0' + month : month) + '-' + (day < 10 ? '0' + day : day);

    //traitement des données: 
    var indicators = [];    
    document.querySelectorAll('#mes_indicateurs').forEach(input => {
        try {
            let event = JSON.parse(input.value);
            indicators.push(event);
        } catch (e) {
            console.error("Erreur de parsing JSON:", e);
        }
    });

    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {

      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
      },

      // customize the button names,
      // otherwise they'd all just say "list"
      views: {
        listDay: { buttonText: 'Jour' },
        listWeek: { buttonText: 'Semaine' }
    
      },

      initialDate: formattedDate,
      navLinks: true, // can click day/week names to navigate views
      businessHours: true, // display business hours
      editable: true,
      selectable: true,
      events: indicators
  
    });
 
    calendar.render();
  });