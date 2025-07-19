// #### File: src/store/userManagement.ts

import { defineStore } from 'pinia';
import api from '@/services/api';

// Definisikan tipe untuk data pengguna
export interface User {
  id: number;
  name: string;
  email: string;
  role: 'admin' | 'user';
  npm_nik?: string;
  institution?: string;
  phone_number?: string;
  created_at: string;
}

// Tipe untuk data dari paginasi Laravel
interface PaginatedUsers {
  data: User[];
  current_page: number;
  last_page: number;
  total: number;
}

interface UserManagementState {
  users: User[];
  pagination: {
    currentPage: number;
    lastPage: number;
    total: number;
  };
  loading: boolean;
  error: string | null;
}

export const useUserManagementStore = defineStore('userManagement', {
  state: (): UserManagementState => ({
    users: [],
    pagination: {
      currentPage: 1,
      lastPage: 1,
      total: 0,
    },
    loading: false,
    error: null,
  }),

  actions: {
    /**
     * Mengambil daftar pengguna dari backend dengan paginasi.
     */
    async fetchUsers(page = 1) {
      this.loading = true;
      this.error = null;
      try {
        const { data } = await api.get<PaginatedUsers>(`/admin/users?page=${page}`);
        this.users = data.data;
        this.pagination = {
          currentPage: data.current_page,
          lastPage: data.last_page,
          total: data.total,
        };
      } catch (err: any) {
        this.error = 'Gagal memuat data pengguna.';
        console.error(err);
      } finally {
        this.loading = false;
      }
    },

    /**
     * Mengupdate peran (role) seorang pengguna.
     */
    async updateUserRole(userId: number, role: 'admin' | 'user') {
      try {
        await api.put(`/admin/users/${userId}`, { role });
        // Perbarui data di state secara lokal agar UI reaktif
        const userIndex = this.users.findIndex(u => u.id === userId);
        if (userIndex !== -1) {
          this.users[userIndex].role = role;
        }
      } catch (err) {
        console.error(err);
        throw new Error('Gagal memperbarui peran pengguna.');
      }
    },

    /**
     * Menghapus seorang pengguna.
     */
    async deleteUser(userId: number) {
      try {
        await api.delete(`/admin/users/${userId}`);
        // Hapus pengguna dari state secara lokal
        this.users = this.users.filter(u => u.id !== userId);
      } catch (err) {
        console.error(err);
        throw new Error('Gagal menghapus pengguna.');
      }
    },
  },
});