<script setup lang="ts">
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useUserManagementStore, type User } from '@/store/userManagement';

const router = useRouter();
const userManagementStore = useUserManagementStore();

const goBack = () => router.back();

// Ambil data pengguna saat komponen pertama kali dimuat
onMounted(() => {
  userManagementStore.fetchUsers();
});

// Fungsi untuk mengubah peran pengguna
const handleEditRole = async (user: User) => {
  const newRole = user.role === 'admin' ? 'user' : 'admin';
  if (confirm(`Apakah Anda yakin ingin mengubah peran ${user.name} menjadi ${newRole}?`)) {
    try {
      await userManagementStore.updateUserRole(user.id, newRole);
      alert('Peran pengguna berhasil diperbarui.');
    } catch (error: any) {
      alert(error.message || 'Gagal memperbarui peran.');
    }
  }
};

// Fungsi untuk menghapus pengguna
const handleDeleteUser = async (user: User) => {
  if (confirm(`PERINGATAN: Apakah Anda yakin ingin menghapus pengguna ${user.name} secara permanen? Tindakan ini tidak dapat dibatalkan.`)) {
    try {
      await userManagementStore.deleteUser(user.id);
      alert('Pengguna berhasil dihapus.');
    } catch (error: any) {
      alert(error.message || 'Gagal menghapus pengguna.');
    }
  }
};

// Fungsi untuk navigasi paginasi
const changePage = (page: number) => {
  if (page > 0 && page <= userManagementStore.pagination.lastPage) {
    userManagementStore.fetchUsers(page);
  }
};
</script>

<template>
  <div class="page-wrapper">
    <div class="background-overlay"></div>
    <div class="content-wrapper">
      <header class="page-header">
        <button @click="goBack" class="back-button material-icons">arrow_back</button>
        <span class="header-icon material-icons">groups</span>
        <h1 class="title">Pengguna</h1>
      </header>
      <div class="main-card">
        <div class="search-header">
          <h3>Data Pengguna (Total: {{ userManagementStore.pagination.total }})</h3>
          <div class="search-bar">
            <span class="material-icons">search</span>
            <input type="text" placeholder="Cari pengguna...">
          </div>
        </div>

        <div v-if="userManagementStore.loading" class="loading-message">
          Memuat data pengguna...
        </div>
        <div v-else-if="userManagementStore.error" class="error-message">
          {{ userManagementStore.error }}
        </div>
        <div v-else-if="userManagementStore.users.length === 0" class="empty-message">
          Tidak ada data pengguna untuk ditampilkan.
        </div>
        
        <div v-else class="users-list">
          <div v-for="user in userManagementStore.users" :key="user.id" class="user-card">
            <div class="info-row"><strong>Nama Lengkap:</strong><span>{{ user.name }}</span></div>
            <div class="info-row"><strong>Email:</strong><span>{{ user.email }}</span></div>
            <div class="info-row"><strong>Hak Akses:</strong>
              <span class="role-badge" :class="user.role">{{ user.role }}</span>
            </div>
            <div class="card-footer">
              <button @click="handleEditRole(user)" class="btn btn-edit">Ubah Hak Akses</button>
              <button @click="handleDeleteUser(user)" class="btn btn-delete">Hapus</button>
            </div>
          </div>
        </div>
        
        <div v-if="userManagementStore.pagination.lastPage > 1" class="pagination">
          <a href="#" @click.prevent="changePage(userManagementStore.pagination.currentPage - 1)" :class="{ disabled: userManagementStore.pagination.currentPage === 1 }">&lt;&lt; Previous</a>
          <span>Halaman {{ userManagementStore.pagination.currentPage }} dari {{ userManagementStore.pagination.lastPage }}</span>
          <a href="#" @click.prevent="changePage(userManagementStore.pagination.currentPage + 1)" :class="{ disabled: userManagementStore.pagination.currentPage === userManagementStore.pagination.lastPage }">Next &gt;&gt;</a>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.page-wrapper {
  background-image: url('@/assets/images/forest_background.jpg');
  background-size: cover; background-position: center; min-height: 100vh;
  padding: 20px;
}
.background-overlay {
  position: fixed; top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0, 0, 0, 0.6); z-index: 1;
}
.content-wrapper {
  position: relative; z-index: 2; width: 100%; max-width: 420px;
  margin: 0 auto;
}
.page-header {
  display: flex; align-items: center; padding-bottom: 16px; color: white; gap: 12px;
}
.title { font-size: 20px; font-weight: bold; margin: 0; }
.header-icon { font-size: 28px; }
.back-button {
  background: none; border: none; color: white;
  font-size: 24px; cursor: pointer;
}
.main-card {
  padding: 16px; background-color: rgba(255, 255, 255, 0.98);
  border-radius: 20px;
}
.search-header {
  margin-bottom: 16px;
}
.search-header h3 {
  font-size: 16px;
  font-weight: bold;
  color: #333;
  margin-bottom: 8px;
}
.search-bar {
  display: flex;
  align-items: center;
  background: white;
  border-radius: 20px;
  padding: 0 16px;
  border: 1px solid #ddd;
}
.search-bar .material-icons { color: #888; }
.search-bar input {
  border: none; outline: none; padding: 10px;
  width: 100%; background: transparent;
}
.users-list {
  max-height: calc(100vh - 350px); /* Disesuaikan agar pagination muat */
  overflow-y: auto;
  padding-right: 8px;
}
.user-card {
  background-color: #2C8A4A;
  color: white;
  padding: 16px;
  border-radius: 15px;
  margin-bottom: 16px;
}
.info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
  font-size: 14px;
}
.info-row span {
  text-align: right;
  max-width: 70%;
}
.role-badge {
  padding: 4px 10px;
  border-radius: 12px;
  font-weight: bold;
  font-size: 12px;
  text-transform: capitalize;
}
.role-badge.admin {
  background-color: #e74c3c;
  color: white;
}
.role-badge.user {
  background-color: #3498db;
  color: white;
}
.card-footer {
  margin-top: 16px;
  display: flex;
  gap: 10px;
}
.btn {
  border: none;
  padding: 8px 16px; /* Padding disesuaikan */
  border-radius: 20px;
  color: white;
  font-weight: bold;
  cursor: pointer;
  font-size: 12px; /* Ukuran font disesuaikan */
}
.btn-edit { background-color: #007bff; }
.btn-delete { background-color: #dc3545; }
.pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 8px 8px 8px;
  color: #333;
  font-size: 14px;
}
.pagination a {
  color: #2C8A4A;
  font-weight: bold;
  text-decoration: none;
}
.pagination a.disabled {
  color: #aaa;
  pointer-events: none;
}
.loading-message, .error-message, .empty-message {
  text-align: center;
  padding: 40px;
  background-color: rgba(240, 240, 240, 0.8);
  border-radius: 15px;
  color: #333;
  font-weight: 500;
}
.error-message {
  background-color: rgba(255, 224, 224, 0.9);
  color: #d32f2f;
}
</style>