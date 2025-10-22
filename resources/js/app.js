import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import jaLocale from '@fullcalendar/core/locales/ja';

import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";

document.addEventListener('DOMContentLoaded', function () {
  const calendarEl = document.getElementById('calendar');
  if (!calendarEl) return;

  // API から予約可能日を取得
  fetch('/patients/reservations/available-dates')
    .then(res => res.json())
    .then(events => {
      const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin],       // 月表示プラグイン
        initialView: 'dayGridMonth',    // 初期表示を月表示に設定
        locale: jaLocale,               // 日本語
        events: events,                 // APIで取得したイベントを表示
        validRange: { start: new Date() }, // 過去の日付は表示させない
        eventContent: function (arg) {
          // arg.event.title は '○'
          const dot = document.createElement('div');
          dot.textContent = arg.event.title;
          dot.classList.add(
            'h-6', 'rounded-full', 'text-blue-500', 'text-lg', 'text-white',
            'flex', 'items-center', 'justify-center'
          );
          return { domNodes: [dot] };
        },
        eventClick: function (info) {
          const date = info.event.startStr; // クリックした日の文字列を取得
          window.location.href = `/reservations/slots?date=${date}`;
        }
      });

      calendar.render();
    })
    .catch(err => console.error(err));
});

flatpickr("#birth_date", {
  dateFormat: "Y-m-d",   // 送信するフォーマット
  altInput: true,        // 表示用にわかりやすいフォーマットを追加
  altFormat: "Y年m月d日", // ユーザーに見える形式
  disableMobile: true,   // スマホ対応
   locale: {             // 日本語対応
    months: {
      shorthand: ['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
      longhand:  ['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月']
    }
  },
});
