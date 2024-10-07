import Vue from 'vue';
import App from './components/App.vue';
import router from './router'; // Impor router

// Render App.vue ke elemen dengan id "app"
new Vue({
    el: '#app',
    router, // Sertakan router di sini
    render: h => h(App),
});
