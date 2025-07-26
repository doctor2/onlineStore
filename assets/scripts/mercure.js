import { custom } from './custom.js';

const url = new URL('http://localhost:3000/.well-known/mercure');
url.searchParams.append('topic', 'http://localhost/notify/1');

const eventSource = new EventSource(url);

eventSource.onmessage = event => {
    let alertDiv = document.createElement('div');
    let data = JSON.parse(event.data);

    alertDiv.className = 'alert alert-success';
    alertDiv.textContent = data.message;

    const navigation = document.querySelector('.navigation');
    if (navigation) {
        navigation.parentNode.insertBefore(alertDiv, navigation);
    }

    custom.addHidingAlert();
};