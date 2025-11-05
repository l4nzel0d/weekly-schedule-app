@use(App\Support\ColorMapper)

<div class="modal fade" id="createTagModal" tabindex="-1" aria-labelledby="createTagModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="createTagForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="createTagModalLabel">Добавить тег</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @csrf

                    <div class="alert alert-danger d-none" id="create-tag-error-container"></div>

                    <div class="mb-3">
                        <label for="create-tag-name" class="form-label">Название тега</label>
                        <input type="text" class="form-control" id="create-tag-name" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Цвет тега</label>
                        <div>
                            @foreach (ColorMapper::getColors() as $color)
                                @php($bsClass = ColorMapper::colorToBsClass($color))
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="bootstrap_color_class" id="create-tag-color-{{ $bsClass }}" value="{{ $bsClass }}" {{ $color == 'grey' ? 'checked' : '' }}>
                                    <label class="form-check-label badge text-bg-{{ $bsClass }}" for="create-tag-color-{{ $bsClass }}">{{ __('colors.' . $color) }}</label>
                                </div>
                            @endforeach
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
