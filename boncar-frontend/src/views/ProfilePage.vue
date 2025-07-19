// #### File: src/views/ProfilePage.vue

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/store/auth';

const authStore = useAuthStore();
const router = useRouter();

// State untuk UI
const isLoading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

// Reactive state untuk form
const profileData = ref({
  firstName: '',
  lastName: '',
  username: '', // Username tidak dapat diubah, hanya untuk tampilan
  nik: '',
  institution: '',
  phone: '',
  email: '',
});

// Mengisi form dengan data dari authStore saat komponen dimuat
onMounted(() => {
  if (authStore.user) {
    const nameParts = authStore.user.name.split(' ');
    profileData.value.firstName = nameParts[0] || '';
    profileData.value.lastName = nameParts.slice(1).join(' ') || '';
    profileData.value.username = authStore.user.name.replace(/\s+/g, '').toLowerCase(); // Contoh username
    profileData.value.email = authStore.user.email;
    profileData.value.nik = authStore.user.npm_nik || '';
    profileData.value.institution = authStore.user.institution || '';
    profileData.value.phone = authStore.user.phone_number || '';
  }
});

// Fungsi untuk menyimpan perubahan
const handleSave = async () => {
  isLoading.value = true;
  errorMessage.value = '';
  successMessage.value = '';

  try {
    // 1. Siapkan payload sesuai yang diharapkan backend
    const payload = {
      name: `${profileData.value.firstName} ${profileData.value.lastName}`.trim(),
      email: profileData.value.email,
      npm_nik: profileData.value.nik,
      institution: profileData.value.institution,
      phone_number: profileData.value.phone,
    };
    
    // 2. Panggil action dari authStore
    await authStore.updateProfile(payload);
    
    // 3. Tampilkan pesan sukses
    successMessage.value = 'Profil berhasil diperbarui!';

  } catch (error: any) {
    if (error.response && error.response.data && error.response.data.message) {
      errorMessage.value = error.response.data.message;
    } else {
      errorMessage.value = 'Terjadi kesalahan saat menyimpan profil.';
    }
    console.error(error);
  } finally {
    isLoading.value = false;
  }
};

const goBack = () => {
    router.back();
}
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
        <div class="avatar-section">
          <div class="avatar-placeholder material-icons">person</div>
        </div>
        <a href="#" class="change-password-link">Ganti Sandi Akun</a>

        <form @submit.prevent="handleSave" class="profile-form">
          <div v-if="successMessage" class="message success-message">
            {{ successMessage }}
          </div>
          <div v-if="errorMessage" class="message error-message">
            {{ errorMessage }}
          </div>

          <div class="input-grid">
            <div class="input-group">
              <input type="text" v-model="profileData.firstName" placeholder="Nama Depan" required>
            </div>
            <div class="input-group">
              <input type="text" v-model="profileData.lastName" placeholder="Nama Belakang" required>
            </div>
          </div>

          <div class="input-group">
            <input type="text" v-model="profileData.username" placeholder="Username" disabled>
          </div>
          <div class="input-group">
            <input type="text" v-model="profileData.nik" placeholder="NPM/NIP/NIK">
          </div>
          <div class="input-group">
            <input type="text" v-model="profileData.institution" placeholder="Instansi/Institusi">
          </div>
          <div class="input-group">
            <input type="text" v-model="profileData.phone" placeholder="Nomor HP">
          </div>
          <div class="input-group">
            <input type="email" v-model="profileData.email" placeholder="Email" required>
          </div>
          <button type="submit" :disabled="isLoading" class="save-button">
            <span v-if="!isLoading">Simpan Perubahan</span>
            <span v-else class="spinner"></span>
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.profile-page {
  background-image: url('@/assets/images/forest_background.jpg');
  background-size: cover;
  background-position: center;
  min-height: 100vh;
  color: white;
  font-family: sans-serif;
  display: flex;
  justify-content: center;
  padding: 20px;
}

.background-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0, 0, 0, 0.4);
  z-index: 1;
}

.content-wrapper {
  position: relative;
  z-index: 2;
  width: 100%;
  max-width: 420px;
}

.profile-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
  background-color: #2C8A4A;
  border-top-left-radius: 20px;
  border-top-right-radius: 20px;
}

.title {
  font-size: 20px;
  font-weight: bold;
  margin: 0;
}

.back-button, .placeholder {
  width: 40px;
  background: none;
  border: none;
  color: white;
  font-size: 24px;
  cursor: pointer;
}

.profile-container {
  padding: 24px;
  display: flex;
  flex-direction: column;
  align-items: center;
  background-color: rgba(240, 244, 241, 0.95);
  border-bottom-left-radius: 20px;
  border-bottom-right-radius: 20px;
}

.avatar-section {
  margin-bottom: 16px;
}

.avatar-placeholder {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background-color: #e0e0e0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 60px;
  color: #757575;
}

.change-password-link {
  color: #1a592e;
  text-decoration: underline;
  margin-bottom: 24px;
  font-weight: bold;
}

.profile-form {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
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

.input-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  width: 100%;
}

.input-group {
  margin-bottom: 16px;
  width: 100%;
}

.input-group input {
  width: 100%;
  padding: 14px 16px;
  background-color: #2C8A4A;
  border: none;
  border-radius: 30px;
  color: white;
  font-size: 16px;
  text-align: center;
}

.input-group input:disabled {
  background-color: #6c757d;
  cursor: not-allowed;
  opacity: 0.7;
}

.save-button {
  width: auto;
  padding: 12px 40px;
  margin-top: 8px;
  background-color: #2C8A4A;
  color: white;
  border: none;
  border-radius: 30px;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
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