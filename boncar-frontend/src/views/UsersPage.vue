<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import AppDrawer from '@/components/AppDrawer.vue'; // <-- Import AppDrawer
import AdminDashboard from '@/components/AdminDashboard.vue'; // <-- Import komponen dashboard admin

type Role = 'Pengguna' | 'Admin';

interface User {
  id: number;
  fullName: string;
  username: string;
  email: string;
  role: Role;
}

const drawerOpen = ref(false);

const toggleDrawer = () => {
  drawerOpen.value = !drawerOpen.value;
};

// Data dummy untuk pengguna
const users = ref<User[]>([
  { id: 1, fullName: 'Liam Chen', username: 'liamc22', email: 'liam.chen22@gmail.com', role: 'Pengguna' },
  { id: 2, fullName: 'Zoe Kim', username: 'zoekim_', email: 'zoekim.08@gmail.com', role: 'Pengguna' },
  { id: 3, fullName: 'Noah Tan', username: 'noah_tn', email: 'noah.tan11@gmail.com', role: 'Pengguna' },
]);
</script>

<template>
  <div class="dashboard-layout">
    <AppDrawer :is-open="drawerOpen" user-role="admin" @close="drawerOpen = false" />
    
    <div class="main-content">
      <header class="main-header">
        <button @click="toggleDrawer" class="menu-button">☰</button>
        <h1 class="header-title">Beranda Admin</h1>
      </header>

      <main class="content-area">
        <AdminDashboard />

        <div class="main-card">
          <div class="search-header">
            <h3>Data Pengguna</h3>
            <div class="search-bar">
              <span class="material-icons">search</span>
              <input type="text" placeholder="Cari...">
            </div>
          </div>
          <div class="users-list">
            <div v-for="(user, index) in users" :key="user.id" class="user-card">
              <div class="info-row"><strong>Nomor:</strong><span>{{ index + 1 }}</span></div>
              <div class="info-row"><strong>Nama Lengkap:</strong><span>{{ user.fullName }}</span></div>
              <div class="info-row"><strong>Username:</strong><span>{{ user.username }}</span></div>
              <div class="info-row"><strong>Email:</strong><span>{{ user.email }}</span></div>
              <div class="info-row"><strong>Hak Akses:</strong><span>{{ user.role }}</span></div>
              <div class="card-footer">
                <button class="btn btn-edit">Edit</button>
                <button class="btn btn-delete">Hapus</button>
              </div>
            </div>
          </div>
          <div class="pagination">
            <a href="#">&lt;&lt; Previous</a>
            <span>1 2 3 ... 7 8 9</span>
            <a href="#">Next &gt;&gt;</a>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<style scoped>
/* Menambahkan gaya dari DashboardPage untuk konsistensi */
.dashboard-layout { min-height: 100vh; background-color: #f4f7f6; }
.main-content { transition: margin-left 0.3s; }
.main-header { display: flex; align-items: center; padding: 16px; background-color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); color: #333; }
.menu-button { background: none; border: none; font-size: 24px; cursor: pointer; margin-right: 16px; }
.header-title { font-size: 20px; font-weight: bold; }
.content-area { padding: 24px; background-image: url('@/assets/images/forest_background.jpg'); background-size: cover; background-attachment: fixed; min-height: calc(100vh - 73px); position: relative; }
.content-area::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.1); }

/* Gaya yang sudah ada sebelumnya, dengan sedikit penyesuaian margin */
.main-card {
  padding: 16px; 
  background-color: rgba(255, 255, 255, 0.98);
  border-radius: 20px;
  max-width: 900px; /* Lebar maksimum untuk konten utama */
  margin: 20px auto 0 auto; /* Memberi jarak dari dashboard stats di atas */
  position: relative;
  z-index: 2;
}
.search-header { margin-bottom: 16px; }
.search-header h3 { font-size: 16px; font-weight: bold; color: #333; margin-bottom: 8px; }
.search-bar { display: flex; align-items: center; background: white; border-radius: 20px; padding: 0 16px; border: 1px solid #ddd; }
.search-bar .material-icons { color: #888; }
.search-bar input { border: none; outline: none; padding: 10px; width: 100%; background: transparent; }
.users-list { max-height: calc(100vh - 400px); overflow-y: auto; padding-right: 8px; } /* Menyesuaikan tinggi */
.user-card { background-color: #2C8A4A; color: white; padding: 16px; border-radius: 15px; margin-bottom: 16px; }
.info-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; }
.info-row span { text-align: right; }
.card-footer { margin-top: 16px; display: flex; gap: 10px; }
.btn { border: none; padding: 8px 24px; border-radius: 20px; color: white; font-weight: bold; cursor: pointer; }
.btn-edit { background-color: #007bff; }
.btn-delete { background-color: #dc3545; }
.pagination { display: flex; justify-content: space-between; align-items: center; padding: 16px 8px 8px 8px; color: #333; font-size: 14px; }
.pagination a { color: #2C8A4A; font-weight: bold; text-decoration: none; }
</style>