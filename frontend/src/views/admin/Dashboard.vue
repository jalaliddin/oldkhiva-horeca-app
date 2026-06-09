<template>
  <div>
    <div class="text-h5 font-weight-bold text-primary mb-6">{{ i18n.t('adminDashboard.title') }}</div>

    <!-- Stats Cards -->
    <v-row class="mb-6">
      <v-col cols="12" sm="6" md="3">
        <v-card color="primary" class="pa-4">
          <div class="d-flex align-center justify-space-between">
            <div>
              <div class="text-caption" style="color: rgba(255,255,255,0.7)">{{ i18n.t('adminDashboard.totalClients') }}</div>
              <div class="text-h5 font-weight-bold text-white">{{ stats.total_clients }}</div>
            </div>
            <v-icon color="rgba(255,255,255,0.4)" size="48">mdi-account-group</v-icon>
          </div>
          <div class="mt-2">
            <v-chip size="x-small" color="warning" v-if="stats.pending_clients > 0">
              {{ stats.pending_clients }} {{ i18n.t('common.pending') }}
            </v-chip>
          </div>
        </v-card>
      </v-col>
      <v-col cols="12" sm="6" md="3">
        <v-card color="success" class="pa-4">
          <div class="d-flex align-center justify-space-between">
            <div>
              <div class="text-caption" style="color: rgba(255,255,255,0.7)">{{ i18n.t('adminDashboard.totalBookings') }}</div>
              <div class="text-h5 font-weight-bold text-white">{{ stats.total_bookings }}</div>
            </div>
            <v-icon color="rgba(255,255,255,0.4)" size="48">mdi-calendar-check</v-icon>
          </div>
          <div class="mt-2">
            <v-chip size="x-small" color="warning" v-if="stats.pending_bookings > 0">
              {{ stats.pending_bookings }} {{ i18n.t('common.pending') }}
            </v-chip>
          </div>
        </v-card>
      </v-col>
      <v-col cols="12" sm="6" md="3">
        <v-card color="accent" class="pa-4">
          <div class="d-flex align-center justify-space-between">
            <div>
              <div class="text-caption" style="color: rgba(255,255,255,0.7)">{{ i18n.t('adminDashboard.totalRevenue') }}</div>
              <div class="text-h6 font-weight-bold text-white">{{ Number(stats.total_revenue || 0).toLocaleString() }}</div>
              <div class="text-caption" style="color: rgba(255,255,255,0.7)">{{ i18n.t('common.currency') }}</div>
            </div>
            <v-icon color="rgba(255,255,255,0.4)" size="48">mdi-cash-multiple</v-icon>
          </div>
        </v-card>
      </v-col>
      <v-col cols="12" sm="6" md="3">
        <v-card color="error" class="pa-4">
          <div class="d-flex align-center justify-space-between">
            <div>
              <div class="text-caption" style="color: rgba(255,255,255,0.7)">{{ i18n.t('adminDashboard.unpaidInvoices') }}</div>
              <div class="text-h5 font-weight-bold text-white">{{ stats.unpaid_invoices }}</div>
            </div>
            <v-icon color="rgba(255,255,255,0.4)" size="48">mdi-receipt-text</v-icon>
          </div>
        </v-card>
      </v-col>
    </v-row>

    <v-row>
      <!-- Recent Bookings -->
      <v-col cols="12" md="8">
        <v-card>
          <v-card-title class="pa-4 text-primary">{{ i18n.t('adminDashboard.recentBookings') }}</v-card-title>
          <v-data-table
            :headers="bookingHeaders"
            :items="recentBookings"
            :loading="loading"
            density="compact"
            hide-default-footer
          >
            <template #item.status="{ item }">
              <v-chip :color="statusColor(item.status)" size="x-small" variant="tonal">
                {{ statusLabel(item.status) }}
              </v-chip>
            </template>
            <template #item.total_amount="{ item }">
              {{ Number(item.total_amount).toLocaleString() }} {{ i18n.t('common.currency') }}
            </template>
            <template #item.actions="{ item }">
              <v-btn size="small" icon="mdi-eye" variant="text" :to="`/admin/bookings/${item.id}`" />
            </template>
          </v-data-table>
        </v-card>
      </v-col>

      <!-- Pending Clients -->
      <v-col cols="12" md="4">
        <v-card>
          <v-card-title class="pa-4 text-primary">
            {{ i18n.t('adminDashboard.newApplications') }}
            <v-badge v-if="pendingClients.length" :content="pendingClients.length" color="error" inline />
          </v-card-title>
          <v-list density="compact">
            <v-list-item
              v-for="client in pendingClients"
              :key="client.id"
              :title="client.company_name || client.name"
              :subtitle="client.email"
              :to="`/admin/clients/${client.id}`"
            >
              <template #append>
                <v-chip size="x-small" color="warning">{{ i18n.t('adminDashboard.new') }}</v-chip>
              </template>
            </v-list-item>
            <v-list-item v-if="pendingClients.length === 0">
              <v-list-item-title class="text-medium-emphasis text-caption">{{ i18n.t('adminDashboard.noApplications') }}</v-list-item-title>
            </v-list-item>
          </v-list>
          <v-card-actions>
            <v-btn variant="text" color="primary" to="/admin/clients">{{ i18n.t('adminDashboard.viewAll') }}</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useI18nStore } from '@/stores/i18n'
import api from '@/plugins/axios'

const i18n = useI18nStore()
const loading = ref(true)
const stats = ref({})
const recentBookings = ref([])
const pendingClients = ref([])

const bookingHeaders = computed(() => [
  { title: i18n.t('bookings.bookingNum'), key: 'booking_number' },
  { title: i18n.t('bookings.client'), key: 'client.company_name' },
  { title: i18n.t('bookings.eventDate'), key: 'event_date' },
  { title: i18n.t('common.amount'), key: 'total_amount' },
  { title: i18n.t('common.status'), key: 'status' },
  { title: '', key: 'actions', sortable: false },
])

function statusColor(s) {
  return { pending: 'warning', approved: 'success', rejected: 'error', cancelled: 'grey', completed: 'info' }[s] || 'grey'
}
function statusLabel(s) { return i18n.t(`bookingStatus.${s}`) || s }

onMounted(async () => {
  try {
    const res = await api.get('/admin/dashboard')
    stats.value = res.data.data.stats
    recentBookings.value = res.data.data.recent_bookings
    pendingClients.value = res.data.data.pending_clients
  } finally {
    loading.value = false
  }
})
</script>
