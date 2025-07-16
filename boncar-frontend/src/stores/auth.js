// boncar-frontend/src/stores/auth.js
import { defineStore } from 'pinia';
import api from '@/api'; // Impor instance Axios kita
import router from '@/router'; // Impor router untuk redirect

export const useAuthStore = defineStore('auth', {
  // STATE: Data reaktif yang akan disimpan
  state: () => ({
    user: null, // Akan berisi data pengguna jika sudah login (e.g., nama, email, role)
    token: localStorage.getItem('token') || null, // Mengambil token dari browser jika ada
  }),

  // GETTERS: Seperti computed properties, untuk mendapatkan state yang sudah diolah
  getters: {
    // Mengecek apakah pengguna sudah terotentikasi
    isAuthenticated: (state) => !!state.token && !!state.user,
    // Mengecek apakah pengguna adalah admin
    isAdmin: (state) => state.user?.role === 'admin',
  },

  // ACTIONS: Fungsi untuk mengubah state (seperti methods di komponen)
  actions: {
    // Fungsi untuk proses login
    async login(credentials) {
      // 1. Ambil CSRF cookie, ini wajib untuk otentikasi Sanctum
      await api.get('/sanctum/csrf-cookie');

      // 2. Kirim request login ke backend
      const response = await api.post('/api/login', credentials);
      const data = response.data;

      // 3. Simpan token dan data pengguna ke state
      this.token = data.access_token;
      this.user = data.user;

      // 4. Simpan token ke localStorage browser agar tidak hilang saat refresh
      localStorage.setItem('token', data.access_token);

      // 5. Set header Authorization untuk semua request Axios selanjutnya
      api.defaults.headers.common['Authorization'] = `Bearer ${data.access_token}`;
      
      // 6. Redirect ke halaman dashboard
      await router.push({ name: 'dashboard' });
    },

    // Fungsi untuk mengambil data pengguna yang sedang login
    async fetchUser() {
      // Hanya jalankan jika ada token tapi data user belum ada
      if (this.token && !this.user) {
        try {
          const response = await api.get('/api/user');
          this.user = response.data;
        } catch (error) {
          console.error('Gagal mengambil data pengguna:', error);
          // Jika token ternyata tidak valid, paksa logout
          this.logout();
        }
      }
    },

    // Fungsi untuk proses logout
    async logout() {
      try {
        await api.post('/api/logout');
      } catch (error) {
        console.error('Logout gagal di sisi server, tapi tetap lanjut di sisi client.', error);
      } finally {
        // Hapus semua data sesi dari state dan localStorage
        this.user = null;
        this.token = null;
        localStorage.removeItem('token');
        delete api.defaults.headers.common['Authorization'];
        // Arahkan ke halaman login
        await router.push({ name: 'login' });
      }
    },
  },
});