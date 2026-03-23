import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useNotificationStore = defineStore('notification', () => {
  const snackbar = ref({
    show: false,
    message: '',
    color: 'success',
    timeout: 3000,
  })

  function showSuccess(message) {
    snackbar.value = { show: true, message, color: 'success', timeout: 3000 }
  }

  function showError(message) {
    snackbar.value = { show: true, message, color: 'error', timeout: 5000 }
  }

  function showInfo(message) {
    snackbar.value = { show: true, message, color: 'info', timeout: 3000 }
  }

  function showWarning(message) {
    snackbar.value = { show: true, message, color: 'warning', timeout: 4000 }
  }

  return { snackbar, showSuccess, showError, showInfo, showWarning }
})
