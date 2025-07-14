<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel FullCalendar</title>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px auto;
            max-width: 900px;
            background-color: #f8f9fa;
        }
        #calendar {
            max-width: 900px;
            margin: 0 auto 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
        }
        #task-form {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: none;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        input[type="text"], input[type="date"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        button {
            padding: 10px 20px;
            margin-right: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        button[type="submit"] {
            background-color: #007bff;
            color: white;
        }
        button[type="submit"]:hover {
            background-color: #0056b3;
        }
        #cancel-btn {
            background-color: #6c757d;
            color: white;
        }
        #cancel-btn:hover {
            background-color: #5a6268;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        .nav-links {
            text-align: center;
            margin-bottom: 20px;
        }
        .nav-links a {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .nav-links a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="nav-links">
    <a href="/">Görev Listesi</a>
    <a href="/calendar">Takvim</a>
</div>

@if(session('success'))
    <div class="success-message">
        {{ session('success') }}
    </div>
@endif

<h2>Görev Takvimi</h2>
<p>Takvimde bir tarihe tıklayarak o tarihe görev ekleyebilirsiniz.</p>

<div id='calendar'></div>

<!-- Görev Ekleme Formu -->
<div id="task-form">
    <h3>Yeni Görev Ekle</h3>
    <form method="POST" action="{{ route('tasks.store') }}">
        @csrf
        <div class="form-group">
            <label for="title">Görev Başlığı:</label>
            <input type="text" name="title" id="title" required>
        </div>

        <div class="form-group">
            <label for="due_date">Bitiş Tarihi:</label>
            <input type="date" name="due_date" id="due_date" required>
        </div>

        <button type="submit">Kaydet</button>
        <button type="button" id="cancel-btn">İptal</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var taskForm = document.getElementById('task-form');
    var dueDateInput = document.getElementById('due_date');
    var cancelBtn = document.getElementById('cancel-btn');
    
    // CSRF token'ı axios için ayarla
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        locale: 'tr', // Türkçe yerelleştirme
        events: function(fetchInfo, successCallback, failureCallback) {
            axios.get('/api/tasks')
                .then(response => {
                    console.log('Gelen veri:', response.data); // Debug için
                    successCallback(response.data);
                })
                .catch(error => {
                    console.error('Takvim verisi alınırken hata:', error);
                    failureCallback(error);
                });
        },
        dateClick: function(info) {
            // Seçilen tarihi form alanına doldur
            dueDateInput.value = info.dateStr;
            taskForm.style.display = 'block';
            document.getElementById('title').value = '';
            document.getElementById('title').focus();
            
            // Sayfayı forma scroll yap
            taskForm.scrollIntoView({ behavior: 'smooth' });
        },
        eventClick: function(info) {
            // Görev detaylarını göster
            alert('Görev: ' + info.event.title + '\nTarih: ' + info.event.start.toLocaleDateString('tr-TR'));
        }
    });

    calendar.render();

    // İptal butonu
    cancelBtn.addEventListener('click', function() {
        taskForm.style.display = 'none';
    });
});
</script>

</body>
</html>