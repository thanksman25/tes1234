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
        const { data } = await api.get('/api/user');
        this.user = data;
      } catch (error) {
        this.user = null;
      }
    },

    async login(credentials: { email: string, password: string }) {
      await getCsrfCookie();
      await api.post('/api/login', credentials);
      await this.fetchUser();
    },

    async register(credentials: any) {
      await getCsrfCookie();
      await api.post('/api/register', credentials);
    },

    async logout() {
      await api.post('/api/logout');
      this.user = null;
      // Redirect dari sini untuk memastikan pengguna langsung keluar
      router.push({ name: 'Login' });
    },
  },
});