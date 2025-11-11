@section('title', 'Мои теги')

@extends('layouts.app')

@use(App\Support\ColorMapper)

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Мои теги</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTagModal">
            Добавить тег
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                @forelse ($tags as $tag)
                    <button type="button" class="btn badge text-bg-{{ ColorMapper::colorToBsClass($tag->color) }} fs-6"
                            data-bs-toggle="modal" data-bs-target="#editDeleteTagModal"
                            data-tag='{{ json_encode($tag) }}'>
                        {{ $tag->name }}
                    </button>
                @empty
                    <p class="text-muted">У вас пока нет ни одного тега.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Подключаем модальные окна --}}
@include('tags.components.create-modal')
@include('tags.components.edit-delete-modal')
@include('tags.components.delete-confirm-modal')
@endsection

@push('scripts')
<script>
    // Передаем карты цветов в JavaScript для использования в формах
    window.colorMaps = {
        bsClassToColor: @json(ColorMapper::getBsClassToColorMap()),
        colorToBsClass: @json(ColorMapper::getColorToBsClassMap())
    };
</script>
@endpush
