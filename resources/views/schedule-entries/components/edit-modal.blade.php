@use(App\Support\ColorMapper)

<div class="modal fade" id="editScheduleEntryModal" tabindex="-1" aria-labelledby="editScheduleEntryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editScheduleEntryForm" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editScheduleEntryModalLabel">Изменить запись</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @csrf

                    <div class="alert alert-danger d-none" id="edit-error-container"></div>

                    <div class="mb-3">
                        <label for="edit-title" class="form-label">Название</label>
                        <input type="text" class="form-control" id="edit-title" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit-description" class="form-label">Описание</label>
                        <textarea class="form-control" id="edit-description" name="description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="edit-day_of_week" class="form-label">День недели</label>
                        <select class="form-select" id="edit-day_of_week" name="day_of_week" required>
                            @php
                                $days = [1 => 'Пн', 2 => 'Вт', 3 => 'Ср', 4 => 'Чт', 5 => 'Пт', 6 => 'Сб', 7 => 'Вс'];
                            @endphp
                            @foreach ($days as $dayNumber => $dayName)
                                <option value="{{ $dayNumber }}">{{ $dayName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit-start_time" class="form-label">Время начала</label>
                            <input type="time" class="form-control" id="edit-start_time" name="start_time" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit-end_time" class="form-label">Время окончания</label>
                            <input type="time" class="form-control" id="edit-end_time" name="end_time" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Теги</label>
                        <div class="d-flex flex-wrap gap-2" id="edit-tags-container">
                            @forelse ($tags as $tag)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="tags[]" id="edit_tag_{{ $tag->id }}" value="{{ $tag->id }}">
                                    <label class="form-check-label badge text-bg-{{ ColorMapper::colorToBsClass($tag->color) }}" for="edit_tag_{{ $tag->id }}">{{ $tag->name }}</label>
                                </div>
                            @empty
                                <p class="text-muted">У вас пока нет тегов. Создайте их на странице "Мои теги".</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" id="delete-button">Удалить</button>
                    <div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
