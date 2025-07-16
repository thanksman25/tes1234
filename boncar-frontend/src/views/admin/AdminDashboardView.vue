// boncar-frontend/src/views/admin/AdminDashboardView.vue

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '@/stores/auth';
import api from '@/api';

const authStore = useAuthStore();
const user = computed(() => authStore.user);

const stats = ref({
  users: 0,
  formulas: 0,
  pending_submissions: 0,
});
const isLoading = ref(true);

onMounted(async () => {
  try {
    // Panggil API khusus admin
    const response = await api.get('/api/admin/dashboard/admin-stats');
    stats.value = response.data;
  } catch (error) {
    console.error("Gagal mengambil statistik admin:", error);
  } finally {
    isLoading.value = false;
  }
});
</script>

<template>
  <div class="admin-dashboard">
    <div class="welcome-card">
      <h3>Selamat Datang</h3>
      <h2 v-if="user">Administrator {{ user.name }}</h2>
    </div>
    
    <div v-if="isLoading" class="loading-container">
      <div class="spinner"></div>
    </div>
    
    <div v-else class="stats-grid">
      <div class="stat-card">
        <div class="stat-title">Pengguna</div>
        <div class="stat-value">{{ stats.users }}</div>
      </div>
      <div class="stat-card">
        <div class="stat-title">File Belum Diproses</div>
        <div class="stat-value">{{ stats.pending_submissions }}</div>
      </div>
      <div class="stat-card">
        <div class="stat-title">Rumus Alometrik</div>
        <div class="stat-value">{{ stats.formulas }}</div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Style ini sesuai dengan desain AdminDashboard Anda */
.admin-dashboard {
  display: flex;
  flex-direction: column;
  gap: 20px;
  max-width: 900px;
  margin: 0 auto;
  position: relative;
  z-index: 2;
}
.welcome-card {
  background-color: rgba(44, 138, 74, 0.9);
  color: white;
  padding: 20px 24px;
  border-radius: 15px;
  text-align: left;
}
.welcome-card h3 {
  font-weight: normal;
  font-size: 1em;
  margin: 0;
  opacity: 0.8;
}
.welcome-card h2 {
  font-size: 1.5em;
  margin: 4px 0 0 0;
  font-weight: bold;
}
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}
.stat-card {
  background-color: rgba(69, 178, 107, 0.9);
  color: white;
  padding: 20px;
  border-radius: 15px;
  text-align: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.stat-title {
  font-size: 0.9em;
  margin-bottom: 8px;
}
.stat-value {
  font-size: 2em;
  font-weight: bold;
}
.loading-container {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 50px;
}
.spinner {
  border: 5px solid rgba(0, 0, 0, 0.1);
  border-radius: 50%;
  border-top: 5px solid #2E7D32;
  width: 50px;
  height: 50px;
  animation: spin 1s linear infinite;
}
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>