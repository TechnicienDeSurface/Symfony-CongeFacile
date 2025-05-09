function calculerJoursOuvres() {
    var date1Element = document.getElementById('request_start_at'); //Quand j'inspecte ces ids apparaissent sans doute un attribut qui génére ça automatiquement
    var date2Element = document.getElementById('request_end_at');
    var joursOuvresElement = document.getElementById('request_working_days');

    if (!date1Element || !date2Element || !joursOuvresElement) {
        console.error("Un ou plusieurs éléments DOM sont introuvables.");
        return;
    }

    var date1 = new Date(date1Element.value);
    var date2 = new Date(date2Element.value);
    var joursOuvres = 0;

    console.log("Date1:", date1);
    console.log("Date2:", date2);

    if (date1 && date2 && date1 <= date2) {
        var currentDate = new Date(date1);
        while (currentDate <= date2) {
            var dayOfWeek = currentDate.getDay();
            if (dayOfWeek !== 0 && dayOfWeek !== 6) { // Exclure les dimanches (0) et samedis (6)
                joursOuvres++;
            }
            currentDate.setDate(currentDate.getDate() + 1);
        }
    }

    joursOuvresElement.value = joursOuvres;
}