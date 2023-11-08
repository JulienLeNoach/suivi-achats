import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    initialize() {
      this.initCustomCalendar();
    }
     initCustomCalendar() {
        let calendarElt = document.querySelector("#calendrier");
        let calendar = new FullCalendar.Calendar(calendarElt, {
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
            eventContent: function (info) {
                const dateStr = info.event.start.toISOString().slice(0, 10);
                const tdElts = document.querySelectorAll('td[data-date="' + dateStr + '"]');
                for (let i = 0; i < tdElts.length; i++) {
                    tdElts[i].classList.add('mark');
                }
                return {
                    html: '<style> td[data-date="' + info.event.start.toISOString().slice(0, 10) + '"]{background-color:' + info.backgroundColor + ';}</style>'
                };
            }
        });
        calendar.render();
}
}