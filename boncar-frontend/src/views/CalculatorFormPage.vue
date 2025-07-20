<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useCalculatorStore, type ProjectDetails, BrownEquationIDs } from '@/store/calculator';
import { storeToRefs } from 'pinia';

const router = useRouter();
const calculatorStore = useCalculatorStore();

const { provinces, regencies, districts, villages, loadingWilayah } = storeToRefs(calculatorStore);

const form = ref({
  project_name: '',
  land_area: '',
  sample_area: '', // <-- INPUT BARU
  province_id: '',
  regency_id: '',
  district_id: '',
  village_id: '',
  method: 'census' as 'census' | 'sampling',
  default_equation_id: BrownEquationIDs.Moist,
});

onMounted(() => {
  calculatorStore.fetchProvinces();
});

watch(() => form.value.province_id, (newId) => {
  if (newId) calculatorStore.fetchRegencies(newId);
  form.value.regency_id = '';
  form.value.district_id = '';
  form.value.village_id = '';
});
watch(() => form.value.regency_id, (newId) => {
  if (newId) calculatorStore.fetchDistricts(newId);
  form.value.district_id = '';
  form.value.village_id = '';
});
watch(() => form.value.district_id, (newId) => {
  if (newId) calculatorStore.fetchVillages(newId);
  form.value.village_id = '';
});


const handleNext = () => {
  const landArea = parseFloat(form.value.land_area);
  const sampleArea = parseFloat(form.value.sample_area);

  if (isNaN(landArea) || landArea <= 0) {
    alert('Luas Area Estimasi harus berupa angka positif.');
    return;
  }
  if (form.value.method === 'sampling' && (isNaN(sampleArea) || sampleArea <= 0)) {
    alert('Total Luas Plot Sampel wajib diisi untuk metode sampling.');
    return;
  }
  if (!form.value.village_id) {
    alert('Semua data lokasi wajib diisi.');
    return;
  }

  const getWilayahName = (list: any[], id: string) => list.find(item => item.id === id)?.name || '';

  // Perbarui interface ProjectDetails untuk menyertakan sample_area
  const projectDetails: ProjectDetails & { sample_area?: number } = {
    project_name: form.value.project_name,
    land_area: landArea,
    province: getWilayahName(provinces.value, form.value.province_id),
    city: getWilayahName(regencies.value, form.value.regency_id),
    district: getWilayahName(districts.value, form.value.district_id),
    village: getWilayahName(villages.value, form.value.village_id),
    method: form.value.method,
    default_equation_id: form.value.default_equation_id,
  };

  if (form.value.method === 'sampling') {
    projectDetails.sample_area = sampleArea;
  }

  calculatorStore.setProjectDetails(projectDetails);
  router.push({ name: 'TreeInput' });
};

const goBack = () => {
  router.back();
}
</script>

<template>
  <div class="page-wrapper">
    <div class="background-overlay"></div>
    <div class="content-wrapper">
      <header class="page-header">
         <button @click="goBack" class="back-button material-icons">arrow_back</button>
         <h1 class="title">Kalkulator Biomassa</h1>
      </header>

      <div class="form-card">
        <form @submit.prevent="handleNext">
          <h2>1. Pendataan Wilayah & Metode</h2>
          <hr>

          <div class="form-grid">
             <div class="input-group full-width">
              <label>Nama Proyek/Hutan*</label>
              <input type="text" v-model="form.project_name" required>
            </div>
            <div class="input-group">
              <label>Provinsi*</label>
              <select v-model="form.province_id" required :disabled="provinces.length === 0">
                <option disabled value="">{{ loadingWilayah && !provinces.length ? 'Memuat...' : 'Pilih Provinsi' }}</option>
                <option v-for="p in provinces" :key="p.id" :value="p.id">{{ p.name }}</option>
              </select>
            </div>
            <div class="input-group">
              <label>Kabupaten/Kota*</label>
              <select v-model="form.regency_id" required :disabled="!form.province_id">
                <option disabled value="">{{ loadingWilayah && !regencies.length ? 'Memuat...' : 'Pilih Kabupaten/Kota' }}</option>
                <option v-for="r in regencies" :key="r.id" :value="r.id">{{ r.name }}</option>
              </select>
            </div>
            <div class="input-group">
              <label>Kecamatan*</label>
              <select v-model="form.district_id" required :disabled="!form.regency_id">
                <option disabled value="">{{ loadingWilayah && !districts.length ? 'Memuat...' : 'Pilih Kecamatan' }}</option>
                <option v-for="d in districts" :key="d.id" :value="d.id">{{ d.name }}</option>
              </select>
            </div>
            <div class="input-group">
              <label>Desa*</label>
              <select v-model="form.village_id" required :disabled="!form.district_id">
                <option disabled value="">{{ loadingWilayah && !villages.length ? 'Memuat...' : 'Pilih Desa' }}</option>
                <option v-for="v in villages" :key="v.id" :value="v.id">{{ v.name }}</option>
              </select>
            </div>
            
            <div class="radio-group full-width">
                <label>Pilih Metode Perhitungan*</label>
                <div class="radio-options">
                  <div>
                    <input type="radio" id="census" value="census" v-model="form.method">
                    <label for="census">Sensus (Mengukur semua pohon)</label>
                  </div>
                  <div>
                    <input type="radio" id="sampling" value="sampling" v-model="form.method">
                    <label for="sampling">Sampling (Menggunakan plot sampel)</label>
                  </div>
                </div>
            </div>

            <div class="input-group">
              <label>Luas Area Estimasi (Ha)*</label>
              <input type="text" v-model="form.land_area" placeholder="Contoh: 5" required>
            </div>

            <div v-if="form.method === 'sampling'" class="input-group">
              <label>Total Luas Plot Sampel (Ha)*</label>
              <input type="text" v-model="form.sample_area" placeholder="Contoh: 0.5" required>
            </div>
          </div>
          
          <div class="input-group full-width">
            <label>Pilih Zona Iklim (untuk rumus Brown)*</label>
            <select v-model.number="form.default_equation_id">
              <option :value="BrownEquationIDs.Moist">Iklim Lembab (1500-4000 mm/th)</option>
              <option :value="BrownEquationIDs.Dry">Iklim Kering (&lt;1500 mm/th)</option>
              <option :value="BrownEquationIDs.Wet">Iklim Basah (&gt;4000 mm/th)</option>
            </select>
          </div>

          <button type="submit" class="submit-button">Lanjut ke Data Pohon</button>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* CSS tidak berubah */
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
  padding: 16px;
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
input[type="text"], select {
  width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px;
  font-size: 16px;
}
select:disabled {
  background-color: #f0f0f0;
  cursor: not-allowed;
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