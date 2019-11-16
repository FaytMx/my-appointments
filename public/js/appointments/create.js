let $doctor, $date, $specialty, $hours;
let iRadio;
const noHoursAlert = `<div class="alert alert-danger" role="alert">No se encontraron horas disponibles para el medico en el dia seleccionado</div>`;

$(function() {
    $specialty = $("#specialty");
    $doctor = $("#doctor");
    $date = $("#date");
    $hours = $("#hours");

    $specialty.change(() => {
        const specilatyId = $specialty.val();
        const url = `/specialties/${specilatyId}/doctors`;
        $.getJSON(url, onDoctorsLoaded);
    });

    $doctor.change(loadHours);
    $date.change(loadHours);
});

function onDoctorsLoaded(doctors) {
    // console.log(doctors);
    let htmlOptions = "";
    doctors.forEach(function(doctor) {
        // console.log(`${doctor.id} - ${doctor.name}`);
        htmlOptions += `<option value = "${doctor.id}">${doctor.name}</option>`;
    });
    $doctor.html(htmlOptions);
    loadHours();
}

function loadHours() {
    const selectedDate = $date.val();
    const doctorId = $doctor.val();
    const url = `/schedule/hours?date=${selectedDate}&doctor_id=${doctorId}`;
    $.getJSON(url, displayHours);
}

function displayHours(data) {
    // console.log(data);
    if (!data.morning && !data.afternoon) {
        console.log(
            "No se encontraron horas disponibles para el medico en el dia seleccionado"
        );
        $hours.html(noHoursAlert);
        return;
    }
    let htmlHours = '' ;
    iRadio = 0;

    if (data.morning) {
        const morning_intervals = data.morning;
        morning_intervals.forEach(interval => {
            // console.log(`${interval.start} - ${interval.end}`);
            htmlHours += getRadioIntervalHtml(interval);
        });
    }

    if (data.afternoon) {
        const afternoon_intervals = data.afternoon;
        afternoon_intervals.forEach(interval => {
            // console.log(`${interval.start} - ${interval.end}`);
            htmlHours += getRadioIntervalHtml(interval);
        });
    }
    $hours.html(htmlHours);
}

function getRadioIntervalHtml(interval) {
    const text = `${interval.start} - ${interval.end}`;
    return `<div class="custom-control custom-radio mb-3">
    <input name="scheduled_time" class="custom-control-input" id="interval${iRadio}" type="radio" value="${interval.start}" required>
    <label class="custom-control-label" for="interval${iRadio++}">${text}</label>
  </div>`;
}
