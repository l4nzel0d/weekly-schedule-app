@use(App\Support\ColorMapper)

<div class="modal fade" id="editDeleteTagModal" tabindex="-1" aria-labelledby="editDeleteTagModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editTagForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDeleteTagModalLabel">Редактировать тег</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @csrf
                    @method('PUT')

                    <div class="alert alert-danger d-none" id="edit-tag-error-container"></div>

                    <div class="mb-3">
                        <label for="edit-tag-name" class="form-label">Название тега</label>
                        <input type="text" class="form-control" id="edit-tag-name" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Цвет тега</label>
                        <div>
                            @foreach (ColorMapper::getColors() as $color)
                                @php($bsClass = ColorMapper::colorToBsClass($color))
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="bootstrap_color_class" id="edit-tag-color-{{ $bsClass }}" value="{{ $bsClass }}">
                                    <label class="form-check-label badge text-bg-{{ $bsClass }}" for="edit-tag-color-{{ $bsClass }}">{{ __('colors.' . $color) }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" id="delete-tag-button">Удалить</button>
                    <div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
