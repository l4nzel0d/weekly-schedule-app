@use(App\Support\ColorMapper)

<div class="modal fade" id="createScheduleEntryModal" tabindex="-1" aria-labelledby="createScheduleEntryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="createScheduleEntryForm" method="POST" action="{{ route('schedule-entries.store') }}">
                <div class="modal-header">
                    <h5 class="modal-title" id="createScheduleEntryModalLabel">Добавить запись</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @csrf

                    <div class="alert alert-danger d-none" id="create-error-container"></div>

                    <div class="mb-3">
                        <label for="create-title" class="form-label">Название</label>
                        <input type="text" class="form-control" id="create-title" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="create-description" class="form-label">Описание</label>
                        <textarea class="form-control" id="create-description" name="description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Дни недели</label>
                        <div>
                            @php
                                $days = [1 => 'Пн', 2 => 'Вт', 3 => 'Ср', 4 => 'Чт', 5 => 'Пт', 6 => 'Сб', 7 => 'Вс'];
                            @endphp
                            @foreach ($days as $dayNumber => $dayName)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="days_of_week[]" id="create_day_{{ $dayNumber }}" value="{{ $dayNumber }}">
                                    <label class="form-check-label" for="create_day_{{ $dayNumber }}">{{ $dayName }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="create-start_time" class="form-label">Время начала</label>
                            <input type="time" class="form-control" id="create-start_time" name="start_time" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="create-end_time" class="form-label">Время окончания</label>
                            <input type="time" class="form-control" id="create-end_time" name="end_time" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Теги</label>
                        <div class="d-flex flex-wrap gap-2">
                            @forelse ($tags as $tag)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="tags[]" id="create_tag_{{ $tag->id }}" value="{{ $tag->id }}">
                                    <label class="form-check-label badge text-bg-{{ ColorMapper::colorToBsClass($tag->color) }}" for="create_tag_{{ $tag->id }}">{{ $tag->name }}</label>
                                </div>
                            @empty
                                <p class="text-muted">У вас пока нет тегов. Создайте их на странице "Мои теги".</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>
