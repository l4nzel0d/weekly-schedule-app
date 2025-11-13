<div class="modal fade" id="deleteConfirmTagModal" tabindex="-1" aria-labelledby="deleteConfirmTagModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmTagModalLabel">Подтверждение</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Вы уверены, что хотите удалить следующий тег?</p>
                <div id="delete-tag-badge-container" class="text-center my-3">
                    <!-- Бейдж тега будет вставлен сюда -->
                </div>
                <div id="delete-tag-error-container" class="alert alert-danger d-none mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-tag-button">Удалить</button>
            </div>
        </div>
    </div>
</div>
