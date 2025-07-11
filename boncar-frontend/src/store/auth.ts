import { defineStore } from 'pinia';
import api, { getCsrfCookie } from '@/services/api';
import router from '@/router'; // <-- PENTING: Impor router di sini

// Definisikan tipe untuk data pengguna
interface User {
  id: number;
  name: string;
  email: string;
  role: 'admin' | 'user';
}

export const useAuthStore = defineStore('auth', {
  // STATE: Hanya bergantung pada data user. Tidak ada lagi 'token'.
  state: () => ({
    user: null as User | null,
  }),

  // GETTERS: Status login hanya dicek dari keberadaan data user.
  getters: {
    isAuthenticated: (state) => !!state.user,
    userRole: (state) => state.user?.role,
  },

  actions: {
    // Fungsi ini untuk memeriksa apakah sesi di backend masih aktif
    async fetchUser() {
      try {
        const { data } = await api.get('/api/user');
        this.user = data;
      } catch (error) {
        this.user = null;
      }
    },

    async login(credentials: { email: string, password: string }) {
      // 1. Lakukan "jabat tangan" dengan backend
      await getCsrfCookie();
      
      // 2. Kirim data login. Jika gagal, error akan ditangkap di komponen.
      await api.post('/api/login', credentials);
      
      // 3. Setelah login berhasil, panggil fetchUser untuk mengisi state.
      await this.fetchUser();

      // 4. PERBAIKAN UTAMA: Arahkan ke Dashboard SETELAH state terisi
      if (this.user) {
        router.push({ name: 'Dashboard' });
      }
    },

    async register(credentials: any) {
      await getCsrfCookie();
      await api.post('/api/register', credentials);
    },

    async logout() {
      try {
        await api.post('/api/logout');
      } finally {
        // Selalu bersihkan state dan arahkan ke halaman login.
        this.user = null;
        router.push({ name: 'Login' });
      }
    },
  },
});