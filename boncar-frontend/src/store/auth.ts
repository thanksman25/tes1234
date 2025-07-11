import { defineStore } from 'pinia';
import api, { getCsrfCookie } from '@/services/api';
import router from '@/router';

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
      try {
        await api.post('/api/logout');
      } finally {
        this.user = null;
        router.push({ name: 'Login' });
      }
    },
  },
});