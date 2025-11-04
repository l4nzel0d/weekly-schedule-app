@extends('layouts.app')

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

    {{-- Форма поиска --}}
    <form action="{{ route('schedule-entries.index') }}" method="GET" class="mb-3">
        {{-- Добавляем скрытые поля для всех текущих параметров, кроме самого поиска --}}
        @foreach (request()->query() as $key => $value)
            @if ($key !== 'search')
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach

        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Поиск по названию или описанию..." value="{{ request('search') }}">
            <button class="btn btn-outline-secondary" type="submit">Найти</button>
            @if (request('search'))
                <a href="{{ route('schedule-entries.index', array_diff_key(request()->query(), ['search' => null])) }}" class="btn btn-outline-danger">Сбросить</a>
            @endif
        </div>
    </form>

    {{-- Таблица с расписанием --}}
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">День недели</th>
                    <th scope="col">Время</th>
                    <th scope="col">Название</th>
                    <th scope="col">Описание</th>
                </tr>
            </thead>
            <tbody>
                @php $lastDay = null; @endphp
                @forelse ($groupedEntries as $day => $entries)
                    @foreach ($entries as $entry)
                        <tr class="schedule-row" id="entry-{{ $entry->id }}" data-entry='{{ json_encode($entry) }}' data-bs-toggle="modal" data-bs-target="#editScheduleEntryModal" style="cursor: pointer;">
                            <td>
                                @if ($day !== $lastDay)
                                    <strong>{{ $days[$day] }}</strong>
                                @endif
                            </td>
                            <td>{{ date('H:i', strtotime($entry->start_time)) }} - {{ date('H:i', strtotime($entry->end_time)) }}</td>
                            <td>{{ $entry->title }}</td>
                            <td>{{ $entry->description }}</td>
                        </tr>
                        @php $lastDay = $day; @endphp
                    @endforeach
                @empty
                    <tr>
                        <td colspan="4" class="text-center">На выбранный день записей нет.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@include('schedule.components.create-modal')
@include('schedule.components.edit-modal')
@include('schedule.components.delete-confirm-modal')
