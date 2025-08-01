<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тестовое задание ООО Центр Помощи Должникам</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route('home') }}">Главная</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('items.index') }}">Элементы</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('items.create') }}">Добавить элемент</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('sync') }}">Выгрузить в таблицу</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('fetch') }}">Получить комментарии</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>