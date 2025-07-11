// src/services/api.ts

import axios from 'axios';

// Buat instance Axios baru
const api = axios.create({
  // URL dasar backend Anda dari file .env.
  // Pastikan VITE_API_URL adalah http://localhost:8000 atau http://127.0.0.1:8000
  baseURL: import.meta.env.VITE_API_URL, 
  
  // WAJIB: Izinkan browser untuk mengirim dan menerima cookie dari backend.
  withCredentials: true, 
  
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
});

// Fungsi untuk mendapatkan CSRF cookie sebelum melakukan login/register.
// Ini adalah "jabat tangan" yang diperlukan oleh Laravel Sanctum.
export const getCsrfCookie = async () => {
  try {
    // Permintaan ini akan mengarah ke http://localhost:8000/sanctum/csrf-cookie
    await api.get('/sanctum/csrf-cookie');
  } catch (error) {
    console.error('Gagal mendapatkan CSRF cookie:', error);
    // Di aplikasi produksi, Anda mungkin ingin menangani error ini lebih lanjut.
  }
};

export default api;
