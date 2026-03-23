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
      <div class="pa-4 d-flex align-center" style="border-bottom: 1px solid rgba(255,255,255,0.08); min-height: 64px;">
        <v-icon color="accent" size="32">mdi-silverware-fork-knife</v-icon>
        <transition name="fade">
          <div v-if="!rail" class="ml-3">
            <div class="text-white font-weight-bold text-subtitle-1">OldKhiva</div>
            <div class="text-caption" style="color: #C8941A;">{{ auth.user?.company_name }}</div>
          </div>
        </transition>
      </div>

      <v-list nav density="compact" class="pa-3 mt-2">
        <v-list-item
          v-for="item in clientNavItems"
          :key="item.to"
          :to="item.to"
          :prepend-icon="item.icon"
          :title="rail ? '' : item.title"
          active-color="accent"
          rounded="lg"
          class="mb-1"
          style="color: #B0BEC5"
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

    <v-app-bar elevation="0" color="white" border="b">
      <v-app-bar-nav-icon @click="rail = !rail" />
      <v-app-bar-title>
        <span class="text-primary font-weight-medium">{{ auth.user?.company_name || 'Kabinet' }}</span>
      </v-app-bar-title>
      <v-spacer />
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

const clientNavItems = [
  { title: 'Bosh sahifa', icon: 'mdi-home-outline', to: '/client' },
  { title: 'Shartnoma', icon: 'mdi-file-sign', to: '/client/contract' },
  { title: 'Menyu', icon: 'mdi-food-outline', to: '/client/menu' },
  { title: 'Bron qilish', icon: 'mdi-calendar-plus-outline', to: '/client/bookings/new' },
  { title: 'Bronlarim', icon: 'mdi-calendar-text-outline', to: '/client/bookings' },
  { title: 'Invoicelar', icon: 'mdi-receipt-text-outline', to: '/client/invoices' },
  { title: "To'lovlar", icon: 'mdi-cash-multiple', to: '/client/payments' },
]

async function handleLogout() {
  await auth.logout()
  router.push('/login')
}
</script>
