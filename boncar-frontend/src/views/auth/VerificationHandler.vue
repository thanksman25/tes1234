<!-- boncar-frontend/src/views/auth/VerificationHandler.vue -->
<script setup>
defineProps({
  success: Boolean
})
</script>
<template>
  <div>
    <h1 v-if="success">Verifikasi Berhasil!</h1>
    <h1 v-else>Verifikasi Gagal.</h1>
    <p>Anda akan diarahkan ke halaman login...</p>
    <router-link to="/login">Login Sekarang</router-link>
  </div>
</template>