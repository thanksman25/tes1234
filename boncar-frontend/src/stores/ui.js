import { defineStore } from 'pinia';

export const useUiStore = defineStore('ui', {
  state: () => ({
    isLoading: false, // Untuk menampilkan spinner/loading indicator global
    notification: {
      show: false,
      message: '',
      type: 'success', // 'success' atau 'error'
    },
  }),
  actions: {
    setLoading(status) {
      this.isLoading = status;
    },
    showNotification(message, type = 'success') {
      this.notification.message = message;
      this.notification.type = type;
      this.notification.show = true;

      // Sembunyikan notifikasi setelah 3 detik
      setTimeout(() => {
        this.hideNotification();
      }, 3000);
    },
    hideNotification() {
      this.notification.show = false;
      this.notification.message = '';
    },
  },
});