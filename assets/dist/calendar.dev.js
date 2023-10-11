"use strict";

//sélectionne un élément HTML ayant un ID 'calendrier' et
//utilise la bibliothèque FullCalendar pour initialiser un
//calendrier. Il configure plusieurs options, notamment la
//vue initiale, la localisation et le fuseau horaire,
//les événements à afficher et le contenu des événements.
//Il ajoute également une classe CSS 'mark' aux cellules du calendrier
//qui ont une date correspondant à la date de début de l'événement et
//ajoute une règle CSS pour définir la couleur d'arrière-plan de ces cellules
//Enfin, il rend le calendrier en appelant la méthode 'render' de l'objet 'calendar'.
document.addEventListener('DOMContentLoaded', function () {
  var calendarElt = document.querySelector("#calendrier");
  var calendar = new FullCalendar.Calendar(calendarElt, {
    initialView: 'dayGridYear',
    locale: 'fr',
    timeZone: 'Europe/Paris',
    firstDay: 1,
    headerToolbar: {
      start: 'prev,next today',
      center: 'title',
      end: 'dayGridYear,dayGridMonth,timeGridWeek'
    },
    events: events,
    eventContent: function eventContent(info) {
      var dateStr = info.event.start.toISOString().slice(0, 10);
      var tdElts = document.querySelectorAll('td[data-date="' + dateStr + '"]');

      for (var i = 0; i < tdElts.length; i++) {
        tdElts[i].classList.add('mark');
      }

      return {
        html: '<style> td[data-date="' + info.event.start.toISOString().slice(0, 10) + '"]{   background-color:' + info.backgroundColor + ';}</style>'
      };
    },
    viewDidMount: function viewDidMount(info) {} // const daysInMonth = info.view.currentEnd.diff(info.view.currentStart, 'days');
    // const titleElement = calendarElt.querySelector('.fc-toolbar-title');
    // titleElement.innerText += ' (' + daysInMonth + ' jours)';
    //         viewDidMount: function(info) {
    //             let year = info.view.currentStart.getFullYear();
    //             console.log(year);
    //             let daysInYear = 0;
    //             // Boucle sur chaque jour de l'année
    //             for (let month = 0; month < 12; month++) {
    //                 // Boucle sur chaque jour du mois
    //                 for (let day = 1; day <= new Date(year, month + 1, 0).getDate(); day++) {
    //                     let date = new Date(year, month, day);
    //                     // Vérifie si le jour est un jour de week-end (samedi ou dimanche)
    //                     if (date.getDay() !== 0 && date.getDay() !== 6) {
    //                         daysInYear++;
    //                     }
    //                 }
    //             }
    //             // Ajoute le nombre de jours à côté de l'année
    //         setTimeout(function() {
    //         let markedDays = calendarElt.querySelectorAll('.mark');
    //         let numberOfMarkedDays = markedDays.length;
    //         console.log("Nombre de jours marqués : " + numberOfMarkedDays);
    //         let titleElement = calendarElt.querySelector('.fc-toolbar-title');
    //         titleElement.innerText = year + 'Nombre de jours oeuvrés :' + (daysInYear-numberOfMarkedDays) + ' jours';
    // }, 1);
    //         }

  });
  calendar.render();
});