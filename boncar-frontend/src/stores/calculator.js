// boncar-frontend/src/stores/calculator.js
import { defineStore } from 'pinia';

export const useCalculatorStore = defineStore('calculator', {
  state: () => ({
    // Menyimpan data proyek seperti nama hutan, luas, dll.
    projectData: null, 
    // Menyimpan daftar pohon yang diinput pengguna
    trees: [],
    // Menyimpan hasil akhir kalkulasi
    results: null,
  }),
  actions: {
    // Aksi ini dipanggil dari halaman form wilayah
    setProjectData(data) {
      this.projectData = data;
      this.trees = []; // Kosongkan daftar pohon setiap kali proyek baru dibuat
      this.results = null; // Hapus hasil lama
      this.addTree(); // Otomatis tambahkan satu form pohon untuk memulai
    },

    // Menambah satu form input pohon baru
    addTree() {
      const newTree = {
        id: Date.now() + Math.random(), // ID unik sementara
        name: '', // Nama pohon (opsional)
        species_id: null, // Akan diisi dari API spesies
        parameters: {
          diameter: 0,
          height: 0, // Opsional tergantung rumus
        },
      };
      this.trees.push(newTree);
    },

    // Menghapus form input pohon
    removeTree(treeId) {
      if (this.trees.length > 1) {
        this.trees = this.trees.filter(tree => tree.id !== treeId);
      } else {
        alert('Minimal harus ada satu data pohon.');
      }
    },
    
    // Untuk menyimpan hasil dari backend
    setResults(data) {
        this.results = data;
    },

    // Mengosongkan semua data saat proses selesai atau dibatalkan
    clearCalculatorState() {
      this.projectData = null;
      this.trees = [];
      this.results = null;
    }
  },
});