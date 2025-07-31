@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Список элементов</h1>
        <div>
            <a href="{{ route('items.create') }}" class="btn btn-primary">Добавить элемент</a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5>Действия с данными</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('items.generate') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success me-2">Сгенерировать 1000 строк</button>
            </form>
            <form action="{{ route('items.clear') }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" 
                        onclick="return confirm('Вы уверены? Все данные будут удалены!')">
                    Очистить таблицу
                </button>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5>Настройки Google Sheets</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('items.set-sheet') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="sheet_url" class="form-label">URL Google Таблицы</label>
                    <input type="url" class="form-control" id="sheet_url" name="sheet_url" 
                           value="{{ old('sheet_url', $sheetUrl ?? '') }}" required>
                </div>
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>Все элементы</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Статус</th>
                            <th>Описание</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->name }}</td>
                                <td>
                                    <span class="badge bg-{{ $item->status === 'Allowed' ? 'success' : 'danger' }}">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                <td>{{ Str::limit($item->description, 50) }}</td>
                                <td>
                                    <a href="{{ route('items.show', $item->id) }}" class="btn btn-sm btn-info">Просмотр</a>
                                    <a href="{{ route('items.edit', $item->id) }}" class="btn btn-sm btn-warning">Редактировать</a>
                                    <form action="{{ route('items.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $items->links() }}
        </div>
    </div>
@endsection