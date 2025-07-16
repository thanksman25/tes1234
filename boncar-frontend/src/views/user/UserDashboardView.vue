// boncar-frontend/src/views/user/UserDashboardView.vue

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '@/stores/auth';
import api from '@/api';

const authStore = useAuthStore();
const user = computed(() => authStore.user);

// State untuk menyimpan data statistik dari API
const stats = ref({
  carbon_stock: 0,
  trees: 0,
  locations: 0,
});
const isLoading = ref(true);

// Fungsi ini akan berjalan otomatis saat komponen pertama kali ditampilkan
onMounted(async () => {
  try {
    const response = await api.get('/api/dashboard/user-stats');
    stats.value = response.data;
  } catch (error) {
    console.error("Gagal mengambil statistik pengguna:", error);
    // Biarkan nilai default (0) jika gagal
  } finally {
    isLoading.value = false;
  }
});
</script>

<template>
  <div class="user-dashboard">
    <div class="welcome-card">
      <h3>Selamat Datang</h3>
      <h2 v-if="user">{{ user.name }}</h2>
    </div>

    <div v-if="isLoading" class="loading-container">
      <div class="spinner"></div>
    </div>

    <div v-else class="stats-grid">
      <div class="stat-card main-stat">
        <h4>Total Cadangan Karbon</h4>
        <p>{{ stats.carbon_stock }} <span class="unit">Ton/Ha</span></p>
      </div>
      <div class="stat-card">
        <h4>Pohon Yang Dihitung</h4>
        <p>{{ stats.trees }}</p>
      </div>
      <div class="stat-card">
        <h4>Lokasi Yang Dihitung</h4>
        <p>{{ stats.locations }}</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Style dari file UserDashboard.vue Anda, ditambah loading state */
.user-dashboard {
  max-width: 420px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 20px;
  position: relative;
  z-index: 2;
}
.welcome-card {
  background-color: rgba(44, 138, 74, 0.9);
  color: white;
  padding: 20px;
  border-radius: 20px;
  text-align: left;
}
.welcome-card h3 {
  font-weight: normal;
  font-size: 1em;
  margin: 0 0 4px 0;
  opacity: 0.9;
}
.welcome-card h2 {
  font-size: 1.8em;
  margin: 0;
  font-weight: bold;
}
.stats-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
  grid-template-areas:
    "main main"
    "tree location";
}
.stat-card {
  background-color: rgba(69, 178, 107, 0.9);
  color: white;
  padding: 16px;
  border-radius: 20px;
  text-align: center;
}
.main-stat {
  grid-area: main;
  grid-column: 1 / -1;
}
.stat-card h4 {
  margin: 0 0 8px 0;
  font-size: 1em;
  font-weight: normal;
  opacity: 0.9;
}
.stat-card p {
  margin: 0;
  font-size: 1.6em;
  font-weight: bold;
}
.stat-card .unit {
  font-size: 1rem;
  font-weight: normal;
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