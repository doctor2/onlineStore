// Функция для скрытия уведомления с эффектом исчезновения
function hideAlert() {
    const alert = document.querySelector('.alert.alert-success');
    if (alert) {
        alert.classList.add('fade-out');
        // Удаляем элемент из DOM через 0.5 секунды после применения класса
        setTimeout(() => {
            alert.remove();
        }, 500);
    }
}

function addHidingAlert() {
    const alert = document.querySelector('.alert.alert-success');
    if (alert) {
        setTimeout(hideAlert, 3000);
    }
}

function addToCart() {
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();

            const productId = button.getAttribute('data-product-id');

            fetch(`/api/v1/cart/products/${productId}/increase`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
            })
                .then(response => response.json())
                .then(data => {
                    document.querySelectorAll('.alert').forEach(el => el.remove());

                    let alertDiv = document.createElement('div');

                    if (data.error) {
                        alertDiv.className = 'alert alert-error';
                        alertDiv.textContent = data.error;
                    } else if (data.success) {
                        alertDiv.className = 'alert alert-success';
                        alertDiv.textContent = data.success;
                    } else {
                        alertDiv.className = 'alert alert-error';
                        alertDiv.textContent = 'Неизвестный ответ от сервера';
                    }

                    const navigation = document.querySelector('.navigation');
                    if (navigation) {
                        navigation.parentNode.insertBefore(alertDiv, navigation);
                    }
                    addHidingAlert();
                })
                .catch(error => {
                    console.error('Ошибка при отправке запроса:', error);
                    // Удаляем предыдущие alerts, если есть
                    document.querySelectorAll('.alert').forEach(el => el.remove());

                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'alert alert-error';
                    errorDiv.textContent = 'Произошла ошибка при отправке запроса.';
                    const navigation = document.querySelector('.navigation');
                    if (navigation) {
                        navigation.parentNode.insertBefore(errorDiv, navigation);
                    }
                });
        });
    });
}

export const custom = {
    addHidingAlert,
    addToCart,
};