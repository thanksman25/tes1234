<script setup lang="ts">
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useRecapStore } from '@/store/recapStore';
import { storeToRefs } from 'pinia';

const router = useRouter();
const recapStore = useRecapStore();

const { projects, loading, error } = storeToRefs(recapStore);

const goBack = () => router.back();

onMounted(() => {
  recapStore.fetchProjects();
});

const viewDetails = (id: number) => {
  router.push({ name: 'RecapDetail', params: { id } });
};
</script>

<template>
  <div class="page-wrapper">
    <div class="background-overlay"></div>
    <div class="content-wrapper">
      <header class="page-header">
        <button @click="goBack" class="back-button material-icons">arrow_back</button>
        <h1 class="title">Rekapitulasi</h1>
      </header>

      <div v-if="loading" class="message-box">Memuat data...</div>
      <div v-else-if="error" class="message-box error">{{ error }}</div>
      <div v-else-if="projects.length === 0" class="message-box">Belum ada riwayat rekapitulasi.</div>

      <div v-else class="recap-list">
        <div v-for="recap in projects" :key="recap.id" class="recap-item" @click="viewDetails(recap.id)">
          <div>
            <div class="item-name">{{ recap.project_name }}</div>
            <div class="item-date">Dihitung pada {{ new Date(recap.created_at).toLocaleDateString('id-ID') }}</div>
          </div>
          <span class="material-icons">chevron_right</span>
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
  display: flex; align-items: center; padding-bottom: 16px; color: white; gap: 16px;
}
.title { font-size: 20px; font-weight: bold; margin: 0; }
.back-button {
  background: none; border: none; color: white;
  font-size: 24px; cursor: pointer;
}
.recap-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.recap-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
  background-color: #2C8A4A;
  color: white;
  border-radius: 15px;
  cursor: pointer;
  transition: background-color 0.2s;
}
.recap-item:hover {
  background-color: #25733e;
}
.item-name {
  font-size: 16px;
  font-weight: bold;
}
.item-date {
  font-size: 12px;
  opacity: 0.8;
}
.message-box {
  background-color: rgba(255,255,255,0.9);
  color: #333;
  padding: 20px;
  text-align: center;
  border-radius: 15px;
  font-weight: 500;
}
.message-box.error {
  background-color: #f8d7da;
  color: #721c24;
}
</style>