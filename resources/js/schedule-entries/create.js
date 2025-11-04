import axios from 'axios';

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
                    if (response.data && response.data.redirectUrl) {
                        window.location.href = response.data.redirectUrl;
                    } else {
                        // Если URL не пришел, просто перезагружаем страницу
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Ошибка при отправке формы:', error.response);
                    if (error.response && error.response.status === 422) {
                        // Ошибки валидации
                        const errors = error.response.data.errors;
                        let errorMessages = '<ul>';
                        for (const field in errors) {
                            errors[field].forEach(message => {
                                errorMessages += `<li>${message}</li>`;
                            });
                        }
                        errorMessages += '</ul>';

                        errorContainer.innerHTML = errorMessages;
                        errorContainer.classList.remove('d-none');
                    } else {
                        // Другие ошибки сервера
                        const status = error.response ? error.response.status : 'N/A';
                        errorContainer.innerHTML = `Произошла непредвиденная ошибка. Код: ${status}`;
                        errorContainer.classList.remove('d-none');
                    }
                });
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
