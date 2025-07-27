import { custom } from './custom.js';
// логика для получения токена через mercure-token и установка его в куки, сейчас кука ставиться автоматом при вызове в твиг mercure(
// import { MercureSubscriber } from './mercure-subscriber.js';

// const topic = document.getElementById("mercure-url").textContent.trim();

// if(topic) {
//     const subscriber = new MercureSubscriber({
//         topic: topic,
//         tokenUrl: '/api/v1/mercure-token',
//         hubUrl: 'http://localhost:3000/.well-known/mercure',
//         onMessage: (data) => {
//             showMessage('🔔 Уведомление: ' + data.message)
//         }
//     });
//
//     subscriber.connect();
// }

const url = document.getElementById("mercure-url").textContent.trim();

if(url) {
    const eventSource = new EventSource(url, {
        withCredentials: true,
    });

    eventSource.onmessage = event => {
        let data = JSON.parse(event.data);
        showMessage(data.message);
    }
}

function showMessage(message)
{
    let alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success';
    alertDiv.textContent = message;

    const navigation = document.querySelector('.navigation');
    if (navigation) {
        navigation.parentNode.insertBefore(alertDiv, navigation);
    }

    custom.addHidingAlert();
}