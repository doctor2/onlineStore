/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import './custom.js';

import { createApp } from 'vue';

// import Cart from './components/Cart.vue';
import Cart from './components/CartNew.vue';

if (document.querySelector('#app-cart')) {
    const app = createApp(Cart);
    app.mount('#app-cart');
}
