// #### File: src/store/formulaStore.ts

import { defineStore } from 'pinia';
import api from '@/services/api';

// Perbarui interface agar sesuai dengan struktur DB baru
export interface AllometricEquation {
  id: number;
  name: string;
  reference: string;
  equation_template: string | null; // Bisa jadi null sekarang
  formula_agb: string;
  formula_bgb: string;
  formula_carbon: string;
  required_parameters: ('circumference' | 'height' | 'wood_density')[];
}

interface FormulaState {
  formulas: AllometricEquation[];
  selectedFormula: AllometricEquation | null;
  loading: boolean;
  error: string | null;
}

export const useFormulaStore = defineStore('formula', {
  state: (): FormulaState => ({
    formulas: [],
    selectedFormula: null,
    loading: false,
    error: null,
  }),

  actions: {
    async fetchFormulas() {
      this.loading = true;
      this.error = null;
      try {
        const { data } = await api.get<AllometricEquation[]>('/formulas');
        this.formulas = data;
      } catch (err: any) {
        this.error = 'Gagal memuat daftar rumus.';
        console.error(err);
      } finally {
        this.loading = false;
      }
    },

    async fetchFormulaById(id: number) {
        this.loading = true;
        this.selectedFormula = null;
        try {
            const { data } = await api.get<AllometricEquation>(`/admin/formulas/${id}`);
            this.selectedFormula = data;
        } catch (err) {
            this.error = 'Gagal mengambil detail rumus.';
            console.error(err);
        } finally {
            this.loading = false;
        }
    },

    async createFormula(payload: Omit<AllometricEquation, 'id' | 'equation_template'>) {
        try {
            const { data } = await api.post('/admin/formulas', payload);
            this.formulas.push(data);
        } catch (err: any) {
            console.error(err);
            if (err.response?.data?.errors) {
                const errors = err.response.data.errors;
                throw new Error(Object.values(errors).flat().join('\n'));
            }
            throw new Error('Gagal membuat rumus baru.');
        }
    },

    async updateFormula(id: number, payload: Omit<AllometricEquation, 'id' | 'equation_template'>) {
      try {
        const { data } = await api.put(`/admin/formulas/${id}`, payload);
        const index = this.formulas.findIndex(f => f.id === id);
        if (index !== -1) {
          this.formulas[index] = data;
        }
        if (this.selectedFormula?.id === id) {
            this.selectedFormula = data;
        }
      } catch (err: any) {
        console.error(err);
        // --- LOGIKA ERROR HANDLING DIPERBAIKI DI SINI ---
        if (err.response?.data?.errors) {
            const errors = err.response.data.errors;
            // Ambil pesan error validasi pertama dari Laravel
            throw new Error(Object.values(errors).flat().join('\n'));
        }
        throw new Error('Gagal memperbarui rumus.');
        // ------------------------------------------------
      }
    },

    async deleteFormula(id: number) {
      try {
        await api.delete(`/admin/formulas/${id}`);
        this.formulas = this.formulas.filter(f => f.id !== id);
      } catch (err: any) {
        console.error(err);
        if (err.response && err.response.status === 409) {
            throw new Error(err.response.data.message);
        }
        throw new Error('Gagal menghapus rumus.');
      }
    },
  },
});