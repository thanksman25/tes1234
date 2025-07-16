<!-- boncar-frontend/src/layouts/AdminLayout.vue -->
<script setup>
import Sidebar from '@/components/Sidebar.vue';
import Notification from '@/components/Notification.vue'; // <-- TAMBAHKAN INI
</script>

<template>
  <div class="app-layout">
    <Sidebar />
    <main class="main-content">
      <router-view />
    </main>
    <Notification /> </div>
</template>

<style scoped>
/* Style tidak berubah */
.app-layout {
  display: flex;
  background-color: #F0F4F1;
}
.main-content {
  flex-grow: 1;
  padding: 30px;
  min-height: 100vh;
}
</style>