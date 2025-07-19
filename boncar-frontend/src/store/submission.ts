// #### File: src/store/submission.ts

import { defineStore } from 'pinia';
import api from '@/services/api';

// Definisikan tipe data agar lebih terstruktur
export interface Submission {
  id: number;
  user_id: number;
  formula_name: string;
  equation_template: string;
  reference: string;
  supporting_document_path: string;
  status: 'pending' | 'approved' | 'rejected';
  rejection_reason: string | null;
  reviewed_by: number | null;
  reviewed_at: string | null;
  created_at: string;
  updated_at: string;
  user: {
    id: number;
    name: string;
    email: string;
  };
}

interface SubmissionState {
  submissions: Submission[];
  selectedSubmission: Submission | null;
  loading: boolean;
  error: string | null;
}

export const useSubmissionStore = defineStore('submission', {
  state: (): SubmissionState => ({
    submissions: [],
    selectedSubmission: null,
    loading: false,
    error: null,
  }),

  actions: {
    /**
     * Mengambil daftar semua pengajuan dari backend.
     */
    async fetchSubmissions() {
      this.loading = true;
      this.error = null;
      try {
        const { data } = await api.get('/admin/formula-submissions');
        this.submissions = data;
      } catch (err: any) {
        this.error = 'Gagal memuat daftar pengajuan.';
        console.error(err);
      } finally {
        this.loading = false;
      }
    },

    /**
     * Memilih dan mengambil detail satu pengajuan.
     * Jika data sudah ada di state, gunakan itu. Jika tidak, fetch dari API.
     */
    async selectSubmission(id: number) {
      this.selectedSubmission = null;
      const existing = this.submissions.find(s => s.id === id);
      if (existing) {
        this.selectedSubmission = existing;
        return;
      }
      // Jika butuh fetch detail terpisah (saat ini tidak ada endpointnya, jadi kita andalkan dari list)
      // Jika endpoint detail ada, logikanya akan ditambahkan di sini.
    },

    /**
     * Menyetujui sebuah pengajuan.
     */
    async approveSubmission(id: number) {
      this.loading = true;
      this.error = null;
      try {
        await api.post(`/admin/formula-submissions/${id}/approve`);
        // Perbarui status di state secara lokal agar UI reaktif
        const index = this.submissions.findIndex(s => s.id === id);
        if (index !== -1) {
          this.submissions[index].status = 'approved';
        }
        if (this.selectedSubmission?.id === id) {
          this.selectedSubmission.status = 'approved';
        }
      } catch (err: any) {
        this.error = 'Gagal menyetujui pengajuan.';
        console.error(err);
        throw err; // Lempar error agar komponen bisa menangani
      } finally {
        this.loading = false;
      }
    },

    /**
     * Menolak sebuah pengajuan.
     */
    async rejectSubmission(id: number, reason: string) {
      this.loading = true;
      this.error = null;
      try {
        await api.post(`/admin/formula-submissions/${id}/reject`, { rejection_reason: reason });
        // Perbarui status di state
        const index = this.submissions.findIndex(s => s.id === id);
        if (index !== -1) {
          this.submissions[index].status = 'rejected';
        }
        if (this.selectedSubmission?.id === id) {
          this.selectedSubmission.status = 'rejected';
        }
      } catch (err: any) {
        this.error = 'Gagal menolak pengajuan.';
        console.error(err);
        throw err; // Lempar error
      } finally {
        this.loading = false;
      }
    },
  },
});