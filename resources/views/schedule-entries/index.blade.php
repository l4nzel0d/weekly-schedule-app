@extends('layouts.app')

@use(App\Support\ColorMapper)

@section('content')
<div class="container">
    <h1 class="mb-4">Моё расписание</h1>

    {{-- Переключатель дней недели --}}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ request('day') == 'all' || !request()->has('day') ? 'active' : '' }}" href="{{ route('schedule-entries.index', array_merge(request()->query(), ['day' => 'all'])) }}">Вся неделя</a>
        </li>
        @php
            $days = [1 => 'Пн', 2 => 'Вт', 3 => 'Ср', 4 => 'Чт', 5 => 'Пт', 6 => 'Сб', 7 => 'Вс'];
        @endphp
        @foreach ($days as $dayNumber => $dayName)
            <li class="nav-item">
                <a class="nav-link {{ request('day') == $dayNumber ? 'active' : '' }}" href="{{ route('schedule-entries.index', array_merge(request()->query(), ['day' => $dayNumber])) }}">{{ $dayName }}</a>
            </li>
        @endforeach
    </ul>

    {{-- Кнопка добавления записи --}}
    <div class="d-flex justify-content-end mb-3">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createScheduleEntryModal">
            Добавить запись
        </button>
    </div>

    {{-- Форма поиска и фильтрации --}}
    <form action="{{ route('schedule-entries.index') }}" method="GET" class="mb-3" id="schedule-filter-form">

        {{-- Скрытые поля для сохранения других параметров URL (например, 'day') --}}
        @foreach (request()->query() as $key => $value)
            @if ($key !== 'search' && $key !== 'tags')
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach

        <div class="row g-2">
            {{-- Колонка для Поиска --}}
            <div class="col-sm">
                <input type="text" name="search" class="form-control" placeholder="Поиск по названию или описанию..." value="{{ request('search') }}">
            </div>

            {{-- Колонка для нашего нового компонента "Tag Input" --}}
            <div class="col-sm-7">
                <div class="form-control d-flex flex-wrap align-items-center gap-1" id="tag-input-container" style="min-height: 38px; height: auto;">
                    {{-- Сюда JS будет добавлять бейджи выбранных тегов --}}
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-sm btn-light py-0 px-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Добавить тег">Добавить тег +</button>
                        <div class="dropdown-menu p-2" id="tag-selection-dropdown" style="width: 250px;">
                            {{-- Сюда JS будет добавлять пункты меню (теги) --}}
                        </div>
                    </div>
                </div>
                {{-- Скрытый контейнер для input'ов для отправки на сервер --}}
                <div class="d-none" id="tag-hidden-inputs"></div>
            </div>

            {{-- Колонка для кнопок "Применить" и "Сбросить" --}}
            <div class="col-sm-auto">
                <button type="submit" class="btn btn-primary">Применить</button>
                <a href="{{ route('schedule-entries.index') }}" class="btn btn-outline-secondary">Сбросить</a>
            </div>
        </div>
    </form>

    {{-- Скрипт для передачи данных из PHP в JavaScript --}}
    <script>
        window.scheduleFilter = {
            allTags: {!! json_encode($tags->keyBy('id')) !!},
            initialTagIds: {!! json_encode(request('tags', [])) !!}
        };
        // Карта цветов для JS
        window.colorMaps = {
            colorToBsClass: @json(ColorMapper::getColorToBsClassMap())
        };
    </script>

    {{-- Таблица с расписанием --}}
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">День недели</th>
                    <th scope="col">Время</th>
                    <th scope="col">Название</th>
                    <th scope="col">Описание</th>
                    <th scope="col">Теги</th>
                </tr>
            </thead>
            <tbody>
                @php $lastDay = null; @endphp
                @forelse ($groupedEntries as $day => $entries)
                    @foreach ($entries as $entry)
                        <tr class="schedule-row" id="entry-{{ $entry->id }}" data-entry='{{ json_encode($entry->load('tags')) }}' data-bs-toggle="modal" data-bs-target="#editScheduleEntryModal" style="cursor: pointer;">
                            <td>
                                @if ($day !== $lastDay)
                                    <strong>{{ $days[$day] }}</strong>
                                @endif
                            </td>
                            <td>{{ date('H:i', strtotime($entry->start_time)) }} - {{ date('H:i', strtotime($entry->end_time)) }}</td>
                            <td>{{ $entry->title }}</td>
                            <td>{{ $entry->description }}</td>
                            <td>
                                @foreach ($entry->tags as $tag)
                                    <span class="badge text-bg-{{ ColorMapper::colorToBsClass($tag->color) }}">{{ $tag->name }}</span>
                                @endforeach
                            </td>
                        </tr>
                        @php $lastDay = $day; @endphp
                    @endforeach
                @empty
                    <tr>
                        <td colspan="5" class="text-center">На выбранный день записей нет.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@include('schedule-entries.components.create-modal')
@include('schedule-entries.components.edit-modal', ['tags' => $tags])
@include('schedule-entries.components.delete-confirm-modal')
