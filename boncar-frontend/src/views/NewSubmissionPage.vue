<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api'; // Impor instance API Axios

const router = useRouter();

// State untuk UI
const isLoading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

// State untuk form, termasuk kolom deskripsi baru
const formData = ref({
  formula_name: '',
  equation_template: '',
  reference: '',
  description: '', // Kolom baru ditambahkan di sini
});
const selectedFile = ref<File | null>(null);
const fileName = ref('');

const goBack = () => {
  router.back();
};

// Fungsi untuk menangani perubahan file
const handleFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    const file = target.files[0];
    if (file.type === 'application/pdf' && file.size <= 2048 * 1024) {
      selectedFile.value = file;
      fileName.value = file.name;
      errorMessage.value = ''; // Hapus pesan error jika file valid
    } else {
      target.value = '';
      selectedFile.value = null;
      fileName.value = '';
      if (file.type !== 'application/pdf') {
        errorMessage.value = 'Hanya file dengan format PDF yang diizinkan.';
      } else if (file.size > 2048 * 1024) {
        errorMessage.value = 'Ukuran file tidak boleh lebih dari 2MB.';
      }
    }
  }
};

// Fungsi untuk menangani pengiriman form
const handleSubmit = async () => {
  errorMessage.value = '';
  successMessage.value = '';

  if (!selectedFile.value) {
    errorMessage.value = 'Mohon unggah berkas pendukung.';
    return;
  }

  isLoading.value = true;

  const submissionData = new FormData();
  submissionData.append('formula_name', formData.value.formula_name);
  submissionData.append('equation_template', formData.value.equation_template);
  submissionData.append('reference', formData.value.reference);
  submissionData.append('description', formData.value.description); // Menambahkan deskripsi ke data yang dikirim
  submissionData.append('supporting_document', selectedFile.value);

  try {
    await api.post('/formulas/submit', submissionData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });

    successMessage.value = 'Pengajuan berhasil dikirim! Anda akan dialihkan ke Beranda.';
    
    setTimeout(() => {
      router.push({ name: 'Dashboard' });
    }, 2000);

  } catch (error: any) {
    if (error.response?.data?.errors) {
      const errors = error.response.data.errors;
      const firstErrorKey = Object.keys(errors)[0];
      errorMessage.value = errors[firstErrorKey][0];
    } else if (error.response?.data?.message) {
      errorMessage.value = error.response.data.message;
    } else {
      errorMessage.value = 'Terjadi kesalahan saat mengirim pengajuan.';
    }
    console.error(error);
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <div class="page-wrapper">
    <div class="background-overlay"></div>
    <div class="content-wrapper">
      <header class="page-header">
        <button @click="goBack" class="back-button material-icons">arrow_back</button>
        <span class="header-icon material-icons">post_add</span>
        <h1 class="title">Pengajuan Alometrik Baru</h1>
      </header>
      <div class="form-card">
        <form @submit.prevent="handleSubmit">
          
          <div v-if="successMessage" class="message success-message">
            {{ successMessage }}
          </div>
          <div v-if="errorMessage" class="message error-message">
            {{ errorMessage }}
          </div>

          <div class="input-group">
            <label for="formulaName">Nama Rumus (Contoh: "Chave et al. (2014)")</label>
            <input id="formulaName" type="text" v-model="formData.formula_name" required>
          </div>
          <div class="input-group">
            <label for="formula">Rumus</label>
            <input id="formula" type="text" v-model="formData.equation_template" placeholder="e.g., 0.11 * DBH^2.5" required>
          </div>
          <div class="input-group">
            <label for="reference">Sumber/Referensi</label>
            <input id="reference" type="text" v-model="formData.reference" required>
          </div>
          
          <div class="input-group">
            <label for="description">Deskripsi Tambahan</label>
            <textarea id="description" v-model="formData.description" rows="3" placeholder="Jelaskan secara singkat tentang rumus ini..."></textarea>
          </div>
          
          <div class="input-group">
            <label>Upload Berkas Pendukung (PDF, maks 2MB)</label>
            <label class="file-upload-label">
              <input type="file" @change="handleFileChange" accept=".pdf" required>
              <span class="file-upload-button">Pilih File</span>
              <span class="file-name">{{ fileName || 'Tidak ada file dipilih' }}</span>
            </label>
          </div>

          <button type="submit" :disabled="isLoading" class="submit-button">
            <span v-if="!isLoading">Kirim</span>
            <span v-else class="spinner"></span>
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.page-wrapper {
  background-image: url('@/assets/images/forest_background.jpg');
  background-size: cover; background-position: center; min-height: 100vh;
  padding: 20px;
}
.background-overlay {
  position: fixed; top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0, 0, 0, 0.6); z-index: 1;
}
.content-wrapper {
  position: relative; z-index: 2; width: 100%; max-width: 420px;
  margin: 0 auto;
}
.page-header {
  display: flex; align-items: center; padding: 16px; color: white;
  gap: 12px;
}
.title { font-size: 20px; font-weight: bold; margin: 0; }
.header-icon { font-size: 28px; }
.back-button {
  background: none; border: none; color: white;
  font-size: 24px; cursor: pointer;
}
.form-card {
  padding: 32px; background-color: rgba(255, 255, 255, 0.98);
  border-radius: 20px;
}

.message {
  width: 100%;
  padding: 10px;
  border-radius: 8px;
  margin-bottom: 16px;
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

.input-group { margin-bottom: 20px; }
.input-group label {
  display: block; margin-bottom: 8px; color: #555; font-weight: 500;
  font-size: 14px;
}
.input-group input[type="text"],
.input-group textarea {
  width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px;
  font-size: 16px;
  font-family: inherit; /* Pastikan font sama */
}
.input-group textarea {
  resize: vertical; /* Biarkan pengguna menyesuaikan tinggi textarea */
}

/* Custom File Upload */
.file-upload-label {
  display: flex;
  align-items: center;
  border: 1px solid #ccc;
  border-radius: 8px;
  overflow: hidden;
  cursor: pointer;
}
/* PERBAIKAN DI SINI: Menyembunyikan input file asli */
.file-upload-label input[type="file"] {
  display: none;
}
.file-upload-button {
  background-color: #2C8A4A;
  color: white;
  padding: 12px 16px;
  white-space: nowrap;
}
.file-name {
  padding: 12px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  color: #555;
  font-size: 14px;
}

.submit-button {
  width: auto; padding: 12px 50px;
  margin: 16px auto 0 auto; display: block;
  background-color: #2C8A4A; color: white; border: none;
  border-radius: 30px; font-size: 16px; font-weight: bold; cursor: pointer;
  min-height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.spinner {
  border: 4px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top: 4px solid #fff;
  width: 24px;
  height: 24px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>