import axios from 'axios';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  withCredentials: true,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
});

export const getCsrfCookie = async () => {
  try {
    await api.get('/sanctum/csrf-cookie');
  } catch (error) {
    console.error('Gagal mendapatkan CSRF cookie:', error);
  }
};

export default api;