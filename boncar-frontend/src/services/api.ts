// src/services/api.ts

import axios from 'axios';


const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  withCredentials: true, // <-- PASTIKAN INI ADA DAN BERNILAI TRUE
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
