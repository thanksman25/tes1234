<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '@/store/auth';
import api from '@/services/api';
import AppDrawer from '@/components/AppDrawer.vue';

const authStore = useAuthStore();
const drawerOpen = ref(false);

// Logika ini sekarang khusus untuk pengguna biasa
const userName = computed(() => authStore.user?.name || 'Pengguna');
const stats = ref({
  totalCarbon: '0 Ton/Ha',
  projectCount: 0,
  treeCount: 0,
});
const isLoading = ref(true);

const fetchUserDashboardStats = async () => {
  isLoading.value = true;
  try {
    const response = await api.get('/api/projects');
    const projects = response.data;
    const totalProjects = projects.length;
    let totalCarbon = 0;

    if (totalProjects > 0) {
      const sumCarbon = projects.reduce((acc: number, project: any) => acc + parseFloat(project.total_carbon_stock || 0), 0);
      totalCarbon = sumCarbon > 0 ? sumCarbon / totalProjects : 0;
    }
    
    const totalTrees = projects.reduce((acc: number, project: any) => acc + (project.trees_count || 0), 0);
    
    stats.value = {
      totalCarbon: `${totalCarbon.toFixed(2)} Ton/Ha`,
      projectCount: totalProjects,
      treeCount: totalTrees,
    };
  } catch (error) {
    console.error("Gagal mengambil data statistik:", error);
  } finally {
    isLoading.value = false;
  }
};

onMounted(fetchUserDashboardStats);

const toggleDrawer = () => {
  drawerOpen.value = !drawerOpen.value;
};
</script>

<template>
  <div class="dashboard-layout">
    <AppDrawer :is-open="drawerOpen" user-role="user" @close="drawerOpen = false" />
    
    <div class="main-content">
      <header class="main-header">
        <button @click="toggleDrawer" class="menu-button">☰</button>
        <h1 class="header-title">Beranda Pengguna</h1>
      </header>
      
      <main class="content-area">
        <div class="user-dashboard">
          <div class="welcome-card">
            <h3>Selamat Datang</h3>
            <h2>{{ userName }}</h2>
          </div>

          <div v-if="isLoading" class="loading-state">
            <p>Memuat data statistik...</p>
          </div>

          <div v-else class="stats-grid">
            <div class="stat-card main-stat">
              <h4>Cadangan Karbon (Rata-rata)</h4>
              <p>{{ stats.totalCarbon }}</p>
            </div>
            <div class="stat-card">
              <h4>Pohon Yang Dihitung</h4>
              <p>{{ stats.treeCount }}</p>
            </div>
            <div class="stat-card">
              <h4>Proyek Yang Dihitung</h4>
              <p>{{ stats.projectCount }}</p>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<style scoped>
/* Semua style dari file sebelumnya tetap sama, tidak perlu diubah */
.dashboard-layout { min-height: 100vh; background-color: #f4f7f6; }
.main-content { transition: margin-left 0.3s; }
.main-header { display: flex; align-items: center; padding: 16px; background-color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); color: #333; }
.menu-button { background: none; border: none; font-size: 24px; cursor: pointer; margin-right: 16px; }
.header-title { font-size: 20px; font-weight: bold; }
.content-area { padding: 24px; background-image: url('@/assets/images/forest_background.jpg'); background-size: cover; background-attachment: fixed; min-height: calc(100vh - 73px); position: relative; }
.content-area::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.1); }
.user-dashboard { max-width: 420px; margin: 0 auto; padding: 0 10px; display: flex; flex-direction: column; gap: 20px; position: relative; z-index: 2; }
.welcome-card { background-color: rgba(44, 138, 74, 0.9); color: white; padding: 20px; border-radius: 20px; text-align: left; }
.welcome-card h3 { font-weight: normal; font-size: 1em; margin: 0 0 4px 0; opacity: 0.9; }
.welcome-card h2 { font-size: 1.8em; margin: 0; font-weight: bold; }
.stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; grid-template-areas: "main main" "tree location"; }
.stat-card { background-color: rgba(69, 178, 107, 0.9); color: white; padding: 16px; border-radius: 20px; text-align: center; }
.main-stat { grid-area: main; grid-column: 1 / -1; }
.stat-card h4 { margin: 0 0 8px 0; font-size: 1em; font-weight: normal; opacity: 0.9; }
.stat-card p { margin: 0; font-size: 1.6em; font-weight: bold; }
.loading-state { color: white; text-align: center; padding: 40px; font-size: 1.2em; }
</style>