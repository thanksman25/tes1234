import { defineStore } from 'pinia';
import api, { getCsrfCookie } from '@/services/api';

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
    
    // === FUNGSI YANG DIPERBAIKI ===
    async login(credentials: { email: string, password: string }) {
      await getCsrfCookie();
      
      // Panggil API login dan simpan responsnya
      const response = await api.post('/api/login', credentials);

      // Cek jika login berhasil dan ada data pengguna di respons
      if (response.data && response.data.user) {
        // Langsung set state user dari data yang sudah didapat,
        // tidak perlu memanggil fetchUser() lagi.
        this.user = response.data.user;
      } else {
        // Jika respons tidak sesuai harapan, reset state
        this.user = null;
      }
    },
    // === AKHIR FUNGSI YANG DIPERBAIKI ===

    async register(credentials: any) {
      await getCsrfCookie();
      await api.post('/api/register', credentials);
    },
    async logout() {
      await api.post('/api/logout');
      this.user = null;
    },
  },
});