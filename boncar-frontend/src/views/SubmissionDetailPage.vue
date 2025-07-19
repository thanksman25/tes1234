<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useSubmissionStore } from '@/store/submission';

const route = useRoute();
const router = useRouter(); // Tambahkan ini
const submissionStore = useSubmissionStore();

const submissionId = computed(() => Number(route.params.id));
const submission = computed(() => submissionStore.selectedSubmission);
const apiUrl = import.meta.env.VITE_API_URL.replace('/api', '');

const isLoadingAction = ref(false);
const actionMessage = ref('');
const actionError = ref('');

// Fungsi untuk kembali ke halaman sebelumnya
const goBack = () => {
  router.back();
};

onMounted(() => {
  // Ambil data detail saat komponen dimuat, jika belum ada di state
  submissionStore.selectSubmission(submissionId.value);
});

const handleApprove = async () => {
  if (!submission.value || isLoadingAction.value) return;
  isLoadingAction.value = true;
  actionMessage.value = '';
  actionError.value = '';
  try {
    await submissionStore.approveSubmission(submission.value.id);
    actionMessage.value = 'Pengajuan berhasil disetujui.';
  } catch (error) {
    actionError.value = 'Gagal menyetujui pengajuan.';
  } finally {
    isLoadingAction.value = false;
  }
};

const handleReject = async () => {
  if (!submission.value || isLoadingAction.value) return;
  
  const reason = prompt('Masukkan alasan penolakan:');
  if (!reason) {
    alert('Alasan penolakan wajib diisi.');
    return;
  }

  isLoadingAction.value = true;
  actionMessage.value = '';
  actionError.value = '';
  try {
    await submissionStore.rejectSubmission(submission.value.id, reason);
    actionMessage.value = 'Pengajuan berhasil ditolak.';
  } catch (error) {
    actionError.value = 'Gagal menolak pengajuan.';
  } finally {
    isLoadingAction.value = false;
  }
};
</script>

<template>
  <div class="page-container">
    <header class="page-header">
      <button @click="goBack" class="back-button material-icons">arrow_back</button>
      <h1 class="title">Detail Pengajuan #{{ submissionId }}</h1>
    </header>

    <div v-if="submission" class="detail-layout">
      <div class="details-card">
        <div v-if="actionMessage" class="message success-message">
          {{ actionMessage }}
        </div>
        <div v-if="actionError" class="message error-message">
          {{ actionError }}
        </div>
        
        <div class="detail-item">
          <label>Nama Pengaju</label>
          <div class="value-box">{{ submission.user.name }} ({{ submission.user.email }})</div>
        </div>
        <div class="detail-item">
          <label>Nama Rumus</label>
          <div class="value-box">{{ submission.formula_name }}</div>
        </div>
        <div class="detail-item">
          <label>Template Rumus</label>
          <div class="value-box code">{{ submission.equation_template }}</div>
        </div>
        <div class="detail-item">
          <label>Referensi</label>
          <div class="value-box">{{ submission.reference }}</div>
        </div>
        <div class="detail-item">
          <label>Berkas Pendukung</label>
          <div class="value-box">
            <a :href="`${apiUrl}/storage/${submission.supporting_document_path}`" target="_blank" rel="noopener noreferrer" class="file-link">
              Lihat Dokumen (PDF)
            </a>
          </div>
        </div>
        <div class="detail-item">
          <label>Tanggal Pengajuan</label>
          <div class="value-box">{{ new Date(submission.created_at).toLocaleString('id-ID') }}</div>
        </div>
        
        <div v-if="submission.status === 'pending'" class="action-buttons">
          <button @click="handleApprove" :disabled="isLoadingAction" class="btn btn-approve">
            {{ isLoadingAction ? 'Memproses...' : 'Terima' }}
          </button>
          <button @click="handleReject" :disabled="isLoadingAction" class="btn btn-reject">
            {{ isLoadingAction ? 'Memproses...' : 'Tolak' }}
          </button>
        </div>
        <div v-else class="reviewed-status">
          Status: <strong>{{ submission.status.toUpperCase() }}</strong>
        </div>
      </div>
    </div>
    <div v-else class="not-found">
      <h1>Pengajuan tidak ditemukan atau sedang dimuat...</h1>
    </div>
  </div>
</template>

<style scoped>
.page-container {
  max-width: 800px;
  margin: 0 auto;
}
/* STYLE BARU UNTUK HEADER */
.page-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 20px;
}
.title, .page-title {
  color: white;
  text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
  font-size: 20px;
  font-weight: bold;
}
.back-button {
  background: none;
  border: none;
  color: white;
  font-size: 28px;
  cursor: pointer;
}

.details-card {
  background-color: rgba(255, 255, 255, 0.95);
  color: #333;
  padding: 30px;
  border-radius: 20px;
}
.detail-item {
  margin-bottom: 16px;
}
.detail-item label {
  color: #555;
  display: block;
  margin-bottom: 5px;
  font-weight: 500;
}
.value-box {
  background-color: #f0f4f1;
  color: #333;
  padding: 12px 16px;
  border-radius: 10px;
  border: 1px solid #e0e0e0;
}
.value-box.code {
  font-family: 'Courier New', Courier, monospace;
  font-weight: bold;
}
.file-link {
  color: #2C8A4A;
  font-weight: bold;
  text-decoration: none;
}
.file-link:hover {
  text-decoration: underline;
}
.action-buttons {
  margin-top: 30px;
  text-align: right;
}
.btn {
  padding: 12px 30px;
  border-radius: 20px;
  border: none;
  color: white;
  cursor: pointer;
  font-weight: bold;
  min-width: 120px;
}
.btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}
.btn-approve { background-color: #27ae60; margin-right: 10px; }
.btn-reject { background-color: #e74c3c; }
.not-found {
  color: white; text-align: center; padding-top: 50px;
}
.reviewed-status {
  margin-top: 30px;
  padding: 15px;
  text-align: center;
  font-size: 1.1em;
  border-radius: 10px;
  background-color: #e0e0e0;
}
.message {
  width: 100%;
  padding: 12px;
  border-radius: 8px;
  margin-bottom: 20px;
  font-size: 14px;
  text-align: center;
}
.success-message {
  background-color: #d4edda;
  color: #155724;
}
.error-message {
  background-color: #ffdddd;
  color: #d8000c;
}
</style>