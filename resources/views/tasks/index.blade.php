<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Yapılacaklar Listesi</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
    
    <style>
        .task-form-container {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .calendar-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 30px;
        }
        .task-section {
            margin-bottom: 30px;
        }
        #calendar {
            max-width: 100%;
        }
        .nav-tabs {
            margin-bottom: 20px;
        }
        .tab-content {
            min-height: 400px;
        }
        .task-item {
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .task-with-date {
            border-left: 4px solid #007bff;
        }
        .task-without-date {
            border-left: 4px solid #6c757d;
        }
        .task-completed {
            border-left: 4px solid #28a745;
        }
        .date-badge {
            background-color: #e9ecef;
            color: #495057;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.8em;
        }
        .overdue {
            background-color: #fff5f5;
            border-left-color: #dc3545 !important;
        }
        .overdue .date-badge {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4 text-center">📝 Yapılacaklar Listesi</h1>

    {{-- Başarı/Hata Mesajları --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Görev Ekleme Formu --}}
    <div class="task-form-container">
        <h3 class="mb-3">Yeni Görev Ekle</h3>
        <form method="POST" action="/tasks" class="row g-3">
            @csrf
            <div class="col-md-8">
                <input type="text" name="title" class="form-control" placeholder="Görev başlığını girin..." required />
            </div>
            <div class="col-md-3">
                <input type="date" name="due_date" class="form-control" />
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100">Ekle</button>
            </div>
        </form>
    </div>

    {{-- Sekme Navigasyonu --}}
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tasks-tab" data-bs-toggle="tab" data-bs-target="#tasks" type="button" role="tab">
                📋 Görevler
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="calendar-tab" data-bs-toggle="tab" data-bs-target="#calendar-view" type="button" role="tab">
                📅 Takvim
            </button>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        {{-- Görevler Sekmesi --}}
        <div class="tab-pane fade show active" id="tasks" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    {{-- Yapılacaklar --}}
                    <div class="task-section">
                        <h3 class="text-primary">
                            <i class="fas fa-clock"></i> Yapılacaklar 
                            <span class="badge bg-primary">{{ $pendingTasks->count() }}</span>
                        </h3>
                        <div class="list-group">
                            @forelse($pendingTasks as $task)
                                @php
                                    $isOverdue = $task->due_date && \Carbon\Carbon::parse($task->due_date)->isPast();
                                    $hasDate = $task->due_date;
                                @endphp
                                <div class="list-group-item task-item {{ $isOverdue ? 'overdue' : ($hasDate ? 'task-with-date' : 'task-without-date') }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $task->title }}</h6>
                                            @if($task->due_date)
                                                <small class="date-badge">
                                                    @if($isOverdue)
                                                        ⚠️ Gecikti: {{ \Carbon\Carbon::parse($task->due_date)->format('d.m.Y') }}
                                                    @else
                                                        📅 {{ \Carbon\Carbon::parse($task->due_date)->format('d.m.Y') }}
                                                    @endif
                                                </small>
                                            @endif
                                        </div>
                                        <div class="btn-group" role="group">
                                            {{-- Tamamlandı Butonu --}}
                                            <form method="POST" action="{{ route('tasks.toggle', $task->id) }}" style="display:inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-success btn-sm" title="Tamamla">
                                                    ✓
                                                </button>
                                            </form>

                                            {{-- Sil Butonu --}}
                                            <form method="POST" action="/tasks/{{ $task->id }}" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" 
                                                        onclick="return confirm('Silmek istediğinize emin misiniz?');" 
                                                        title="Sil">
                                                    🗑️
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="list-group-item text-center text-muted">
                                    <i class="fas fa-check-circle fa-3x mb-3"></i>
                                    <p>Hiç yapılacak görev yok! 🎉</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    {{-- Tamamlananlar --}}
                    <div class="task-section">
                        <h3 class="text-success">
                            <i class="fas fa-check-circle"></i> Tamamlananlar 
                            <span class="badge bg-success">{{ $completedTasks->count() }}</span>
                        </h3>
                        <div class="list-group">
                            @forelse($completedTasks as $task)
                                <div class="list-group-item task-item task-completed">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 text-decoration-line-through text-muted">{{ $task->title }}</h6>
                                            @if($task->due_date)
                                                <small class="date-badge">
                                                    ✅ {{ \Carbon\Carbon::parse($task->due_date)->format('d.m.Y') }}
                                                </small>
                                            @endif
                                        </div>
                                        <div class="btn-group" role="group">
                                            {{-- Geri Al Butonu --}}
                                            <form method="POST" action="{{ route('tasks.toggle', $task->id) }}" style="display:inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-secondary btn-sm" title="Geri Al">
                                                    ↺
                                                </button>
                                            </form>

                                            {{-- Sil Butonu --}}
                                            <form method="POST" action="/tasks/{{ $task->id }}" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" 
                                                        onclick="return confirm('Silmek istediğinize emin misiniz?');" 
                                                        title="Sil">
                                                    🗑️
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="list-group-item text-center text-muted">
                                    <i class="fas fa-tasks fa-3x mb-3"></i>
                                    <p>Henüz tamamlanmış görev yok.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Takvim Sekmesi --}}
        <div class="tab-pane fade" id="calendar-view" role="tabpanel">
            <div class="calendar-container">
                <h3 class="mb-3">📅 Görev Takvimi</h3>
                <p class="text-muted">Takvimde bir tarihe tıklayarak o tarihe görev ekleyebilirsiniz.</p>
                
                {{-- Görev Ekleme Formu (Takvim için) --}}
                <div id="calendar-task-form" class="alert alert-info" style="display: none;">
                    <h5>Seçilen Tarihe Görev Ekle</h5>
                    <form method="POST" action="/tasks" class="row g-3">
                        @csrf
                        <div class="col-md-8">
                            <input type="text" name="title" id="calendar-task-title" class="form-control" placeholder="Görev başlığını girin..." required />
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="due_date" id="calendar-task-date" class="form-control" required />
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100">Ekle</button>
                        </div>
                    </form>
                    <button type="button" class="btn btn-secondary btn-sm mt-2" id="cancel-calendar-form">İptal</button>
                </div>

                <div id="calendar"></div>

                {{-- Takvim Renk Açıklamaları --}}
                <div class="mt-3">
                    <small class="text-muted">
                        <span class="badge bg-primary me-2">●</span> Bekleyen Görevler
                        <span class="badge bg-success me-2">●</span> Tamamlanan Görevler
                        <span class="badge bg-danger me-2">●</span> Geciken Görevler
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var taskForm = document.getElementById('calendar-task-form');
    var taskTitle = document.getElementById('calendar-task-title');
    var taskDate = document.getElementById('calendar-task-date');
    var cancelBtn = document.getElementById('cancel-calendar-form');
    
    // CSRF token'ı axios için ayarla
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'tr',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            axios.get('/api/tasks')
                .then(response => {
                    console.log('Takvim verileri:', response.data);
                    successCallback(response.data);
                })
                .catch(error => {
                    console.error('Takvim verisi alınırken hata:', error);
                    failureCallback(error);
                });
        },
        dateClick: function(info) {
            // Seçilen tarihi forma doldur
            taskDate.value = info.dateStr;
            taskForm.style.display = 'block';
            taskTitle.value = '';
            taskTitle.focus();
            
            // Forma scroll yap
            taskForm.scrollIntoView({ behavior: 'smooth' });
        },
        eventClick: function(info) {
            // Görev detaylarını göster
            var eventDate = info.event.start.toLocaleDateString('tr-TR');
            alert('Görev: ' + info.event.title + '\nTarih: ' + eventDate);
        },
        eventDidMount: function(info) {
            // Tooltip ekle
            info.el.title = info.event.title + ' (' + info.event.start.toLocaleDateString('tr-TR') + ')';
        }
    });

    // Takvim sekmesi aktif olduğunda takvimi render et
    document.getElementById('calendar-tab').addEventListener('shown.bs.tab', function (e) {
        calendar.render();
    });

    // İptal butonu
    cancelBtn.addEventListener('click', function() {
        taskForm.style.display = 'none';
    });

    // Otomatik alert kapat
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>
</body>
</html>