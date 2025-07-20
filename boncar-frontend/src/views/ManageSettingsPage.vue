<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';

interface Setting {
  key: string;
  value: string;
  label: string;
  group: string;
}

const router = useRouter();
const settings = ref<Setting[]>([]);
const loading = ref(true);
const successMessage = ref('');
const errorMessage = ref('');

const goBack = () => router.back();

onMounted(async () => {
  try {
    const { data } = await api.get('/admin/settings');
    settings.value = data;
  } catch (error) {
    errorMessage.value = 'Gagal memuat pengaturan.';
  } finally {
    loading.value = false;
  }
});

const handleSave = async () => {
  successMessage.value = '';
  errorMessage.value = '';
  loading.value = true;
  try {
    await api.put('/admin/settings', { settings: settings.value });
    successMessage.value = 'Pengaturan berhasil disimpan.';
  } catch (error) {
    errorMessage.value = 'Gagal menyimpan pengaturan.';
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="page-wrapper">
    <div class="background-overlay"></div>
    <div class="content-wrapper">
      <header class="page-header">
        <button @click="goBack" class="back-button material-icons">arrow_back</button>
        <span class="header-icon material-icons">settings</span>
        <h1 class="title">Pengaturan Kalkulator</h1>
      </header>
      <div class="main-card">
        <div v-if="loading" class="loading-message">Memuat...</div>
        <form v-else @submit.prevent="handleSave">
          <div v-if="successMessage" class="message success-message">{{ successMessage }}</div>
          <div v-if="errorMessage" class="message error-message">{{ errorMessage }}</div>
          
          <div v-for="setting in settings" :key="setting.key" class="input-group">
            <label>{{ setting.label }}</label>
            <input type="number" step="0.01" v-model="setting.value" required>
          </div>

          <button type="submit" class="submit-button" :disabled="loading">
            {{ loading ? 'Menyimpan...' : 'Simpan Perubahan' }}
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
  position: relative; z-index: 2; width: 100%; max-width: 600px;
  margin: 0 auto;
}
.page-header {
  display: flex; align-items: center; padding-bottom: 16px; color: white; gap: 12px;
}
.title { font-size: 20px; font-weight: bold; margin: 0; }
.header-icon { font-size: 28px; }
.back-button {
  background: none; border: none; color: white;
  font-size: 24px; cursor: pointer;
}
.main-card {
  padding: 32px; background-color: rgba(255, 255, 255, 0.98);
  border-radius: 20px;
}
.input-group {
  margin-bottom: 24px;
}
.input-group label {
  display: block;
  font-weight: 500;
  margin-bottom: 8px;
  color: #333;
}
.input-group input {
  width: 100%;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 16px;
}
.submit-button {
  background-color: #2C8A4A;
  color: white;
  padding: 12px 30px;
  border-radius: 30px;
  border: none;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  display: block;
  margin-left: auto;
}
.submit-button:disabled {
  background-color: #95a5a6;
}
.loading-message {
  text-align: center;
  padding: 40px;
}
.message {
  padding: 12px;
  border-radius: 8px;
  margin-bottom: 20px;
  text-align: center;
}
.success-message {
  background-color: #d4edda;
  color: #155724;
}
.error-message {
  background-color: #f8d7da;
  color: #721c24;
}
</style>