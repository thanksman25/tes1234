// boncar-frontend/src/views/auth/RegisterView.vue

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/api';
import { useUiStore } from '@/stores/ui';

const router = useRouter();
const uiStore = useUiStore();

const name = ref('');
const email = ref('');
const password = ref('');
const password_confirmation = ref('');

const errors = ref({});
const successMessage = ref('');

const handleRegister = async () => {
  uiStore.setLoading(true);
  errors.value = {};
  successMessage.value = '';

  if (password.value !== password_confirmation.value) {
    errors.value.password = ['Konfirmasi kata sandi tidak cocok.'];
    uiStore.setLoading(false);
    return;
  }

  try {
    const response = await api.post('/api/register', {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value,
    });

    successMessage.value = response.data.message;
    setTimeout(() => {
      router.push({ name: 'login' });
    }, 3000);

  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors;
    } else {
      console.error('Registrasi gagal:', error);
      errors.value.general = ['Terjadi kesalahan pada server. Silakan coba lagi.'];
    }
  } finally {
    uiStore.setLoading(false);
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
          <div v-if="successMessage" class="success-message">
            {{ successMessage }}<br>Silakan cek email untuk verifikasi.
          </div>
          <div v-if="errors.general" class="error-message">
            {{ errors.general[0] }}
          </div>
          
          <div class="input-grid">
            <div class="input-group">
              <input type="text" v-model="name" placeholder="Nama Lengkap" required>
              <div v-if="errors.name" class="validation-error">{{ errors.name[0] }}</div>
            </div>
            </div>

          <div class="input-group">
            <input type="email" v-model="email" placeholder="Email" required>
            <div v-if="errors.email" class="validation-error">{{ errors.email[0] }}</div>
          </div>

          <div class="input-group password-group">
            <label>Kata Sandi</label>
            <input type="password" v-model="password" placeholder="Minimal 8 karakter" required>
            <div v-if="errors.password" class="validation-error">{{ errors.password[0] }}</div>
          </div>
          <div class="input-group password-group">
            <label>Konfirmasi Kata Sandi</label>
            <input type="password" v-model="password_confirmation" placeholder="Ulangi kata sandi" required>
          </div>

          <button type="submit" :disabled="uiStore.isLoading" class="register-button">
            <span v-if="!uiStore.isLoading">Daftar</span>
            <div v-else class="spinner"></div>
          </button>
        </form>
      </div>

      <div class="login-link">
        <router-link :to="{ name: 'login' }">
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
  font-family: 'Poppins', sans-serif;
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
  max-width: 420px;
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
  padding: 24px;
  width: 100%;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.register-card form {
  display: flex;
  flex-direction: column;
  align-items: center;
}
.success-message, .error-message {
  width: 100%;
  text-align: center;
  padding: 10px;
  border-radius: 8px;
  margin-bottom: 16px;
  font-size: 14px;
}
.success-message {
  background-color: #d4edda;
  color: #155724;
}
.error-message {
  background-color: #ffdddd;
  color: #d8000c;
}
.validation-error {
  color: #d8000c;
  font-size: 12px;
  text-align: left;
  width: 100%;
  padding-left: 5px;
  margin-top: 4px;
}
.input-grid {
  display: grid;
  grid-template-columns: 1fr; /* Diubah menjadi 1 kolom */
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
  background-color: #ffffff;
  border: 1px solid #ccc;
  border-radius: 10px;
  color: #333;
  font-size: 16px;
}
.password-group label {
  text-align: left;
  display: block;
  font-size: 14px;
  color: #555;
  margin-bottom: 8px;
}
.register-button {
  width: 100%;
  padding: 12px 40px;
  margin-top: 8px;
  background-color: #2C8A4A;
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  min-height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.register-button:disabled {
  background-color: #a5d6a7;
}
.spinner {
  border: 4px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top: 4px solid #fff;
  width: 24px;
  height: 24px;
  animation: spin 1s linear infinite;
  display: inline-block;
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