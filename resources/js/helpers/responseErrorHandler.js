/**
 * Отображает ошибки валидации в виде списка.
 * @param {object} errors - Объект ошибок от Laravel.
 * @param {HTMLElement} container - DOM-элемент для вывода ошибок.
 */
function displayValidationErrors(errors, container) {
    let errorMessages = '<ul>';
    for (const field in errors) {
        errors[field].forEach(message => {
            errorMessages += `<li>${message}</li>`;
        });
    }
    errorMessages += '</ul>';
    container.innerHTML = errorMessages;
}

/**
 * Отображает общее сообщение об ошибке.
 * @param {string} message - Сообщение об ошибке.
 * @param {HTMLElement} container - DOM-элемент для вывода.
 */
function displayGeneralError(message, container) {
    container.innerHTML = message;
}

/**
 * Централизованный обработчик ошибок AJAX-запросов.
 * Анализирует ошибку от axios и отображает ее в соответствующем контейнере.
 *
 * @param {Error} error - Объект ошибки, перехваченный в .catch()
 * @param {HTMLElement} errorContainerElement - DOM-элемент, в котором нужно показать ошибку.
 */
export function handleResponseError(error, errorContainerElement) {
    console.error('Ошибка ответа сервера:', error.response);

    // Сначала скрываем и очищаем контейнер
    errorContainerElement.innerHTML = '';
    errorContainerElement.classList.add('d-none');

    if (error.response) {
        // Сервер ответил с кодом ошибки (4xx, 5xx)
        const status = error.response.status;
        const data = error.response.data;

        // Проверяем, есть ли специфичные ошибки для полей (как при валидации)
        if (data.errors && Object.keys(data.errors).length > 0) {
            displayValidationErrors(data.errors, errorContainerElement);
        } else if (data.message) {
            // В противном случае показываем общее сообщение об ошибке
            displayGeneralError(data.message, errorContainerElement);
        } else {
            // Если в ответе нет ни message, ни errors
            displayGeneralError(`Произошла непредвиденная ошибка. Код: ${status}`, errorContainerElement);
        }
    } else {
        // Ошибка сети или другая проблема на стороне клиента
        displayGeneralError('Ошибка сети. Проверьте подключение и попробуйте снова.', errorContainerElement);
    }

    // Показываем контейнер с ошибкой
    errorContainerElement.classList.remove('d-none');
}
