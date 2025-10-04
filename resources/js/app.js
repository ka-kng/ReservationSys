import './bootstrap';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import jaLocale from '@fullcalendar/core/locales/ja';

document.addEventListener('DOMContentLoaded', function () {
  const calendarEl = document.getElementById('calendar');
  if (!calendarEl) return;

  // API から予約可能日を取得
  fetch('/patients/reservations/available-dates')
    .then(res => res.json())
    .then(events => {
      const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin],
        initialView: 'dayGridMonth',
        locale: jaLocale,
        events: events,
        eventContent: function (arg) {
          // arg.event.title は '○'
          const dot = document.createElement('div');
          dot.textContent = arg.event.title;
          dot.classList.add(
            'h-6', 'rounded-full', 'text-blue-500', 'text-lg',
            'flex', 'items-center', 'justify-center'
          );
          return { domNodes: [dot] };
        },
        eventClick: function (info) {
          const date = info.event.startStr;
          window.location.href = `/reservations/slots?date=${date}`;
        }
      });

      calendar.render();
    })
    .catch(err => console.error(err));
});
