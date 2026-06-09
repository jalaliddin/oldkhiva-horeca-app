<template>
  <div>
    <div class="text-h5 font-weight-bold text-primary mb-6">{{ i18n.t('dashboard.title') }}</div>

    <v-row>
      <v-col cols="12" md="3">
        <v-card color="primary" class="pa-4">
          <div class="d-flex align-center justify-space-between">
            <div>
              <div class="text-caption" style="color: rgba(255,255,255,0.7)">{{ i18n.t('dashboard.balance') }}</div>
              <div class="text-h6 font-weight-bold text-white mt-1">
                {{ Number(deposit?.balance || 0).toLocaleString() }} {{ i18n.t('common.currency') }}
              </div>
            </div>
            <v-icon color="rgba(255,255,255,0.5)" size="40">mdi-wallet</v-icon>
          </div>
        </v-card>
      </v-col>
      <v-col cols="12" md="3">
        <v-card color="success" class="pa-4">
          <div class="d-flex align-center justify-space-between">
            <div>
              <div class="text-caption" style="color: rgba(255,255,255,0.7)">{{ i18n.t('dashboard.totalBookings') }}</div>
              <div class="text-h6 font-weight-bold text-white mt-1">{{ stats.totalBookings }}</div>
            </div>
            <v-icon color="rgba(255,255,255,0.5)" size="40">mdi-calendar-check</v-icon>
          </div>
        </v-card>
      </v-col>
      <v-col cols="12" md="3">
        <v-card color="warning" class="pa-4">
          <div class="d-flex align-center justify-space-between">
            <div>
              <div class="text-caption" style="color: rgba(255,255,255,0.7)">{{ i18n.t('dashboard.pending') }}</div>
              <div class="text-h6 font-weight-bold text-white mt-1">{{ stats.pendingBookings }}</div>
            </div>
            <v-icon color="rgba(255,255,255,0.5)" size="40">mdi-clock-outline</v-icon>
          </div>
        </v-card>
      </v-col>
      <v-col cols="12" md="3">
        <v-card color="error" class="pa-4">
          <div class="d-flex align-center justify-space-between">
            <div>
              <div class="text-caption" style="color: rgba(255,255,255,0.7)">{{ i18n.t('dashboard.unpaidInvoices') }}</div>
              <div class="text-h6 font-weight-bold text-white mt-1">{{ stats.unpaidInvoices }}</div>
            </div>
            <v-icon color="rgba(255,255,255,0.5)" size="40">mdi-receipt-text-outline</v-icon>
          </div>
        </v-card>
      </v-col>
    </v-row>

    <v-row class="mt-4">
      <v-col cols="12" md="8">
        <v-card>
          <v-card-title class="pa-4 text-primary">{{ i18n.t('dashboard.recentBookings') }}</v-card-title>
          <v-data-table
            :headers="bookingHeaders"
            :items="recentBookings"
            :loading="loading"
            density="compact"
            hide-default-footer
          >
            <template #item.status="{ item }">
              <v-chip :color="statusColor(item.status)" size="small" variant="tonal">
                {{ statusLabel(item.status) }}
              </v-chip>
            </template>
            <template #item.total_amount="{ item }">
              {{ Number(item.total_amount).toLocaleString() }} {{ i18n.t('common.currency') }}
            </template>
            <template #item.actions="{ item }">
              <v-btn size="small" icon="mdi-eye" variant="text" :to="`/client/bookings/${item.id}`" />
            </template>
          </v-data-table>
        </v-card>
      </v-col>
      <v-col cols="12" md="4">
        <v-card class="pa-4" height="100%">
          <v-card-title class="pa-0 mb-4 text-primary">{{ i18n.t('dashboard.quickActions') }}</v-card-title>
          <v-btn block color="primary" class="mb-3" to="/client/bookings/new" prepend-icon="mdi-calendar-plus">
            {{ i18n.t('dashboard.newBooking') }}
          </v-btn>
          <v-btn block variant="outlined" color="primary" class="mb-3" to="/client/invoices" prepend-icon="mdi-receipt">
            {{ i18n.t('dashboard.viewInvoices') }}
          </v-btn>
          <v-btn block variant="outlined" color="primary" to="/client/menu" prepend-icon="mdi-food">
            {{ i18n.t('dashboard.viewMenu') }}
          </v-btn>
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
const deposit = ref(null)
const recentBookings = ref([])
const stats = ref({ totalBookings: 0, pendingBookings: 0, unpaidInvoices: 0 })

const bookingHeaders = computed(() => [
  { title: i18n.t('bookings.bookingNum'), key: 'booking_number', width: '130px' },
  { title: i18n.t('bookings.eventDate'), key: 'event_date' },
  { title: i18n.t('bookings.guests'), key: 'guest_count' },
  { title: i18n.t('common.amount'), key: 'total_amount' },
  { title: i18n.t('common.status'), key: 'status' },
  { title: '', key: 'actions', sortable: false },
])

function statusColor(status) {
  return { pending: 'warning', approved: 'success', rejected: 'error', cancelled: 'grey', completed: 'info' }[status] || 'grey'
}

function statusLabel(status) {
  return i18n.t(`bookingStatus.${status}`) || status
}

onMounted(async () => {
  try {
    const [depositRes, bookingsRes, invoicesRes] = await Promise.all([
      api.get('/deposit'),
      api.get('/bookings?per_page=5'),
      api.get('/invoices?per_page=100'),
    ])
    deposit.value = depositRes.data.data
    recentBookings.value = bookingsRes.data.data.data || []
    const invoices = invoicesRes.data.data.data || []
    stats.value = {
      totalBookings: bookingsRes.data.data.total || 0,
      pendingBookings: recentBookings.value.filter(b => b.status === 'pending').length,
      unpaidInvoices: invoices.filter(i => i.status !== 'paid').length,
    }
  } finally {
    loading.value = false
  }
})
</script>
