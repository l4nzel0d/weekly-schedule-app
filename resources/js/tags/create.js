import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    const createForm = document.getElementById('createTagForm');
    const errorContainer = document.getElementById('create-tag-error-container');

    if (createForm) {
        createForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const formData = new FormData(this);

            axios.post(this.action, formData)
                .then(response => {
                    if (response.data && response.data.redirectUrl) {
                        window.location.href = response.data.redirectUrl;
                    } else {
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Ошибка при отправке формы создания тега:', error.response);
                    if (error.response && error.response.status === 422) {
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
                        const status = error.response ? error.response.status : 'N/A';
                        errorContainer.innerHTML = `Произошла непредвиденная ошибка. Код: ${status}`;
                        errorContainer.classList.remove('d-none');
                    }
                });
        });
    }

    const createModal = document.getElementById('createTagModal');
    if (createModal) {
        createModal.addEventListener('hidden.bs.modal', function () {
            errorContainer.classList.add('d-none');
            errorContainer.innerHTML = '';
            createForm.reset();
            // Устанавливаем цвет по умолчанию обратно на secondary
            createForm.querySelector('#create-tag-color-secondary').checked = true;
        });
    }
});
