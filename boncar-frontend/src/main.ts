// src/main.ts

import '@/assets/css/main.css';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';
import App from './App.vue';
import { useAuthStore } from './store/auth';

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);

// Dapatkan store setelah pinia di-install
const authStore = useAuthStore();

// Coba ambil data pengguna (memeriksa sesi) SEBELUM aplikasi di-mount.
// Ini mencegah layar berkedip dari halaman login ke dashboard.
authStore.fetchUser().then(() => {
  app.mount('#app');
});
