<script setup lang="ts">
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useCalculatorStore } from '@/store/calculator';

const router = useRouter();
const store = useCalculatorStore();

if (!store.results || !store.projectDetails) {
  router.replace({ name: 'CalculatorForm' });
}

const projectDetails = computed(() => store.projectDetails);
const results = computed(() => store.results);

const fullLocation = computed(() => {
  if (!projectDetails.value) return 'N/A';
  return [
    projectDetails.value.village,
    projectDetails.value.district,
    projectDetails.value.city,
    projectDetails.value.province,
  ].filter(Boolean).join(', ');
});

const calculationDate = computed(() => new Date().toLocaleDateString('id-ID', {
  day: 'numeric', month: 'long', year: 'numeric'
}));

const carbonMarketValue = computed(() => {
  if (!results.value) return { low: 'USD 0.00', medium: 'USD 0.00', high: 'USD 0.00' };
  
  const co2Factor = parseFloat(results.value.settings?.co2_conversion_factor || '3.67');
  const priceLow = parseFloat(results.value.settings?.carbon_price_low || '10');
  const priceMedium = parseFloat(results.value.settings?.carbon_price_medium || '30');
  const priceHigh = parseFloat(results.value.settings?.carbon_price_high || '50');

  const totalCO2 = results.value.total_carbon_ton * co2Factor;

  return {
    low: (totalCO2 * priceLow).toLocaleString('en-US', { style: 'currency', currency: 'USD' }),
    medium: (totalCO2 * priceMedium).toLocaleString('en-US', { style: 'currency', currency: 'USD' }),
    high: (totalCO2 * priceHigh).toLocaleString('en-US', { style: 'currency', currency: 'USD' }),
  };
});

const viewDetails = () => {
  if (!results.value?.project) return;
  // Simpan hasil saat ini agar bisa diakses oleh halaman detail
  // Di aplikasi yang lebih besar, ini bisa disimpan di session storage atau di-fetch ulang
  router.push({ name: 'RecapDetail', params: { id: results.value.project.id } });
};

const goBack = () => router.push({ name: 'Dashboard' });
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
          <h2 class="card-title">Informasi Proyek</h2>
          <div class="info-grid">
            <span class="label">Nama Proyek/Hutan</span>
            <span class="value">: {{ projectDetails.project_name.toUpperCase() }}</span>
            <span class="label">Luas Area</span>
            <span class="value">: {{ projectDetails.land_area.toFixed(2) }} ha</span>
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
              <p>{{ (results.total_biomass_ton || 0).toFixed(2) }} <span>Ton</span></p>
              <small>{{ (results.biomass_per_ha_ton || 0).toFixed(2) }} Ton/Ha</small>
            </div>
            <div class="metric">
              <label>Total Cadangan Karbon</label>
              <p>{{ (results.total_carbon_ton || 0).toFixed(2) }} <span>Ton C</span></p>
              <small>{{ (results.carbon_per_ha_ton || 0).toFixed(2) }} Ton C/Ha</small>
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
        
        <div class="action-card">
            <button @click="viewDetails" class="detail-button">
            Lihat Rincian & Grafik
            <span class="material-icons">arrow_forward</span>
            </button>
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
  position: relative; z-index: 2; width: 100%; max-width: 800px;
  margin: 0 auto;
}
.page-header {
  display: flex; align-items: center; padding-bottom: 16px; color: white; gap: 12px;
}
.title { font-size: 20px; font-weight: bold; margin: 0; }
.back-button {
  background: none; border: none; color: white;
  font-size: 24px; cursor: pointer;
}
.results-container {
  display: flex; flex-direction: column; gap: 24px;
}
.results-card {
  padding: 24px 32px;
  background-color: rgba(44, 138, 74, 0.85);
  border-radius: 20px;
  border: 1px solid rgba(255, 255, 255, 0.2);
}
.card-title {
  font-size: 18px; margin-bottom: 20px; font-weight: 500;
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
  padding-bottom: 12px;
}
h3.card-title { font-size: 16px; }

.info-grid {
  display: grid; grid-template-columns: 150px 1fr; gap: 12px 16px;
}
.info-grid .label { opacity: 0.8; }
.info-grid .value { font-weight: 500; }

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

.action-card {
  padding: 0;
  background-color: transparent;
  border: none;
}
.detail-button {
  width: 100%;
  padding: 16px;
  font-size: 16px;
  font-weight: bold;
  color: #2C8A4A;
  background-color: white;
  border: none;
  border-radius: 16px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: background-color 0.2s;
}
.detail-button:hover {
  background-color: #f0f0f0;
}
</style>