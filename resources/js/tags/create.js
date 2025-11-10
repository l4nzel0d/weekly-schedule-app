import axios from 'axios';
import { handleResponseError } from '../helpers/responseErrorHandler.js';

document.addEventListener('DOMContentLoaded', () => {
    const createForm = document.getElementById('createTagForm');
    const errorContainer = document.getElementById('create-tag-error-container');

    if (createForm) {
        createForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const formData = new FormData(this);
            const bsClass = formData.get('bootstrap_color_class');
            const genericColor = window.colorMaps.bsClassToColor[bsClass];

            const payload = {
                name: formData.get('name'),
                color: genericColor,
            };

            axios.post(this.action, payload)
                .then(response => {
                    if (response.data?.payload?.redirectUrl) {
                        window.location.href = response.data.payload.redirectUrl;
                    } else {
                        window.location.reload();
                    }
                })
                .catch(error => handleResponseError(error, errorContainer));
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
