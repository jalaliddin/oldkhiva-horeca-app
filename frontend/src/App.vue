<template>
  <v-app :theme="'oldkhivaTheme'">
    <component :is="layoutComponent">
      <router-view />
    </component>
    <v-snackbar
      v-model="notification.snackbar.show"
      :color="notification.snackbar.color"
      :timeout="notification.snackbar.timeout"
      location="top right"
      rounded="lg"
    >
      {{ notification.snackbar.message }}
      <template #actions>
        <v-btn icon="mdi-close" size="small" @click="notification.snackbar.show = false" />
      </template>
    </v-snackbar>
  </v-app>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useNotificationStore } from '@/stores/notification'
import LandingLayout from '@/layouts/LandingLayout.vue'
import AuthLayout from '@/layouts/AuthLayout.vue'
import ClientLayout from '@/layouts/ClientLayout.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'

const route = useRoute()
const notification = useNotificationStore()

const layoutComponent = computed(() => {
  const layout = route.meta.layout || 'landing'
  const layouts = {
    landing: LandingLayout,
    auth: AuthLayout,
    client: ClientLayout,
    admin: AdminLayout,
  }
  return layouts[layout] || LandingLayout
})
</script>
