// #### File: src/store/dashboard.ts

import { defineStore } from 'pinia';
import api from '@/services/api';

// Definisikan tipe untuk statistik agar lebih aman
interface StatItem {
  title: string;
  value: number | string;
}

interface DashboardState {
  userStats: StatItem[];
  adminStats: StatItem[];
  loading: boolean;
  error: string | null;
}

export const useDashboardStore = defineStore('dashboard', {
  state: (): DashboardState => ({
    userStats: [],
    adminStats: [],
    loading: false,
    error: null,
  }),

  actions: {
    /**
     * Mengambil dan memetakan statistik untuk dashboard pengguna biasa.
     */
    async fetchUserStats() {
      this.loading = true;
      this.error = null;
      try {
        const { data } = await api.get('/dashboard/user-stats');
        
        // Transformasi data dari API ke format yang dibutuhkan oleh komponen
        this.userStats = [
          { title: 'Cadangan Karbon', value: `${data.carbon_stock} Ton/Ha` },
          { title: 'Pohon Yang Dihitung', value: data.trees },
          { title: 'Lokasi Yang Dihitung', value: data.locations },
        ];
      } catch (err: any) {
        this.error = 'Gagal memuat statistik pengguna.';
        console.error(err);
      } finally {
        this.loading = false;
      }
    },

    /**
     * Mengambil dan memetakan statistik untuk dashboard admin.
     */
    async fetchAdminStats() {
      this.loading = true;
      this.error = null;
      try {
        const { data } = await api.get('/admin/dashboard/admin-stats');
        
        // Transformasi data dari API ke format yang dibutuhkan oleh komponen
        this.adminStats = [
          { title: 'Pengguna', value: data.users },
          { title: 'Pengajuan Tertunda', value: data.pending_submissions },
          { title: 'Rumus Alometrik', value: data.formulas },
        ];
      } catch (err: any) {
        this.error = 'Gagal memuat statistik admin.';
        console.error(err);
      } finally {
        this.loading = false;
      }
    },
  },
});