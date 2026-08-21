import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";

function escapeHtml(value) {
  return String(value)
    .replace(/&/g, "&amp;")
    .replace(/"/g, "&quot;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;");
}

export function dashboardCalendarInit() {
  const calendarEl = document.querySelector("#dashboardCalendar");

  if (!calendarEl) {
    return;
  }

  let events = [];
  try {
    events = JSON.parse(calendarEl.dataset.documentEvents || "[]");
  } catch (e) {
    events = [];
  }

  const calendar = new Calendar(calendarEl, {
    plugins: [dayGridPlugin],
    initialView: "dayGridMonth",
    height: "auto",
    aspectRatio: 2.1,
    headerToolbar: {
      left: "prev,next",
      center: "title",
      right: "",
    },
    events,
    displayEventTime: false,
    dayMaxEvents: 1,
    eventContent(eventInfo) {
      const colorClass = `fc-bg-${eventInfo.event.extendedProps.calendar.toLowerCase()}`;
      return {
        html: `
          <div class="fc-event-dot-tip" data-title="${escapeHtml(eventInfo.event.title)}">
            <span class="fc-event-dot ${colorClass}"></span>
          </div>
        `,
      };
    },
  });

  calendar.render();
}

export default dashboardCalendarInit;
