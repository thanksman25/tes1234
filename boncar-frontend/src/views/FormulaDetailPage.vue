<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useFormulaStore, type AllometricEquation } from '@/store/formulaStore';

const route = useRoute();
const router = useRouter();
const formulaStore = useFormulaStore();

const isNew = computed(() => route.params.id === 'new');
const formulaId = computed(() => Number(route.params.id));

// TIPE EKSPLISIT DITAMBAHKAN DI SINI UNTUK MEMPERBAIKI ERROR
type FormulaForm = Omit<AllometricEquation, 'id' | 'equation_template'>;

const form = ref<FormulaForm>({
    name: '',
    reference: '',
    formula_agb: '',
    formula_bgb: '',
    formula_carbon: '',
    required_parameters: [],
});

const parameterOptions = [
    { value: 'circumference', label: 'Keliling (K)' },
    { value: 'height', label: 'Tinggi (H)' },
    { value: 'wood_density', label: 'Kerapatan Kayu (p)' }
];

onMounted(() => {
    if (!isNew.value) {
        formulaStore.fetchFormulaById(formulaId.value);
    }
});

watch(() => formulaStore.selectedFormula, (newVal) => {
    if (newVal && !isNew.value) {
        form.value = {
            name: newVal.name,
            reference: newVal.reference,
            formula_agb: newVal.formula_agb,
            formula_bgb: newVal.formula_bgb,
            formula_carbon: newVal.formula_carbon,
            required_parameters: newVal.required_parameters || [],
        };
    }
}, { immediate: true });

const handleSubmit = async () => {
    try {
        if (isNew.value) {
            await formulaStore.createFormula(form.value);
            alert('Rumus baru berhasil dibuat!');
        } else {
            await formulaStore.updateFormula(formulaId.value, form.value);
            alert('Rumus berhasil diperbarui!');
        }
        router.push({ name: 'ManageFormulas' });
    } catch (err: any) {
        alert(err.message);
    }
};

const goBack = () => router.back();
</script>

<template>
  <div class="page-wrapper">
    <div class="content-wrapper">
      <header class="page-header">
        <button @click="goBack" class="back-button material-icons">arrow_back</button>
        <h1 class="title">{{ isNew ? 'Tambah Rumus Baru' : 'Edit Rumus' }}</h1>
      </header>
      <div class="form-card">
        <div v-if="formulaStore.loading && !isNew" class="loading-message">Memuat data...</div>
        <form v-else @submit.prevent="handleSubmit">
            <div class="input-group">
                <label>Nama Rumus</label>
                <input type="text" v-model="form.name" required>
            </div>
            <div class="input-group">
                <label>Referensi</label>
                <input type="text" v-model="form.reference" required>
            </div>
            <div class="input-group">
                <label>Rumus AGB (Above Ground Biomass)</label>
                <textarea v-model="form.formula_agb" rows="2" required placeholder="Contoh: 0.1923 * K ** 2.15 (Gunakan ** untuk pangkat)"></textarea>
                <small>Variabel: K (keliling), D (diameter, otomatis), H (tinggi), p (kerapatan kayu)</small>
            </div>
            <div class="input-group">
                <label>Rumus BGB (Below Ground Biomass)</label>
                <textarea v-model="form.formula_bgb" rows="2" required placeholder="Contoh: AGB * 0.26"></textarea>
                <small>Variabel: AGB, K, D, H, p</small>
            </div>
            <div class="input-group">
                <label>Rumus Total Karbon</label>
                <textarea v-model="form.formula_carbon" rows="2" required placeholder="Contoh: (AGB + BGB) * 0.47"></textarea>
                <small>Variabel: AGB, BGB, K, D, H, p</small>
            </div>
            <div class="input-group">
                <label>Parameter yang Wajib Diisi Pengguna</label>
                <div class="checkbox-group">
                    <label v-for="param in parameterOptions" :key="param.value">
                        <input type="checkbox" :value="param.value" v-model="form.required_parameters"> {{ param.label }}
                    </label>
                </div>
            </div>

            <button type="submit" class="submit-button">Simpan Rumus</button>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.page-wrapper { background: #f4f7f6; min-height: 100vh; padding: 20px; }
.content-wrapper { max-width: 800px; margin: 0 auto; }
.page-header { display: flex; align-items: center; gap: 16px; margin-bottom: 20px; color: #333; }
.title { font-size: 24px; font-weight: bold; }
.back-button { background: none; border: none; font-size: 28px; cursor: pointer; }
.form-card { background: white; padding: 32px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
.input-group { margin-bottom: 24px; }
.input-group label { display: block; font-weight: 500; margin-bottom: 8px; color: #333; }
.input-group input[type="text"], .input-group textarea {
  width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; font-size: 16px; font-family: inherit;
}
.input-group small { color: #777; margin-top: 4px; display: block; }
.checkbox-group { display: flex; gap: 20px; }
.checkbox-group label { display: flex; align-items: center; gap: 8px; font-weight: normal; }
.submit-button {
  background-color: #2C8A4A; color: white; padding: 12px 30px; border-radius: 30px;
  border: none; font-size: 16px; font-weight: bold; cursor: pointer;
  display: block; margin-left: auto;
}
.loading-message { text-align: center; padding: 40px; }
</style>