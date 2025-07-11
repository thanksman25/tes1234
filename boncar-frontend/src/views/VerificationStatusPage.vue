<template>
  <div class="status-page-wrapper">
    <div class="status-card">
      <span v-if="isSuccess" class="icon success">✓</span>
      <span v-else class="icon failure">✗</span>
      <h1>{{ message }}</h1>
      <p v-if="isSuccess">Anda sekarang dapat masuk ke akun Anda.</p>
      <p v-else>Silakan coba lagi atau hubungi dukungan jika masalah berlanjut.</p>
      <router-link to="/login" class="login-button">Kembali ke Halaman Login</router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const message = ref('Memproses verifikasi Anda...');
const isSuccess = computed(() => route.path.includes('success'));

onMounted(() => {
  if (isSuccess.value) {
    const successMessage = route.query.message === 'Email_already_verified'
      ? 'Email Anda sudah terverifikasi sebelumnya.'
      : 'Akun Anda telah berhasil diverifikasi!';
    message.value = successMessage;
  } else {
    const failureMessage = route.query.message === 'Invalid_verification_link'
      ? 'Link verifikasi tidak valid atau sudah kedaluwarsa.'
      : 'Terjadi kesalahan saat verifikasi.';
    message.value = `Verifikasi Gagal: ${failureMessage}`;
  }
});
</script>

<style scoped>
.status-page-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f0f4f1;
  font-family: 'Poppins', sans-serif;
  padding: 20px;
}
.status-card {
  background-color: white;
  padding: 40px;
  border-radius: 20px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  text-align: center;
  max-width: 450px;
  width: 100%;
}
.icon {
  display: inline-flex;
  justify-content: center;
  align-items: center;
  width: 80px;
  height: 80px;
  border-radius: 50%;
  font-size: 40px;
  color: white;
  margin-bottom: 20px;
}
.icon.success {
  background-color: #27ae60;
}
.icon.failure {
  background-color: #e74c3c;
}
h1 {
  font-size: 24px;
  color: #333;
  margin-bottom: 10px;
}
p {
  font-size: 16px;
  color: #666;
  margin-bottom: 30px;
}
.login-button {
  display: inline-block;
  padding: 12px 30px;
  background-color: #2C8A4A;
  color: white;
  text-decoration: none;
  border-radius: 30px;
  font-weight: bold;
  transition: background-color 0.2s;
}
.login-button:hover {
  background-color: #25733e;
}
</style>
