@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h1>Просмотр элемента #{{ $item->id }}</h1>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Название</label>
                <p class="form-control-plaintext">{{ $item->name }}</p>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Статус</label>
                <p>
                    <span class="badge bg-{{ $item->status === 'Allowed' ? 'success' : 'danger' }}">
                        {{ $item->status }}
                    </span>
                </p>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Описание</label>
                <p class="form-control-plaintext">{{ $item->description }}</p>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Создан</label>
                <p class="form-control-plaintext">{{ $item->created_at }}</p>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Обновлен</label>
                <p class="form-control-plaintext">{{ $item->updated_at }}</p>
            </div>
            
            <div class="d-flex">
                <a href="{{ route('items.edit', $item->id) }}" class="btn btn-warning me-2">Редактировать</a>
                <form action="{{ route('items.destroy', $item->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Удалить</button>
                </form>
            </div>
        </div>
    </div>
@endsection