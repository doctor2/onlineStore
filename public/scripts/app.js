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

// Автоматически скрывать через 3 секунды после появления
window.addEventListener('DOMContentLoaded', () => {
    const alert = document.querySelector('.alert.alert-success');
    if (alert) {
        setTimeout(hideAlert, 5000); // через 3 секунды
    }
});