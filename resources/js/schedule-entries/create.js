import axios from 'axios';
import { handleResponseError } from '../helpers/responseErrorHandler.js';

document.addEventListener('DOMContentLoaded', () => {
    const createForm = document.getElementById('createScheduleEntryForm');
    const errorContainer = document.getElementById('create-error-container');

    if (createForm) {
        createForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const formData = new FormData(this);

            // Собираем выбранные теги и добавляем их в FormData
            createForm.querySelectorAll('input[name="tags[]"]:checked').forEach(checkbox => {
                formData.append('tags[]', checkbox.value);
            });

            axios.post(this.action, formData)
                .then(response => {
                    // В случае успеха, используем URL для редиректа из ответа сервера
                    if (response.data?.payload?.redirectUrl) {
                        window.location.href = response.data.payload.redirectUrl;
                    } else {
                        // Если URL не пришел, просто перезагружаем страницу
                        window.location.reload();
                    }
                })
                .catch(error => handleResponseError(error, errorContainer));
        });
    }

    // Очистка ошибок при закрытии модального окна
    const createModal = document.getElementById('createScheduleEntryModal');
    if (createModal) {
        createModal.addEventListener('hidden.bs.modal', function () {
            errorContainer.classList.add('d-none');
            errorContainer.innerHTML = '';
            createForm.reset();
        });
    }
});
