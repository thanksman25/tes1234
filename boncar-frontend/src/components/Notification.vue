<script setup>
import { useUiStore } from '@/stores/ui';

const uiStore = useUiStore();
</script>

<template>
  <div
    v-if="uiStore.notification.show"
    class="notification-container"
    :class="uiStore.notification.type"
  >
    {{ uiStore.notification.message }}
  </div>
</template>

<style scoped>
.notification-container {
  position: fixed;
  top: 20px;
  right: 20px;
  padding: 15px 25px;
  border-radius: 8px;
  color: white;
  font-weight: bold;
  z-index: 1000;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  animation: slide-in 0.3s ease-out;
}
.notification-container.success {
  background-color: #4CAF50;
}
.notification-container.error {
  background-color: #f44336;
}

@keyframes slide-in {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}
</style>