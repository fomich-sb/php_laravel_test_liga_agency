@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h1>Редактирование элемента #{{ $item->id }}</h1>
        </div>
        <div class="card-body">
            <form action="{{ route('items.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="name" class="form-label">Название</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $item->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="status" class="form-label">Статус</label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="Allowed" {{ old('status', $item->status) === 'Allowed' ? 'selected' : '' }}>Allowed</option>
                        <option value="Prohibited" {{ old('status', $item->status) === 'Prohibited' ? 'selected' : '' }}>Prohibited</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Описание</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $item->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <button type="submit" class="btn btn-primary">Обновить</button>
                <a href="{{ route('items.index') }}" class="btn btn-secondary">Отмена</a>
            </form>
        </div>
    </div>
@endsection