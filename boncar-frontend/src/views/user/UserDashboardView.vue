<script setup>
import { useAuthStore } from '@/stores/auth';
import { computed } from 'vue';

const authStore = useAuthStore();
const user = computed(() => authStore.user);

// Data dummy untuk statistik, nanti akan kita ganti dengan data dari API
const stats = {
  carbon: '1,25',
  trees: 125,
  locations: 125
};
</script>

<template>
  <div class="dashboard-user">
    <div class="welcome-card">
      <p class="greeting">Selamat Datang</p>
      <h1 class="user-name" v-if="user">{{ user.name }}</h1>
    </div>

    <div class="stats-grid">
      <div class="stat-card primary">
        <span class="label">Cadangan Karbon</span>
        <span class="value">{{ stats.carbon }} <span class="unit">Ton/Ha</span></span>
      </div>
      <div class="stat-card">
        <span class="label">Pohon yang Dihitung</span>
        <span class="value">{{ stats.trees }}</span>
      </div>
      <div class="stat-card">
        <span class="label">Lokasi yang Dihitung</span>
        <span class="value">{{ stats.locations }}</span>
      </div>
    </div>
    
    </div>
</template>

<style scoped>
.dashboard-user {
  width: 100%;
}

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
  grid-template-columns: 1fr 1fr; /* 2 kolom */
  gap: 20px;
}

.stat-card {
  background-color: #ffffff;
  color: #333;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  text-align: center;
}

/* Kartu utama dibuat lebih besar dan menonjol */
.stat-card.primary {
  grid-column: 1 / -1; /* Mengambil lebar penuh 2 kolom */
  background-color: #4CAF50; /* Warna hijau yang lebih cerah */
  color: white;
}

.stat-card .label {
  display: block;
  font-size: 14px;
  color: #666;
  margin-bottom: 10px;
}

.stat-card.primary .label {
  color: rgba(255, 255, 255, 0.9);
}

.stat-card .value {
  display: block;
  font-size: 32px;
  font-weight: 700;
  color: #2E7D32;
}

.stat-card.primary .value {
  font-size: 40px;
  color: white;
}

.stat-card .unit {
  font-size: 16px;
  font-weight: normal;
  margin-left: 4px;
}
</style>