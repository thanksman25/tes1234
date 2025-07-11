<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/store/auth';

// State untuk setiap input form
const name = ref('');
const email = ref('');
const password = ref('');
const password_confirmation = ref(''); // Wajib untuk validasi Laravel
const isLoading = ref(false);
const errorMessage = ref('');

const router = useRouter();
const authStore = useAuthStore();

// Fungsi yang dijalankan saat form di-submit
const handleRegister = async () => {
  // Validasi sederhana di frontend
  if (password.value !== password_confirmation.value) {
    errorMessage.value = 'Konfirmasi kata sandi tidak cocok.';
    return;
  }
  if (!name.value || !email.value || !password.value) {
    errorMessage.value = 'Semua field wajib diisi.';
    return;
  }

  isLoading.value = true;
  errorMessage.value = '';

  try {
    // Memanggil action register dari Pinia store
    await authStore.register({
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value,
    });
    
    // Jika berhasil, beri pesan dan arahkan ke halaman login
    alert('Pendaftaran berhasil! Silakan periksa email Anda (atau file laravel.log) untuk link verifikasi.');
    router.push({ name: 'Login' });

  } catch (error: any) {
    // Menampilkan error dari server jika ada (misal: email sudah terdaftar)
    const serverMessage = error.response?.data?.message || 'Terjadi kesalahan pada server.';
    errorMessage.value = `Pendaftaran gagal: ${serverMessage}`;
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <div class="register-page">
    <div class="background-overlay"></div>
    <div class="register-container">
      <h1 class="title">Daftar Akun</h1>

      <div class="register-card">
        <form @submit.prevent="handleRegister">

          <div v-if="errorMessage" class="error-message">
            {{ errorMessage }}
          </div>

          <div class="input-group">
            <input type="text" v-model="name" placeholder="Nama Lengkap" required>
          </div>
          
          <div class="input-group">
            <input type="email" v-model="email" placeholder="Email" required>
          </div>
          <div class="input-group">
            <input type="password" v-model="password" placeholder="Kata Sandi" required>
          </div>

          <div class="input-group">
            <input type="password" v-model="password_confirmation" placeholder="Konfirmasi Kata Sandi" required>
          </div>

          <button type="submit" :disabled="isLoading" class="register-button">
            <span v-if="!isLoading">Daftar</span>
            <span v-else class="spinner"></span>
          </button>
        </form>
      </div>

      <div class="login-link">
        <router-link :to="{ name: 'Login' }">
          Sudah punya akun? Login
        </router-link>
      </div>
    </div>
  </div>
</template>

<style scoped>
.register-page {
  position: relative;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background-image: url('@/assets/images/forest_background.jpg');
  background-size: cover;
  background-position: center;
  font-family: sans-serif;
  padding: 40px 20px;
}
.background-overlay {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0, 0, 0, 0.4);
  z-index: 1;
}
.register-container {
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
  max-width: 380px;
  text-align: center;
}
.title {
  color: white;
  font-size: 28px;
  font-weight: bold;
  margin-bottom: 24px;
}
.register-card {
  background-color: #F0F4F1;
  border-radius: 20px;
  padding: 32px 24px;
  width: 100%;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.register-card form {
  display: flex;
  flex-direction: column;
  align-items: center;
}
.error-message {
  background-color: #ffdddd;
  color: #d8000c;
  padding: 10px;
  border-radius: 8px;
  margin-bottom: 16px;
  font-size: 14px;
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
  transition: background-color 5000s ease-in-out 0s;
}
.input-group input::placeholder {
  color: rgba(255, 255, 255, 0.8);
}
.input-group input:-webkit-autofill,
.input-group input:-webkit-autofill:hover,
.input-group input:-webkit-autofill:focus,
.input-group input:-webkit-autofill:active {
  -webkit-box-shadow: 0 0 0 30px #2C8A4A inset !important;
  -webkit-text-fill-color: white !important;
  caret-color: white;
}
.register-button {
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
.login-link {
  margin-top: 24px;
}
.login-link a {
  color: white;
  font-weight: bold;
  text-decoration: none;
}
</style>