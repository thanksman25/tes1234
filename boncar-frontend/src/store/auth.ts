import { defineStore } from 'pinia';
import api, { getCsrfCookie } from '@/services/api';
import router from '@/router';

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
    async fetchUser() {
      try {
        console.log('[AuthStore] fetchUser: Memulai pengambilan data pengguna...');
        const { data } = await api.get('/api/user');
        console.log('[AuthStore] fetchUser: Data pengguna berhasil diterima:', data);
        this.user = data;
      } catch (error) {
        console.error('[AuthStore] fetchUser: Gagal mengambil data pengguna.', error);
        this.user = null;
      }
    },

    async login(credentials: { email: string, password: string }) {
      await getCsrfCookie();
      await api.post('/api/login', credentials);
      console.log('[AuthStore] login: Panggilan API login berhasil. Sekarang mengambil data pengguna...');
      await this.fetchUser();
    },

    async register(credentials: any) {
      await getCsrfCookie();
      await api.post('/api/register', credentials);
    },

    async logout() {
      await api.post('/api/logout');
      this.user = null;
      router.push({ name: 'Login' });
    },
  },
});
