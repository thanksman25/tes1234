import axios from 'axios';

// Membuat instance Axios dengan konfigurasi dasar
const api = axios.create({
  // Mengambil URL base API dari file .env yang sudah kita buat
  baseURL: import.meta.env.VITE_API_BASE_URL,
  
  // 'withCredentials' sangat PENTING untuk Laravel Sanctum agar bisa bertukar cookie otentikasi
  withCredentials: true, 
  
  // Header default yang dibutuhkan oleh backend Laravel
  headers: {
    'X-Requested-With': 'XMLHttpRequest',
    'Accept': 'application/json',
  },
});

// Interceptor: Kode ini akan berjalan SETIAP KALI kita akan mengirim request
// Tujuannya adalah untuk otomatis melampirkan token otentikasi jika ada.
// Kita akan melengkapi logika ini nanti setelah membuat sistem login.
api.interceptors.request.use(config => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Export instance api agar bisa digunakan di seluruh aplikasi
export default api;