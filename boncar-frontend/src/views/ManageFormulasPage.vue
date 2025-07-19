<script setup lang="ts">
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useFormulaStore, type AllometricEquation } from '@/store/formulaStore';

const router = useRouter();
const formulaStore = useFormulaStore();

const goBack = () => router.back();

onMounted(() => {
  formulaStore.fetchFormulas();
});

const goToDetail = (id: number) => {
  router.push({ name: 'FormulaDetail', params: { id } });
};

const goToAddPage = () => {
    router.push({ name: 'FormulaDetail', params: { id: 'new' } });
};

const handleDelete = (formula: AllometricEquation) => {
  if (confirm(`Apakah Anda yakin ingin menghapus rumus "${formula.name}"?`)) {
    formulaStore.deleteFormula(formula.id)
      .then(() => alert('Rumus berhasil dihapus.'))
      .catch(err => alert(err.message));
  }
};
</script>

<template>
  <div class="page-wrapper">
    <div class="background-overlay"></div>
    <div class="content-wrapper">
      <header class="page-header">
        <button @click="goBack" class="back-button material-icons">arrow_back</button>
        <span class="header-icon material-icons">functions</span>
        <h1 class="title">Manajemen Rumus</h1>
        <button @click="goToAddPage" class="btn btn-add">
            <span class="material-icons">add</span> Tambah Baru
        </button>
      </header>
      <div class="main-card">
        <div v-if="formulaStore.loading" class="loading-message">Memuat data...</div>
        <div v-else-if="formulaStore.error" class="error-message">{{ formulaStore.error }}</div>
        <div v-else class="table-container">
          <table>
            <thead>
              <tr>
                <th>Nama Rumus</th>
                <th>Referensi</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="formulaStore.formulas.length === 0">
                <td colspan="3" style="text-align: center; padding: 20px;">Belum ada data rumus.</td>
              </tr>
              <tr v-for="formula in formulaStore.formulas" :key="formula.id">
                <td data-label="Nama Rumus">{{ formula.name }}</td>
                <td data-label="Referensi">{{ formula.reference }}</td>
                <td data-label="Aksi" class="actions">
                  <button @click="goToDetail(formula.id)" class="btn btn-detail">Detail / Edit</button>
                  <button @click="handleDelete(formula)" class="btn btn-delete">Hapus</button>
                </td>
              </tr>
            </tbody>
          </table>
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
  position: relative; z-index: 2; width: 100%; max-width: 900px;
  margin: 0 auto;
}
.page-header {
  display: flex; justify-content: space-between; align-items: center; padding-bottom: 16px; color: white; gap: 12px;
}
.page-header h1 { flex-grow: 1; }
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
.table-container { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
th { background-color: #f4f4f4; font-weight: bold; }
tr:hover { background-color: #f9f9f9; }
.actions { display: flex; gap: 10px; }
.btn {
  border: none; padding: 6px 12px; border-radius: 20px;
  color: white; font-weight: bold; cursor: pointer; font-size: 12px;
  display: inline-flex; align-items: center; gap: 4px;
}
.btn-add { background-color: #27ae60; }
.btn-detail { background-color: #007bff; }
.btn-delete { background-color: #dc3545; }
.loading-message, .error-message { text-align: center; padding: 40px; color: #333; }
.error-message { color: #d32f2f; }
</style>