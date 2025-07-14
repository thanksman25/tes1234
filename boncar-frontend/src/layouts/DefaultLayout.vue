<script setup>
import { ref } from 'vue';
import AppDrawer from '@/components/AppDrawer.vue';
import Notification from '@/components/Notification.vue';

const drawerOpen = ref(false);
const toggleDrawer = () => {
  drawerOpen.value = !drawerOpen.value;
};
</script>

<template>
  <div class="main-layout">
    <AppDrawer :is-open="drawerOpen" @close="drawerOpen = false" />
    
    <div class="content-wrapper">
      <header class="main-header">
        <button @click="toggleDrawer" class="menu-button">☰</button>
        <h1 class="header-title">Boncar</h1>
      </header>
      
      <main class="content-area">
        <router-view />
      </main>
    </div>
    
    <Notification />
  </div>
</template>

<style scoped>
.main-layout {
  min-height: 100vh;
  background-color: #f4f7f6; /* Warna latar belakang netral */
}
.main-header {
  display: flex;
  align-items: center;
  padding: 16px 24px;
  background-color: #fff;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
  color: #333;
  position: sticky;
  top: 0;
  z-index: 990;
}
.menu-button {
  background: none; border: none; font-size: 24px;
  cursor: pointer; margin-right: 16px; color: #333;
}
.header-title {
  font-size: 20px; font-weight: bold;
}
.content-area {
  padding: 24px;
}
</style>