<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useCalculatorStore } from '@/stores/calculator';
import api from '@/api';

const router = useRouter();
const calculatorStore = useCalculatorStore();

const form = ref({
  project_name: '',
  land_area: '',
  province: '',
  city: '',
  district: '',
  village: '',
  method: 'census', // default
  allometric_equation_id: null,
});

// Data untuk dropdown
const allometricEquations = ref([]);

// Ambil data rumus alometrik dari backend saat komponen dimuat
onMounted(async () => {
  try {
    const response = await api.get('/api/formulas');
    allometricEquations.value = response.data;
    if (allometricEquations.value.length > 0) {
      // Set default jika ada
      form.value.allometric_equation_id = allometricEquations.value[0].id;
    }
  } catch (error) {
    console.error("Gagal mengambil data rumus alometrik:", error);
  }
});

const handleNext = () => {
  if (!form.value.allometric_equation_id) {
      alert('Mohon pilih rumus alometrik terlebih dahulu.');
      return;
  }
  // Simpan data form ke Pinia store
  calculatorStore.setProjectData(form.value);
  // Arahkan ke halaman input data pohon
  router.push({ name: 'tree-input' });
};

const goBack = () => {
  router.back();
};
</script>

<template>
  <div class="page-wrapper">
    <div class="background-overlay"></div>
    <div class="content-wrapper">
      <header class="page-header">
        <button @click="goBack" class="back-button">←</button>
        <h1 class="title">Kalkulator Biomassa</h1>
      </header>

      <div class="form-card">
        <form @submit.prevent="handleNext">
          <h2>1. Pendataan Wilayah & Metode</h2>
          <hr>

          <div class="form-grid">
            <div class="input-group">
              <label>Provinsi*</label>
              <input type="text" v-model="form.province" required placeholder="Contoh: Aceh">
            </div>
            <div class="input-group">
              <label>Kabupaten/Kota*</label>
              <input type="text" v-model="form.city" required placeholder="Contoh: Banda Aceh">
            </div>
            <div class="input-group">
              <label>Kecamatan*</label>
              <input type="text" v-model="form.district" required placeholder="Contoh: Syiah Kuala">
            </div>
            <div class="input-group">
              <label>Desa*</label>
              <input type="text" v-model="form.village" required placeholder="Contoh: Tibang">
            </div>
            <div class="input-group">
              <label>Nama Proyek/Hutan*</label>
              <input type="text" v-model="form.project_name" required placeholder="Contoh: Hutan Kota BNI">
            </div>
            <div class="input-group">
              <label>Luas Area (Ha)*</label>
              <input type="number" step="0.01" v-model="form.land_area" required placeholder="Contoh: 7.15">
            </div>
          </div>
          
          <div class="input-group full-width">
            <label>Pilih Rumus Alometrik*</label>
            <select v-model="form.allometric_equation_id" required>
              <option disabled :value="null">Pilih salah satu rumus</option>
              <option v-for="eq in allometricEquations" :key="eq.id" :value="eq.id">
                {{ eq.name }} ({{ eq.reference }})
              </option>
            </select>
          </div>

          <div class="radio-group full-width">
            <label>Pilih Metode Perhitungan</label>
            <div class="radio-options">
              <div>
                <input type="radio" id="sensus" value="census" v-model="form.method">
                <label for="sensus">Sensus</label>
              </div>
              <div>
                <input type="radio" id="sampling" value="sampling" v-model="form.method">
                <label for="sampling">Sampling</label>
              </div>
            </div>
          </div>

          <button type="submit" class="submit-button">Lanjut ke Data Pohon</button>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Style dari file CalculatorFormPage.vue Anda */
.page-wrapper {
  background-image: url('@/assets/images/forest_background.jpg');
  background-size: cover; background-position: center; min-height: 100vh;
  padding: 20px;
}
.background-overlay {
  position: fixed; top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  z-index: 1;
}
.content-wrapper {
  position: relative; z-index: 2; width: 100%; max-width: 800px;
  margin: 0 auto;
}
.page-header {
  display: flex; align-items: center;
  padding: 16px 0;
  color: white;
}
.title { font-size: 20px; font-weight: bold; }
.back-button {
  background: none; border: none; color: white;
  font-size: 24px; cursor: pointer; margin-right: 16px;
}
.form-card {
  padding: 24px 32px; background-color: rgba(255, 255, 255, 0.98);
  border-radius: 20px;
}
h2 { font-size: 18px; margin-bottom: 8px; color: #333; }
hr { border: 0; border-top: 1px solid #e0e0e0; margin-bottom: 24px; }
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px 24px;
}
.input-group, .radio-group { margin-bottom: 16px; }
.full-width {
  grid-column: 1 / -1;
}
.input-group label, .radio-group > label {
  display: block; margin-bottom: 8px; color: #555; font-weight: 500;
  font-size: 14px;
}
input[type="text"], input[type="number"], select {
  width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px;
  font-size: 16px;
}
.radio-options {
  display: flex;
  gap: 24px;
}
.radio-options div { display: flex; align-items: center; }
input[type="radio"] { margin-right: 8px; }
.submit-button {
  width: auto;
  padding: 12px 40px;
  margin: 16px auto 0 auto;
  display: block;
  background-color: #2C8A4A; color: white; border: none;
  border-radius: 30px; font-size: 16px; font-weight: bold; cursor: pointer;
}
</style>