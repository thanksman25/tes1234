// #### File: src/main.ts

// PERUBAHAN DI SINI: Impor CSS Global
import '@/assets/css/main.css';

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './App.vue'
import { useAuthStore } from './store/auth';

const app = createApp(App);

// 1. Gunakan Pinia terlebih dahulu agar store bisa dibuat
app.use(createPinia());

// 2. Buat instance authStore SETELAH Pinia diinisialisasi
const authStore = useAuthStore();

// 3. Panggil fetchUser() dan TUNGGU hingga selesai sebelum me-mount aplikasi
authStore.fetchUser().then(() => {
  // 4. Setelah status login diketahui, baru gunakan router dan mount aplikasi
  app.use(router);
  app.mount('#app');
});