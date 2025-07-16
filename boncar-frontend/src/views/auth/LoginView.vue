// boncar-frontend/src/views/auth/LoginView.vue

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useUiStore } from '@/stores/ui';

const authStore = useAuthStore();
const uiStore = useUiStore();

const email = ref('');
const password = ref('');
const errorMessage = ref(null);

// Variabel baru untuk fitur tampilkan sandi
const passwordFieldType = ref('password');
const isPasswordVisible = ref(false);

const handleLogin = async () => {
  // Logika login tetap sama
  uiStore.setLoading(true);
  errorMessage.value = null;
  try {
    await authStore.login({ email: email.value, password: password.value });
  } catch (error) {
    if (error.response && error.response.data.message) {
      errorMessage.value = error.response.data.message;
    } else {
      errorMessage.value = 'Terjadi kesalahan. Silakan coba lagi.';
    }
  } finally {
    uiStore.setLoading(false);
  }
};

// Fungsi baru untuk toggle visibilitas sandi
const togglePasswordVisibility = () => {
  isPasswordVisible.value = !isPasswordVisible.value;
  passwordFieldType.value = isPasswordVisible.value ? 'text' : 'password';
};
</script>

<template>
  <div class="login-page">
    <div class="background-overlay"></div>
    <div class="login-container">
      <img src="@/assets/images/logo_boncar_white.png" alt="Boncar Logo" class="logo">
      
      <h1 class="title">Masuk Ke Akun Anda</h1>
      <router-link :to="{ name: 'register' }" class="register-link-alt">
        Atau daftar akun baru
      </router-link>

      <div class="login-card">
        <form @submit.prevent="handleLogin">
          <h2 class="card-title">Login</h2>
          
          <div v-if="errorMessage" class="error-message">
            {{ errorMessage }}
          </div>

          <div class="input-group">
            <label for="email">Username/Email</label>
            <input 
              type="email" 
              id="email" 
              v-model="email"
              placeholder="Masukkan Email" 
              required
            >
          </div>
          
          <div class="input-group">
            <label for="password">Kata Sandi</label>
            <div class="password-wrapper">
              <input 
                :type="passwordFieldType" 
                id="password" 
                v-model="password"
                placeholder="Masukkan Kata Sandi" 
                required
              >
              <button type="button" @click="togglePasswordVisibility" class="password-toggle">
                <span class="material-icons">
                  {{ isPasswordVisible ? 'visibility_off' : 'visibility' }}
                </span>
              </button>
            </div>
          </div>
          <div class="options">
            <label class="remember-me">
              <input type="checkbox">
              Ingat Saya
            </label>
            <a href="#" class="forgot-password">Lupa sandi?</a>
          </div>

          <button type="submit" :disabled="uiStore.isLoading" class="login-button">
            <span v-if="!uiStore.isLoading">Masuk</span>
            <div v-else class="spinner"></div>
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Style sebelumnya sebagian besar tetap sama, dengan tambahan untuk ikon mata */
.login-page {
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
.login-container {
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
  max-width: 380px;
  text-align: center;
}
.logo {
  width: 220px;
  height: auto;
  margin-bottom: 16px;
}
.title {
  color: white;
  font-size: 24px;
  font-weight: bold;
  margin: 0;
}
.register-link-alt {
  color: white;
  margin-top: 4px;
  margin-bottom: 24px;
  text-decoration: none;
  opacity: 0.9;
}
.login-card {
  background-color: #F0F4F1;
  border-radius: 20px;
  padding: 24px;
  width: 100%;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.card-title {
  font-size: 20px;
  font-weight: bold;
  color: #333;
  margin-bottom: 20px;
  text-align: left;
}
.error-message {
  background-color: #ffdddd;
  color: #d8000c;
  padding: 10px;
  border-radius: 8px;
  margin-bottom: 16px;
  font-size: 14px;
}
.input-group {
  margin-bottom: 16px;
  text-align: left;
}
.input-group label {
  display: block;
  margin-bottom: 8px;
  font-size: 14px;
  color: #555;
  font-weight: bold;
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
/* CSS BARU UNTUK WRAPPER PASSWORD */
.password-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}
.password-wrapper input {
  padding-right: 45px; /* Beri ruang untuk ikon */
}
.password-toggle {
  position: absolute;
  right: 5px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  color: #666;
  padding: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}
/* AKHIR CSS BARU */

.options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 12px;
  margin-bottom: 24px;
}
.remember-me {
  display: flex;
  align-items: center;
  gap: 6px;
  color: #555;
}
.forgot-password {
  color: #2C8A4A;
  text-decoration: none;
  font-weight: bold;
}
.login-button {
  width: 100%;
  padding: 12px 40px;
  background-color: #2C8A4A;
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 48px;
}
.login-button:disabled {
  background-color: #a5d6a7;
  cursor: not-allowed;
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
</style>