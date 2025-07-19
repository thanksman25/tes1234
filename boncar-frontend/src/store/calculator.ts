// #### File: src/store/calculator.ts

import { defineStore } from 'pinia';
import api from '@/services/api';
import axios from 'axios';

const WILAYAH_API_URL = 'https://thanksman25.github.io/api-wilayah-indonesia/api';

interface Wilayah {
  id: string;
  name: string;
}

export interface AllometricEquation {
  id: number;
  name: string;
  equation_template: string;
  reference: string;
}

export interface ProjectDetails {
  project_name: string;
  province: string;
  city: string;
  district: string;
  village: string;
  land_area: number;
  method: 'census' | 'sampling';
  default_equation_id: number;
}

// TIPE DATA POHON DIPERBARUI UNTUK PENCARIAN
export interface TreeData {
  id: number; // ID unik di frontend
  name: string; // Nama opsional dari pengguna, misal "Pohon #1"
  species_search_term: string; // Teks yang diketik pengguna di input pencarian
  species_id: number | null; // ID spesies dari DB kita setelah dipilih
  allometric_equation_id: number;
  circumference: number;
  height?: number;
}

interface CalculationResults {
  total_carbon_stock_ton: number;
  project: any;
}

interface CalculatorState {
  projectDetails: ProjectDetails | null;
  trees: TreeData[];
  availableEquations: AllometricEquation[];
  results: CalculationResults | null;
  loading: boolean;
  error: string | null;
  provinces: Wilayah[];
  regencies: Wilayah[];
  districts: Wilayah[];
  villages: Wilayah[];
  loadingWilayah: boolean;
}

export const BrownEquationIDs = {
  Dry: 1,
  Moist: 2,
  Wet: 3,
};

export const useCalculatorStore = defineStore('calculator', {
  state: (): CalculatorState => ({
    projectDetails: null,
    trees: [],
    availableEquations: [],
    results: null,
    loading: false,
    error: null,
    provinces: [],
    regencies: [],
    districts: [],
    villages: [],
    loadingWilayah: false,
  }),

  actions: {
    // Aksi Wilayah tetap sama
    async fetchProvinces() {
      if (this.provinces.length) return;
      this.loadingWilayah = true;
      try {
        const { data } = await axios.get(`${WILAYAH_API_URL}/provinces.json`);
        this.provinces = data;
      } catch (e) { console.error('Gagal fetch provinsi:', e); } 
      finally { this.loadingWilayah = false; }
    },
    async fetchRegencies(provinceId: string) {
      this.regencies = []; this.districts = []; this.villages = [];
      this.loadingWilayah = true;
      try {
        const { data } = await axios.get(`${WILAYAH_API_URL}/regencies/${provinceId}.json`);
        this.regencies = data;
      } catch (e) { console.error('Gagal fetch kabupaten:', e); }
      finally { this.loadingWilayah = false; }
    },
    async fetchDistricts(regencyId: string) {
      this.districts = []; this.villages = [];
      this.loadingWilayah = true;
      try {
        const { data } = await axios.get(`${WILAYAH_API_URL}/districts/${regencyId}.json`);
        this.districts = data;
      } catch (e) { console.error('Gagal fetch kecamatan:', e); }
      finally { this.loadingWilayah = false; }
    },
    async fetchVillages(districtId: string) {
      this.villages = [];
      this.loadingWilayah = true;
      try {
        const { data } = await axios.get(`${WILAYAH_API_URL}/villages/${districtId}.json`);
        this.villages = data;
      } catch (e) { console.error('Gagal fetch desa:', e); }
      finally { this.loadingWilayah = false; }
    },
    
    setProjectDetails(details: ProjectDetails) {
      this.projectDetails = details;
      this.trees = [];
      this.addTree();
    },

    addTree() {
      if (!this.projectDetails) return;
      const newTree: TreeData = {
        id: Date.now() + Math.random(),
        name: '',
        species_search_term: '',
        species_id: null,
        allometric_equation_id: this.projectDetails.default_equation_id,
        circumference: 0,
      };
      this.trees.push(newTree);
    },
    
    removeTree(treeId: number) {
      if (this.trees.length > 1) {
        this.trees = this.trees.filter(tree => tree.id !== treeId);
      } else {
        alert('Minimal harus ada satu data pohon.');
      }
    },

    async fetchAvailableEquations() {
      if (this.availableEquations.length > 0) return;
      this.loading = true;
      try {
        const { data } = await api.get<AllometricEquation[]>('/formulas');
        this.availableEquations = data;
      } catch (err) {
        this.error = 'Gagal memuat daftar rumus.';
      } finally {
        this.loading = false;
      }
    },
    
    async submitAndCalculate() {
      if (!this.projectDetails || this.trees.some(t => !t.species_id)) {
        this.error = 'Pastikan semua data proyek dan spesies pohon telah diisi.';
        alert(this.error);
        return false;
      }
      this.loading = true;
      this.error = null;

      const payload = {
        ...this.projectDetails,
        trees: this.trees.map(tree => {
          const diameter = tree.circumference > 0 ? tree.circumference / Math.PI : 0;
          return {
            species_id: tree.species_id,
            allometric_equation_id: tree.allometric_equation_id,
            parameters: {
              diameter: parseFloat(diameter.toFixed(2)),
              height: tree.height,
            },
          };
        }),
      };

      try {
        const { data } = await api.post('/calculator-projects', payload);
        this.results = data;
        return true;
      } catch (err: any) {
        this.error = 'Terjadi kesalahan saat kalkulasi.';
        console.error(err);
        return false;
      } finally {
        this.loading = false;
      }
    },

    getEquationNameById(id: number): string {
      const equation = this.availableEquations.find(eq => eq.id === id);
      return equation ? `${equation.name}` : 'Rumus Default';
    }
  },
});