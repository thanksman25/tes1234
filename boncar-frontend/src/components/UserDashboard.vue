// #### File: src/components/UserDashboard.vue

<script setup lang="ts">
import { onMounted } from 'vue';
import { useAuthStore } from '@/store/auth';
import { useDashboardStore } from '@/store/dashboard';

const authStore = useAuthStore();
const dashboardStore = useDashboardStore();

// Ambil data saat komponen dimuat
onMounted(() => {
  dashboardStore.fetchUserStats();
});
</script>

<template>
  <div class="user-dashboard">
    <div class="welcome-card">
      <h3>Selamat Datang</h3>
      <h2>{{ authStore.user?.name || 'Pengguna' }}</h2>
    </div>

    <div v-if="dashboardStore.loading" class="loading-message">
      Memuat data statistik...
    </div>

    <div v-else-if="dashboardStore.error" class="error-message">
      {{ dashboardStore.error }}
    </div>

    <div v-else class="stats-grid">
      <div class="stat-card main-stat">
        <h4>{{ dashboardStore.userStats[0]?.title || 'Cadangan Karbon' }}</h4>
        <p>{{ dashboardStore.userStats[0]?.value || '0 Ton/Ha' }}</p>
      </div>
      <div v-if="dashboardStore.userStats.length > 1" class="stat-card">
        <h4>{{ dashboardStore.userStats[1]?.title || 'Pohon' }}</h4>
        <p>{{ dashboardStore.userStats[1]?.value || 0 }}</p>
      </div>
      <div v-if="dashboardStore.userStats.length > 2" class="stat-card">
        <h4>{{ dashboardStore.userStats[2]?.title || 'Lokasi' }}</h4>
        <p>{{ dashboardStore.userStats[2]?.value || 0 }}</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.user-dashboard {
  max-width: 420px;
  margin: 0 auto;
  padding: 0 10px;
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
  grid-column: 1 / -1; /* Memastikan ini mengambil lebar penuh */
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

/* Style untuk loading dan error */
.loading-message, .error-message {
  text-align: center;
  padding: 40px;
  background-color: rgba(255, 255, 255, 0.8);
  border-radius: 15px;
  color: #333;
  font-weight: 500;
}
.error-message {
  background-color: rgba(255, 224, 224, 0.9);
  color: #d32f2f;
}
</style>