<script setup lang="ts">
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useCalculatorStore } from '@/store/calculator';

const router = useRouter();
const store = useCalculatorStore();

// Redirect ke form awal jika tidak ada data hasil (misal: user refresh halaman)
if (!store.results || !store.projectDetails) {
  router.replace({ name: 'CalculatorForm' });
}

// Data rekap proyek
const projectDetails = computed(() => store.projectDetails);
const results = computed(() => store.results);

// Menggabungkan data lokasi menjadi satu string
const fullLocation = computed(() => {
  if (!projectDetails.value) return 'N/A';
  return [
    projectDetails.value.village,
    projectDetails.value.district,
    projectDetails.value.city,
    projectDetails.value.province,
  ].filter(Boolean).join(', ');
});

const calculationDate = computed(() => {
  return new Date().toLocaleDateString('id-ID', {
    day: 'numeric', month: 'long', year: 'numeric',
  });
});

const goBack = () => {
    router.push({ name: 'Dashboard' }); // Kembali ke dashboard setelah selesai
}
</script>

<template>
  <div class="page-wrapper">
     <div class="background-overlay"></div>
     <div class="content-wrapper">
       <header class="page-header">
         <button @click="goBack" class="back-button material-icons">arrow_back</button>
         <h1 class="title">Hasil Kalkulator</h1>
       </header>
       
       <div v-if="projectDetails && results" class="results-container">
          <div class="results-card info-card">
            <h2>Informasi Proyek</h2>
            <div class="info-grid">
              <div class="info-label">Nama Proyek/Hutan</div>
              <div class="info-value">: {{ projectDetails.project_name }}</div>
              
              <div class="info-label">Luas Area</div>
              <div class="info-value">: {{ projectDetails.land_area.toFixed(2) }} ha</div>

              <div class="info-label">Lokasi</div>
              <div class="info-value">: {{ fullLocation }}</div>

              <div class="info-label">Tanggal Hitung</div>
              <div class="info-value">: {{ calculationDate }}</div>
            </div>
          </div>
          
          <div class="results-card summary-card">
            <h2>Rekapitulasi Total</h2>
            <div class="metric-grid">
              <div class="metric">
                <label>Total Biomassa</label>
                <p>{{ (results.project.total_biomass_ton || 0).toFixed(2) }} <span>Ton</span></p>
              </div>
              <div class="metric">
                <label>Total Cadangan Karbon</label>
                <p>{{ (results.project.total_carbon_stock_ton || 0).toFixed(2) }} <span>Ton C</span></p>
              </div>
            </div>
          </div>

          <div class="results-card detail-card">
            <h3>Rincian Perhitungan per Pohon</h3>
            <div class="table-responsive">
              <table>
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Spesies</th>
                    <th>Keliling (cm)</th>
                    <th>Diameter (cm)</th>
                    <th>Biomassa (AGB kg)</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(tree, index) in results.project.trees" :key="tree.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{ tree.species.name }}</td>
                    <td>{{ (tree.parameters.circumference || 0).toFixed(2) }}</td>
                    <td>{{ (tree.parameters.diameter || 0).toFixed(2) }}</td>
                    <td>{{ (tree.biomass_agb_kg || 0).toFixed(2) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
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
  display: flex; align-items: center; padding: 16px; color: white;
}
.title { font-size: 20px; font-weight: bold; }
.back-button {
  background: none; border: none; color: white;
  font-size: 24px; cursor: pointer; margin-right: 16px;
}
.results-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
}
.results-card {
  padding: 24px 32px; background-color: rgba(255, 255, 255, 0.98);
  border-radius: 20px;
}
h2 { font-size: 18px; margin-bottom: 16px; color: #333; font-weight: bold; border-bottom: 1px solid #eee; padding-bottom: 8px;}
h3 { font-size: 16px; margin-bottom: 16px; color: #333; font-weight: bold;}

.info-grid {
  display: grid;
  grid-template-columns: 150px 1fr;
  gap: 8px 16px;
  color: #333;
}
.info-label { color: #555; font-size: 14px; }
.info-value { font-weight: 500; font-size: 14px; }

.metric-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
}
.metric label { font-size: 16px; color: #555; }
.metric p {
  font-size: 32px; font-weight: bold; color: #2C8A4A; margin: 4px 0 0 0;
}
.metric span { font-size: 16px; color: #777; font-weight: normal; }

.table-responsive { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; font-size: 14px; }
th, td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #eee; }
th { background-color: #f8f8f8; font-weight: 600; }
tbody tr:hover { background-color: #f9f9f9; }
</style>