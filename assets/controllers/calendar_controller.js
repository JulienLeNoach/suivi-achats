import { Controller } from '@hotwired/stimulus';
import { Calendar } from 'fullcalendar'

export default class extends Controller {


    initialize() {
      this.initCustomCalendar();
    }
     initCustomCalendar() {
        const calendarEl = document.getElementById('calendar')

        // const calendar = new Calendar(calendarEl, {
        //   initialView: 'dayGridMonth'
        // })
        const calendar = new Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'fr',
            timeZone: 'Europe/Paris',
            firstDay: 1,
            headerToolbar: {
                start: 'prev,next today',
                center: 'title',
                end: 'dayGridYear,dayGridMonth,timeGridWeek'
            },
            events: events, // Assurez-vous que 'events' est défini quelque part dans votre code
            eventContent: function (info) {
                // Votre logique personnalisée pour eventContent
                const dateStr = info.event.start.toISOString().slice(0, 10);
                const tdElts = document.querySelectorAll('td[data-date="' + dateStr + '"]');
                for (let i = 0; i < tdElts.length; i++) {
                    tdElts[i].classList.add('mark');
                }
                return {
                    html: '<style> td[data-date="' + info.event.start.toISOString().slice(0, 10) + '"]{background-color:' + info.backgroundColor + ';}</style>'
                };
            },
            dayCellDidMount: function(info) {
                if (info.date.getUTCDay() === 6 || info.date.getUTCDay() === 0) { // 6 = Samedi, 0 = Dimanche
                    info.el.style.backgroundColor = 'red';
                }
            }
        });
        calendar.render()

}
}