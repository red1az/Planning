$(document).ready(function() {
    const slider = $("#time-range-slider");
    const startTimeInput = $("#heure_debut");
    const endTimeInput = $("#heure_fin");
    const startTimeDisplay = $("#start-time-display");
    const endTimeDisplay = $("#end-time-display");

    function formatTime(value) {
        const hours = Math.floor(value / 60);
        const minutes = value % 60;
        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
    }

    // Convertir les heures de début et de fin existantes en minutes
    const startMinutes = parseInt(startTimeInput.val().split(':')[0]) * 60 + parseInt(startTimeInput.val().split(':')[1]);
    const endMinutes = parseInt(endTimeInput.val().split(':')[0]) * 60 + parseInt(endTimeInput.val().split(':')[1]);

    slider.slider({
        range: true,
        min: 0,
        max: 1440, // minutes in a day (24 * 60)
        step: 5, // 5-minute intervals
        values: [startMinutes, endMinutes], // Initialiser le slider avec les heures existantes
        slide: function(event, ui) {
            const start = formatTime(ui.values[0]);
            const end = formatTime(ui.values[1]);
            startTimeInput.val(start);
            endTimeInput.val(end);
            startTimeDisplay.text(`de ${start} à ${end}`);
        }
    });

    // Initialiser l'affichage avec les valeurs actuelles
    const start = formatTime(slider.slider("values", 0));
    const end = formatTime(slider.slider("values", 1));
    startTimeInput.val(start);
    endTimeInput.val(end);
    startTimeDisplay.text(`de ${start} à ${end}`);

    // Masquer le texte supplémentaire de l'élément endTimeDisplay
    endTimeDisplay.hide();
});

function toggleEndDate() {
    const sameDayCheckbox = document.getElementById('same_day');
    const dateFin = document.getElementById('date_fin');
    if (sameDayCheckbox.checked) {
        dateFin.value = document.getElementById('date_debut').value;
        dateFin.disabled = true;
    } else {
        dateFin.disabled = false;
    }
}
