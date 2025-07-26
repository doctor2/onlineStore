/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import './scripts/mercure.js';
import { custom } from './scripts/custom.js';

import { createApp } from 'vue';

import Cart from './components/Cart.vue';

if (document.querySelector('#app-cart')) {
    const app = createApp(Cart);
    app.mount('#app-cart');
}

// Автоматически скрывать через 3 секунды после появления
window.addEventListener('DOMContentLoaded', () => {
    custom.addHidingAlert();
    custom.addToCart();
});