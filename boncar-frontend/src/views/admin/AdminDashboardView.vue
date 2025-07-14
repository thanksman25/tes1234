<script setup>
import { useAuthStore } from '@/stores/auth';
import { computed } from 'vue';

const authStore = useAuthStore();
const user = computed(() => authStore.user);

// Data dummy untuk statistik admin
const adminStats = {
  users: 73,
  pendingFiles: 10,
  formulas: 5,
};
</script>

<template>
  <div class="dashboard-admin">
    <div class="welcome-card">
      <p class="greeting">Selamat Datang, Administrator</p>
      <h1 class="user-name" v-if="user">{{ user.name }}</h1>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <span class="label">Total Pengguna</span>
        <span class="value">{{ adminStats.users }}</span>
      </div>
      <div class="stat-card">
        <span class="label">File Belum Diproses</span>
        <span class="value">{{ adminStats.pendingFiles }}</span>
      </div>
      <div class="stat-card">
        <span class="label">Total Rumus Alometrik</span>
        <span class="value">{{ adminStats.formulas }}</span>
      </div>
    </div>
  </div>
</template>

<style scoped>
.welcome-card {
  background-color: #2E7D32;
  color: white;
  padding: 20px 25px;
  border-radius: 12px;
  margin-bottom: 25px;
}
.greeting {
  font-size: 16px;
  opacity: 0.9;
}
.user-name {
  font-size: 24px;
  font-weight: bold;
}
.stats-grid {
  display: grid;
  /* Membuat kolom responsif, minimal 200px, maksimal 1fr */
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}
.stat-card {
  background-color: white;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
.stat-card .label {
  font-size: 14px;
  color: #666;
  margin-bottom: 8px;
  display: block;
}
.stat-card .value {
  font-size: 36px;
  font-weight: 700;
  color: #2E7D32;
  display: block;
}
</style>