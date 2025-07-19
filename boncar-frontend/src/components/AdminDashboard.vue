// #### File: src/components/AdminDashboard.vue

<script setup lang="ts">
import { onMounted } from 'vue';
import { useDashboardStore } from '@/store/dashboard';

const dashboardStore = useDashboardStore();

// Ambil data saat komponen dimuat
onMounted(() => {
  dashboardStore.fetchAdminStats();
});
</script>

<template>
  <div class="admin-dashboard">
    <div class="welcome-card">
      <h3>Selamat Datang</h3>
      <h2>Administrator</h2>
    </div>

    <div v-if="dashboardStore.loading" class="loading-message">
      Memuat data statistik...
    </div>

    <div v-else-if="dashboardStore.error" class="error-message">
      {{ dashboardStore.error }}
    </div>
    
    <div v-else class="stats-grid">
      <div v-for="stat in dashboardStore.adminStats" :key="stat.title" class="stat-card">
        <div class="stat-title">{{ stat.title }}</div>
        <div class="stat-value">{{ stat.value }}</div>
      </div>
    </div>
  </div>
</template>

<style scoped>
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
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
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