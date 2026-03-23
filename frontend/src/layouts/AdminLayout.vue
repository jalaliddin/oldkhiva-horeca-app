<template>
  <v-layout>
    <!-- Sidebar -->
    <v-navigation-drawer
      v-model="drawer"
      :rail="rail"
      permanent
      color="#0F1C35"
      width="260"
    >
      <!-- Logo -->
      <div class="pa-4 d-flex align-center" style="border-bottom: 1px solid rgba(255,255,255,0.08); min-height: 64px;">
        <v-icon color="accent" size="32">mdi-silverware-fork-knife</v-icon>
        <transition name="fade">
          <span v-if="!rail" class="ml-3 text-white font-weight-bold text-h6">OldKhiva</span>
        </transition>
      </div>

      <!-- Nav items -->
      <v-list nav density="compact" class="pa-3 mt-2">
        <v-list-item
          v-for="item in adminNavItems"
          :key="item.to"
          :to="item.to"
          :prepend-icon="item.icon"
          :title="rail ? '' : item.title"
          active-color="accent"
          rounded="lg"
          class="mb-1"
          style="color: #B0BEC5"
          :slim="rail"
        >
          <template v-if="rail" #prepend>
            <v-tooltip :text="item.title" location="right">
              <template #activator="{ props }">
                <v-icon v-bind="props">{{ item.icon }}</v-icon>
              </template>
            </v-tooltip>
          </template>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <!-- Top bar -->
    <v-app-bar elevation="0" color="white" border="b">
      <v-app-bar-nav-icon @click="rail = !rail" />
      <v-app-bar-title>
        <span class="text-primary font-weight-medium">Admin Panel</span>
      </v-app-bar-title>
      <v-spacer />
      <v-btn icon="mdi-bell-outline" />
      <v-menu>
        <template #activator="{ props }">
          <v-avatar v-bind="props" color="primary" size="36" class="mr-3 cursor-pointer">
            <span class="text-white text-caption font-weight-bold">{{ initials }}</span>
          </v-avatar>
        </template>
        <v-list min-width="160">
          <v-list-item :subtitle="auth.user?.email" :title="auth.user?.name" />
          <v-divider />
          <v-list-item title="Chiqish" prepend-icon="mdi-logout" @click="handleLogout" />
        </v-list>
      </v-menu>
    </v-app-bar>

    <!-- Main content -->
    <v-main style="background: #F5F6FA;">
      <v-container fluid class="pa-6">
        <router-view />
      </v-container>
    </v-main>
  </v-layout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const router = useRouter()
const drawer = ref(true)
const rail = ref(false)

const initials = computed(() => {
  const name = auth.user?.name || ''
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
})

const adminNavItems = [
  { title: 'Dashboard', icon: 'mdi-view-dashboard-outline', to: '/admin' },
  { title: 'Mijozlar', icon: 'mdi-account-group-outline', to: '/admin/clients' },
  { title: 'Shartnomalar', icon: 'mdi-file-document-outline', to: '/admin/contracts' },
  { title: 'Menyu', icon: 'mdi-food-outline', to: '/admin/menu' },
  { title: 'Xizmatlar', icon: 'mdi-room-service-outline', to: '/admin/services' },
  { title: 'Bronlar', icon: 'mdi-calendar-check-outline', to: '/admin/bookings' },
  { title: 'Invoicelar', icon: 'mdi-receipt-text-outline', to: '/admin/invoices' },
  { title: "To'lovlar", icon: 'mdi-cash-multiple', to: '/admin/payments' },
  { title: 'Hisobotlar', icon: 'mdi-chart-bar', to: '/admin/reports' },
  { title: 'Landing Page', icon: 'mdi-web', to: '/admin/landing' },
]

async function handleLogout() {
  await auth.logout()
  router.push('/login')
}
</script>
