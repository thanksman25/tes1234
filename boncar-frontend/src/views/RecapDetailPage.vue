<script setup lang="ts">
import { computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useRecapStore } from '@/store/recapStore';
import ScatterPlot from '@/components/ScatterPlot.vue';
import { storeToRefs } from 'pinia';

const router = useRouter();
const route = useRoute();
const recapStore = useRecapStore();

const { selectedProject, loading, error } = storeToRefs(recapStore);

const goBack = () => router.back();

onMounted(() => {
  const projectId = Number(route.params.id);
  recapStore.fetchProjectById(projectId);
});

// Menghitung lokasi lengkap dari data proyek
const fullLocation = computed(() => {
  if (!selectedProject.value?.project) return 'N/A';
  const p = selectedProject.value.project;
  return [p.village, p.district, p.city, p.province].filter(Boolean).join(', ');
});

// Menghitung tanggal dari data proyek
const calculationDate = computed(() => {
    if (!selectedProject.value?.project) return '';
    return new Date(selectedProject.value.project.created_at).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric'
    });
});

// Menghitung nilai pasar karbon
const carbonMarketValue = computed(() => {
  if (!selectedProject.value) return { low: 'USD 0.00', medium: 'USD 0.00', high: 'USD 0.00' };
  
  const results = selectedProject.value;
  const co2Factor = parseFloat(results.settings?.co2_conversion_factor || '3.67');
  const priceLow = parseFloat(results.settings?.carbon_price_low || '10');
  const priceMedium = parseFloat(results.settings?.carbon_price_medium || '30');
  const priceHigh = parseFloat(results.settings?.carbon_price_high || '50');

  const totalCO2 = results.total_carbon_ton * co2Factor;

  return {
    low: (totalCO2 * priceLow).toLocaleString('en-US', { style: 'currency', currency: 'USD' }),
    medium: (totalCO2 * priceMedium).toLocaleString('en-US', { style: 'currency', currency: 'USD' }),
    high: (totalCO2 * priceHigh).toLocaleString('en-US', { style: 'currency', currency: 'USD' }),
  };
});
</script>

<template>
  <div class="page-wrapper">
    <div class="background-overlay"></div>
    <div class="content-wrapper">
      <header class="page-header">
        <button @click="goBack" class="back-button material-icons">arrow_back</button>
        <h1 class="title">Detail Rekapitulasi Proyek</h1>
      </header>
      
      <div v-if="loading" class="loading-message">Memuat data detail...</div>
      <div v-else-if="error" class="loading-message error">{{ error }}</div>
      
      <div v-else-if="selectedProject" class="detail-container">
        <div class="results-card info-card">
          <h2 class="card-title">Informasi Proyek</h2>
          <div class="info-grid">
            <span class="label">Nama Proyek/Hutan</span>
            <span class="value">: {{ selectedProject.project.project_name.toUpperCase() }}</span>
            <span class="label">Luas Area</span>
            <span class="value">: {{ Number(selectedProject.project.land_area).toFixed(2) }} ha</span>
            <span class="label">Lokasi</span>
            <span class="value">: {{ fullLocation.toUpperCase() }}</span>
            <span class="label">Tanggal Hitung</span>
            <span class="value">: {{ calculationDate }}</span>
          </div>
        </div>

        <div class="results-card summary-card">
          <h2 class="card-title">Rekapitulasi Total</h2>
          <div class="metric-grid">
            <div class="metric">
              <label>Total Biomassa</label>
              <p>{{ (selectedProject.total_biomass_ton || 0).toFixed(2) }} <span>Ton</span></p>
              <small>{{ (selectedProject.biomass_per_ha_ton || 0).toFixed(2) }} Ton/Ha</small>
            </div>
            <div class="metric">
              <label>Total Cadangan Karbon</label>
              <p>{{ (selectedProject.total_carbon_ton || 0).toFixed(2) }} <span>Ton C</span></p>
              <small>{{ (selectedProject.carbon_per_ha_ton || 0).toFixed(2) }} Ton C/Ha</small>
            </div>
          </div>
        </div>

        <div class="results-card market-card">
          <h2 class="card-title">Estimasi Nilai Pasar Karbon</h2>
          <div class="market-grid">
            <div class="market-item">
              <label>Rendah</label>
              <p>{{ carbonMarketValue.low }}</p>
            </div>
            <div class="market-item">
              <label>Menengah</label>
              <p>{{ carbonMarketValue.medium }}</p>
            </div>
            <div class="market-item">
              <label>Tinggi</label>
              <p>{{ carbonMarketValue.high }}</p>
            </div>
          </div>
        </div>

        <div class="results-card">
          <h2 class="card-title">Rincian per Pohon</h2>
          <div class="table-responsive">
            <table>
              <thead>
                <tr>
                  <th>No</th>
                  <th>Spesies</th>
                  <th>DBH (cm)</th>
                  <th>AGB (kg)</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(tree, index) in selectedProject.trees" :key="tree.id">
                  <td>{{ index + 1 }}</td>
                  <td>{{ tree.species.name }}</td>
                  <td>{{ (tree.parameters.diameter || 0).toFixed(2) }}</td>
                  <td>{{ (tree.biomass_agb_kg || 0).toFixed(2) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="results-card">
           <h2 class="card-title">Grafik Sebar DBH vs AGB</h2>
           <ScatterPlot :data="selectedProject.trees" />
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
  color: white;
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
  display: flex; align-items: center; padding-bottom: 16px; color: white; gap: 16px;
}
.title { font-size: 20px; font-weight: bold; margin: 0; }
.back-button {
  background: none; border: none; color: white;
  font-size: 24px; cursor: pointer;
}
.detail-container {
  display: flex;
  flex-direction: column;
  gap: 24px;
}
.results-card {
  padding: 24px 32px;
  background-color: rgba(44, 138, 74, 0.85);
  border-radius: 20px;
  border: 1px solid rgba(255, 255, 255, 0.2);
}
.card-title {
  font-size: 18px;
  font-weight: 500;
  margin-bottom: 16px;
  padding-bottom: 8px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}
.table-responsive {
  max-height: 400px;
  overflow-y: auto;
}
table { width: 100%; border-collapse: collapse; }
th, td {
  padding: 10px 12px;
  text-align: left;
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}
th { font-weight: 500; opacity: 0.8; }
tbody tr:last-child td { border-bottom: none; }

.loading-message {
  text-align: center;
  color: white;
  font-size: 18px;
  padding-top: 50px;
}
.loading-message.error {
  background-color: #ffdddd;
  color: #d8000c;
  padding: 20px;
  border-radius: 15px;
}
.info-grid {
  display: grid;
  grid-template-columns: 180px 1fr;
  gap: 12px;
}
.info-grid span { font-size: 14px; }
.info-grid span:nth-child(odd) { opacity: 0.8; }
.info-grid span:nth-child(even) { font-weight: 500; }

.metric-grid {
  display: grid; grid-template-columns: 1fr 1fr; gap: 24px;
}
.metric label { font-size: 16px; opacity: 0.8; }
.metric p {
  font-size: 40px; font-weight: 700; color: white;
  margin: 4px 0 0 0;
}
.metric span { font-size: 20px; font-weight: 400; opacity: 0.8; }
.metric small { font-size: 14px; font-weight: 500; opacity: 0.9; }

.market-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 20px;
  text-align: center;
}
.market-item label { display: block; font-size: 16px; opacity: 0.8; margin-bottom: 8px; }
.market-item p { font-size: 24px; font-weight: 600; color: #a5d6b8; }
</style>