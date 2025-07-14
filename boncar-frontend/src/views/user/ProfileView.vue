<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { useUiStore } from '@/stores/ui';
import api from '@/api';

const authStore = useAuthStore();
const uiStore = useUiStore();
const router = useRouter();

// Reactive state untuk form
const profileData = ref({
  name: '',
  email: '',
  npm_nik: '',
  institution: '',
  phone_number: '',
});

// Mengisi form dengan data pengguna saat komponen dimuat
onMounted(() => {
  if (authStore.user) {
    profileData.value.name = authStore.user.name;
    profileData.value.email = authStore.user.email;
    profileData.value.npm_nik = authStore.user.npm_nik || '';
    profileData.value.institution = authStore.user.institution || '';
    profileData.value.phone_number = authStore.user.phone_number || '';
  }
});

const handleSave = async () => {
  uiStore.setLoading(true);
  try {
    // Panggil endpoint update profile di backend
    const response = await api.put('/api/profile', profileData.value);
    
    // Perbarui data user di state Pinia dengan data baru dari server
    authStore.user = response.data.user;
    
    uiStore.showNotification('Profil berhasil diperbarui!', 'success');

  } catch (error) {
    console.error('Gagal memperbarui profil:', error);
    uiStore.showNotification('Gagal memperbarui profil. Coba lagi.', 'error');
  } finally {
    uiStore.setLoading(false);
  }
};

const goBack = () => {
  router.back();
};
</script>

<template>
  <div class="profile-page-background">
    <div class="profile-container">
      <header class="profile-header">
        <button @click="goBack" class="header-button">←</button>
        <h1 class="title">Profil</h1>
        <div class="placeholder"></div>
      </header>

      <div class="profile-content">
        <div class="avatar-placeholder">
          <span>{{ profileData.name.charAt(0) }}</span>
        </div>
        <a href="#" class="change-password-link">Ganti Sandi Akun</a>

        <form @submit.prevent="handleSave" class="profile-form">
          <div class="input-group">
            <input type="text" v-model="profileData.name" placeholder="Nama Lengkap" required>
          </div>
          <div class="input-group">
            <input type="email" v-model="profileData.email" placeholder="Email" required>
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
          <button type="submit" class="save-button" :disabled="uiStore.isLoading">
            {{ uiStore.isLoading ? 'Menyimpan...' : 'Simpan Perubahan' }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.profile-page-background {
  width: 100%;
  padding: 20px;
  background: #f0f4f1; /* Warna latar belakang konten, bukan gambar hutan */
}

.profile-container {
  max-width: 420px;
  margin: 0 auto;
  background-color: #2E7D32;
  border-radius: 20px;
  color: white;
  overflow: hidden;
  box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}

.profile-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
}

.title {
  font-size: 20px;
  font-weight: bold;
}

.header-button, .placeholder {
  width: 40px;
  height: 40px;
  background: none;
  border: none;
  color: white;
  font-size: 24px;
  cursor: pointer;
}

.profile-content {
  background-color: #f0f4f1;
  padding: 24px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.avatar-placeholder {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background-color: #e0e0e0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 50px;
  font-weight: bold;
  color: #2E7D32;
  margin-bottom: 16px;
}

.change-password-link {
  color: #1a592e;
  text-decoration: underline;
  margin-bottom: 24px;
  font-weight: bold;
}

.profile-form {
  width: 100%;
}

.input-group {
  margin-bottom: 16px;
  width: 100%;
}

.input-group input {
  width: 100%;
  padding: 14px 16px;
  background-color: white;
  border: 1px solid #ccc;
  border-radius: 30px;
  color: #333;
  font-size: 16px;
  text-align: center;
}

.save-button {
  width: 100%;
  padding: 14px 40px;
  margin-top: 8px;
  background-color: #2C8A4A;
  color: white;
  border: none;
  border-radius: 30px;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
}
.save-button:disabled {
  background-color: #a5d6a7;
}
</style>