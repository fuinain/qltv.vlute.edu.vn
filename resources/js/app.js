//resources/js/app.js
import './bootstrap';
import { createApp } from 'vue';
import App from './App.vue';
import router from './router/index.js';
// Import Bootstrap CSS và JS


const app = createApp(App);
app.use(router);
app.mount('#app');
