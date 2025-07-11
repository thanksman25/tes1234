import { defineStore } from 'pinia';
import api, { getCsrfCookie } from '@/services/api';
import router from '@/router'; // <-- PENTING: Impor router

// Definisikan tipe untuk data pengguna
interface User {
  id: number;
  name: string;
  email: string;
  role: 'admin' | 'user';
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as User | null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.user,
    userRole: (state) => state.user?.role,
  },

  actions: {
    async register(credentials: any) {
      await getCsrfCookie();
      await api.post('/api/register', credentials);
    },

    async login(credentials: { email: string, password: string }) {
      // 1. Lakukan "jabat tangan" dengan backend
      await getCsrfCookie();
      
      // 2. Kirim data login
      await api.post('/api/login', credentials);
      
      // 3. Ambil data user untuk disimpan di state
      await this.fetchUser();
      
      // 4. PERBAIKAN UTAMA: Arahkan ke Dashboard setelah state terisi
      if (this.user) {
        router.push({ name: 'Dashboard' });
      }
    },

    async logout() {
      try {
        await api.post('/api/logout');
      } finally {
        // Hapus data user dari state dan arahkan ke halaman login
        this.user = null;
        router.push({ name: 'Login' });
      }
    },

    async fetchUser() {
      try {
        const { data } = await api.get('/api/user');
        this.user = data;
      } catch (error) {
        this.user = null;
      }
    },
  },
});
