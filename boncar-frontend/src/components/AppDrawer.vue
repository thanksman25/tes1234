<script setup>
import { defineProps, defineEmits } from 'vue';
import { useAuthStore } from '@/stores/auth';

defineProps({
  isOpen: Boolean,
});

const emit = defineEmits(['close']);
const authStore = useAuthStore();

const handleLogout = () => {
  authStore.logout();
};

const userMenu = [
    { name: 'Beranda', icon: 'home', route: 'dashboard' },
    { name: 'Profil', icon: 'person', route: 'profile' },
    { name: 'Pengajuan Alometrik Baru', icon: 'post_add', route: 'submit-formula' },
    { name: 'Kalkulator', icon: 'calculate', route: 'calculator' },
    { name: 'Rekapitulasi', icon: 'receipt_long', route: 'recap-list' },
];

const adminMenu = [
  { name: 'Beranda', icon: 'home', route: 'admin-dashboard' },
  { name: 'Pengguna', icon: 'groups', route: 'admin-users' },
  { name: 'Verifikasi Alometrik', icon: 'fact_check', route: 'admin-submissions' },
];
</script>

<template>
  <div>
    <div v-if="isOpen" @click="emit('close')" class="drawer-overlay"></div>
    <aside class="drawer" :class="{ open: isOpen }">
      <div class="drawer-header">
        <img src="@/assets/images/logo_boncar_white.png" alt="Boncar Logo" class="drawer-logo" />
        <button @click="emit('close')" class="close-button material-icons">close</button>
      </div>
      <nav class="drawer-nav">
        <template v-if="authStore.isAdmin">
          <router-link v-for="item in adminMenu" :key="item.name" :to="{ name: item.route }" @click="emit('close')">
            <span class="material-icons">{{ item.icon }}</span>
            <span class="nav-text">{{ item.name }}</span>
          </router-link>
        </template>
        <template v-else>
           <router-link v-for="item in userMenu" :key="item.name" :to="{ name: item.route }" @click="emit('close')">
            <span class="material-icons">{{ item.icon }}</span>
            <span class="nav-text">{{ item.name }}</span>
          </router-link>
        </template>
        
        <hr class="divider">
        <a href="#" @click.prevent="handleLogout" class="logout-link">
          <span class="material-icons">logout</span>
          <span>Log Out</span>
        </a>
      </nav>
    </aside>
  </div>
</template>

<style scoped>
.drawer-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background-color: rgba(0,0,0,0.5);
  z-index: 998;
  transition: opacity 0.3s ease;
}
.drawer {
  position: fixed;
  top: 0;
  left: -280px;
  width: 280px;
  height: 100%;
  background-color: #2E7D32;
  color: white;
  box-shadow: 2px 0 5px rgba(0,0,0,0.2);
  transition: left 0.3s ease;
  z-index: 999;
  display: flex;
  flex-direction: column;
}
.drawer.open {
  left: 0;
}
.drawer-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}
.drawer-logo {
  height: 30px;
  width: auto;
}

/* PERBAIKAN CSS DI SINI */
.close-button {
  background: none;
  border: none;
  color: white;
  cursor: pointer;
  font-size: 24px;
  padding: 0;
}

.drawer-nav {
  display: flex; 
  flex-direction: column;
  padding: 10px; 
  flex-grow: 1;
}
.drawer-nav a {
  color: white; 
  text-decoration: none;
  padding: 15px 20px; 
  border-radius: 8px;
  transition: background-color 0.2s;
  display: flex; 
  align-items: center; 
  font-size: 16px;
  gap: 16px;
}
.drawer-nav a:hover, .drawer-nav .router-link-exact-active {
  background-color: #25733e;
}
.divider {
  border: 0; 
  border-top: 1px solid rgba(255, 255, 255, 0.2);
  margin: auto 10px 15px 10px;
}
.logout-link {
  font-weight: bold;
}
</style>