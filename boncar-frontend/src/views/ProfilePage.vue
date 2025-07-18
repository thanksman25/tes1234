<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/store/auth';
import api from '@/services/api';

const authStore = useAuthStore();
const router = useRouter();

// Inisialisasi dengan string kosong untuk memastikan input selalu terikat ke nilai yang valid
const profileData = ref({
  name: '',
  email: '',
  npm_nik: '',
  institution: '',
  phone_number: '',
});

const isLoading = ref(true);
const isSaving = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

// Fungsi untuk mengambil data profil
const fetchProfile = async () => {
  isLoading.value = true;
  errorMessage.value = ''; // Hapus pesan error lama
  try {
    const response = await api.get('/api/profile');
    const user = response.data;
    
    // Isi data form dengan nilai dari API, atau string kosong jika null
    profileData.value = {
      name: user.name || '',
      email: user.email || '',
      npm_nik: user.npm_nik || '',
      institution: user.institution || '',
      phone_number: user.phone_number || '',
    };
  } catch (error: any) {
    console.error("Gagal mengambil data profil:", error);
    errorMessage.value = "Gagal memuat data profil. Silakan periksa koneksi Anda.";
  } finally {
    isLoading.value = false;
  }
};

onMounted(fetchProfile);

// Fungsi untuk menyimpan perubahan
const handleSave = async () => {
  isSaving.value = true;
  successMessage.value = '';
  errorMessage.value = '';

  try {
    const response = await api.put('/api/profile', profileData.value);
    if (authStore.user) {
      authStore.user.name = response.data.user.name;
    }
    successMessage.value = 'Perubahan berhasil disimpan!';
    setTimeout(() => successMessage.value = '', 3000);
  } catch (error) {
    console.error('Gagal menyimpan profil:', error);
    errorMessage.value = 'Terjadi kesalahan saat menyimpan.';
  } finally {
    isSaving.value = false;
  }
};

const goBack = () => {
    router.back();
};
</script>

<template>
  <div class="profile-page">
    <div class="background-overlay"></div>
    
    <div class="content-wrapper">
      <header class="profile-header">
        <button @click="goBack" class="back-button material-icons">arrow_back</button>
        <h1 class="title">Profil</h1>
        <div class="placeholder"></div>
      </header>

      <div class="profile-container">
        <div v-if="isLoading" class="status-container">
          <p>Memuat profil...</p>
        </div>

        <div class="avatar-section">
          <div class="avatar-placeholder material-icons">person</div>
        </div>
        <a href="#" class="change-password-link">Ganti Sandi Akun</a>

        <form @submit.prevent="handleSave" class="profile-form">
          <div v-if="successMessage" class="success-message">{{ successMessage }}</div>
          <div v-if="errorMessage" class="error-message">{{ errorMessage }}</div>
          
          <fieldset :disabled="isLoading" class="input-fieldset">
            <div class="input-group">
              <input type="text" v-model="profileData.name" placeholder="Nama Lengkap">
            </div>
            <div class="input-group">
              <input type="email" v-model="profileData.email" placeholder="Email">
            </div>
            <div class="input-group">
              <input type="text" v-model="profileData.npm_nik" placeholder="NPM/NIP/NIK">
            </div>
            <div class="input-group">
              <input type="text" v-model="profileData.institution" placeholder="Instansi/Institusi">
            </div>
            <div class="input-group">
              <input type="text" v-model="profileData.phone_number" placeholder="Nomor HP">
            </div>
          </fieldset>
          
          <button type="submit" class="save-button" :disabled="isSaving || isLoading">
            <span v-if="isSaving">Menyimpan...</span>
            <span v-else>Simpan Perubahan</span>
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Style tidak berubah dari versi lama Anda yang berfungsi */
.profile-page { background-image: url('@/assets/images/forest_background.jpg'); background-size: cover; background-position: center; min-height: 100vh; color: white; font-family: sans-serif; display: flex; justify-content: center; padding: 20px; }
.background-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.4); z-index: 1; }
.content-wrapper { position: relative; z-index: 2; width: 100%; max-width: 420px; }
.profile-header { display: flex; justify-content: space-between; align-items: center; padding: 16px; background-color: #2C8A4A; border-top-left-radius: 20px; border-top-right-radius: 20px; }
.title { font-size: 20px; font-weight: bold; margin: 0; }
.back-button, .placeholder { width: 40px; background: none; border: none; color: white; font-size: 24px; cursor: pointer; }
.profile-container { padding: 24px; display: flex; flex-direction: column; align-items: center; background-color: rgba(240, 244, 241, 0.95); border-bottom-left-radius: 20px; border-bottom-right-radius: 20px; }
.avatar-section { margin-bottom: 16px; }
.avatar-placeholder { width: 100px; height: 100px; border-radius: 50%; background-color: #e0e0e0; display: flex; align-items: center; justify-content: center; font-size: 60px; color: #757575; }
.change-password-link { color: #1a592e; text-decoration: underline; margin-bottom: 24px; font-weight: bold; }
.profile-form { width: 100%; display: flex; flex-direction: column; align-items: center; }
.input-fieldset { border: none; padding: 0; margin: 0; width: 100%; } /* Fieldset untuk menonaktifkan semua input */
.input-group { margin-bottom: 16px; width: 100%; }
.input-group input { width: 100%; padding: 14px 16px; background-color: #2C8A4A; border: none; border-radius: 30px; color: white; font-size: 16px; text-align: center; }
.save-button { width: auto; padding: 14px 40px; margin-top: 8px; background-color: #2C8A4A; color: white; border: none; border-radius: 30px; font-size: 16px; font-weight: bold; cursor: pointer; }
.save-button:disabled { background-color: #6c757d; cursor: not-allowed; }
.status-container { color: #333; text-align: center; padding: 20px; background-color: rgba(255, 255, 255, 0.8); border-radius: 8px; margin-bottom: 20px; }
.success-message { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; border-radius: 8px; margin-bottom: 16px; width: 100%; text-align: center; }
.error-message { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; border-radius: 8px; margin-bottom: 16px; width: 100%; text-align: center; }
</style>