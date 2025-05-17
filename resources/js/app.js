//resources/js/app.js
import './bootstrap';
import { createApp } from 'vue';
import App from './App.vue';
import router from './router/index.js';
// Import Bootstrap CSS và JS
// import 'bootstrap/dist/css/bootstrap.min.css';
// import 'bootstrap/dist/js/bootstrap.bundle.min.js';
// import 'bootstrap-icons/font/bootstrap-icons.css';

const app = createApp(App);
app.use(router);
app.mount('#app');
