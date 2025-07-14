<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <title>Laravel FullCalendar</title>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        /* Takvimi sayfa ortasına hizalamak için basit stil */
        body {
            font-family: Arial, sans-serif;
            margin: 40px auto;
            max-width: 900px;
        }
        #calendar {
            max-width: 900px;
            margin: 0 auto;
        }
    </style>
</head>
<body>

<h2>Görev Takvimi</h2>
<h3>Yeni Görev Ekle</h3>
<form method="POST" action="{{ route('tasks.store') }}">
    @csrf
    <label for="title">Görev Başlığı:</label><br>
    <input type="text" name="title" id="title" required><br><br>

    <label for="due_date">Bitiş Tarihi:</label><br>
    <input type="date" name="due_date" id="due_date"><br><br>

    <button type="submit">Kaydet</button>
</form>
<hr>

<div id='calendar'></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        dateClick: function(info) {
            // Takvimde tarih tıklanınca due_date inputunu doldur
            document.getElementById('due_date').value = info.dateStr;
            // İsteğe bağlı: Başlık inputuna odaklan
            document.getElementById('title').focus();
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            axios.get('/api/tasks')
                .then(response => {
                    let events = response.data.map(task => ({
                        id: task.id,
                        title: task.title,
                        start: task.start
                    }));
                    successCallback(events);
                })
                .catch(error => {
                    console.error('Takvim verisi alınırken hata:', error);
                    failureCallback(error);
                });
        },
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        }
    });

    calendar.render();
});
</script>

</body>
</html>
