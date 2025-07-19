// #### File: src/store/auth.ts

import { defineStore } from 'pinia';
import api from '@/services/api';
import axios from 'axios';

// Definisikan tipe untuk data pengguna
interface User {
  id: number;
  name: string;
  email: string;
  role: 'admin' | 'user';
  npm_nik?: string;
  institution?: string;
  phone_number?: string;
  email_verified_at: string | null;
}

// Tipe untuk data yang akan dikirim ke API update
interface UpdateProfilePayload {
  name: string;
  email: string;
  npm_nik?: string;
  institution?: string;
  phone_number?: string;
}

// Tipe untuk data registrasi
interface RegisterPayload {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
}


export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as User | null,
    token: localStorage.getItem('token') || null as string | null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token && !!state.user,
    userRole: (state) => state.user?.role,
  },

  actions: {
    /**
     * Aksi untuk mendaftarkan pengguna baru.
     */
    async register(payload: RegisterPayload) {
      // Sama seperti login, kita perlu CSRF cookie terlebih dahulu
      const rootUrl = import.meta.env.VITE_API_URL.replace('/api', '');
      await axios.get(`${rootUrl}/sanctum/csrf-cookie`, { withCredentials: true });

      // Kirim data registrasi ke backend
      await api.post('/register', payload);
    },

    /**
     * Aksi untuk menangani proses login.
     */
    async login(credentials: { email: string, password: string }) {
      const rootUrl = import.meta.env.VITE_API_URL.replace('/api', '');
      await axios.get(`${rootUrl}/sanctum/csrf-cookie`, { withCredentials: true });
      
      const { data } = await api.post('/login', credentials);
      this.token = data.access_token;
      this.user = data.user;
      localStorage.setItem('token', data.access_token);
      api.defaults.headers.common['Authorization'] = `Bearer ${data.access_token}`;
    },

    /**
     * Aksi untuk logout.
     */
    async logout() {
      try {
        await api.post('/logout');
      } catch (error) {
        console.error("Logout di server gagal, token akan dihapus di client saja.", error);
      } finally {
        this.user = null;
        this.token = null;
        localStorage.removeItem('token');
        delete api.defaults.headers.common['Authorization'];
      }
    },

    /**
     * Aksi untuk mengambil data pengguna jika token sudah ada.
     */
    async fetchUser() {
      if (this.token && !this.user) {
        try {
          api.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
          const { data } = await api.get('/user');
          this.user = data;
        } catch (error) {
          console.error("Gagal fetch user, token mungkin tidak valid.", error);
          await this.logout();
        }
      }
    },

    /**
     * Aksi untuk memperbarui profil pengguna.
     */
    async updateProfile(payload: UpdateProfilePayload) {
      const { data } = await api.put('/profile', payload);
      this.user = data.user;
    },
  },
});