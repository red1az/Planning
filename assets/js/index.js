document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: [
            // Les événements sont injectés dynamiquement depuis PHP
        ],
        eventClick: function(info) {
            info.jsEvent.preventDefault();

            document.getElementById('modalTitle').innerText = info.event.title;
            document.getElementById('modalDescription').innerText = info.event.extendedProps.description;
            document.getElementById('modalStart').innerText = info.event.start.toLocaleDateString();
            document.getElementById('modalEnd').innerText = info.event.end ? info.event.end.toLocaleDateString() : 'Non spécifiée';
            document.getElementById('modalStatus').innerText = info.event.extendedProps.statut;

            document.getElementById('taskModal').style.display = 'block';
        }
    });
    calendar.render();

    document.querySelector('.modal .close').addEventListener('click', function() {
        document.getElementById('taskModal').style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target == document.getElementById('taskModal')) {
            document.getElementById('taskModal').style.display = 'none';
        }
    });
});
