import { defineStore } from 'pinia';
import api from '@/services/api';

// Definisikan tipe data untuk daftar proyek
interface Project {
  id: number;
  project_name: string;
  created_at: string;
}

// Definisikan tipe data untuk detail proyek yang diterima dari API
interface ProjectDetail {
  project: {
    id: number;
    project_name: string;
    land_area: number;
    village: string;
    district: string;
    city: string;
    province: string;
    method: 'census' | 'sampling';
    created_at: string; // <-- TAMBAHKAN PROPERTI INI
  };
  trees: any[];
  total_biomass_ton: number;
  total_carbon_ton: number;
  biomass_per_ha_ton: number;
  carbon_per_ha_ton: number;
  settings: { [key: string]: string };
}

interface RecapState {
  projects: Project[];
  selectedProject: ProjectDetail | null;
  loading: boolean;
  error: string | null;
}

export const useRecapStore = defineStore('recap', {
  state: (): RecapState => ({
    projects: [],
    selectedProject: null,
    loading: false,
    error: null,
  }),

  actions: {
    /**
     * Mengambil daftar semua riwayat proyek kalkulasi.
     */
    async fetchProjects() {
      this.loading = true;
      this.error = null;
      try {
        const { data } = await api.get<Project[]>('/calculator-projects');
        this.projects = data;
      } catch (err) {
        this.error = 'Gagal memuat riwayat rekapitulasi.';
        console.error(err);
      } finally {
        this.loading = false;
      }
    },

    /**
     * Mengambil detail satu proyek berdasarkan ID-nya.
     */
    async fetchProjectById(id: number) {
      this.loading = true;
      this.selectedProject = null;
      this.error = null;
      try {
        const { data } = await api.get<ProjectDetail>(`/calculator-projects/${id}`);
        this.selectedProject = data;
      } catch (err) {
        this.error = `Gagal memuat detail untuk proyek #${id}.`;
        console.error(err);
      } finally {
        this.loading = false;
      }
    },
  },
});