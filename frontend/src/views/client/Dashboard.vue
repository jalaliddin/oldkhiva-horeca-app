<template>
  <div>
    <div class="text-h5 font-weight-bold text-primary mb-6">Bosh sahifa</div>

    <v-row>
      <v-col cols="12" md="3">
        <v-card color="primary" class="pa-4">
          <div class="d-flex align-center justify-space-between">
            <div>
              <div class="text-caption" style="color: rgba(255,255,255,0.7)">Balans</div>
              <div class="text-h6 font-weight-bold text-white mt-1">
                {{ Number(deposit?.balance || 0).toLocaleString() }} so'm
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
              <div class="text-caption" style="color: rgba(255,255,255,0.7)">Jami bronlar</div>
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
              <div class="text-caption" style="color: rgba(255,255,255,0.7)">Kutilmoqda</div>
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
              <div class="text-caption" style="color: rgba(255,255,255,0.7)">To'lanmagan</div>
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
          <v-card-title class="pa-4 text-primary">So'nggi bronlar</v-card-title>
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
              {{ Number(item.total_amount).toLocaleString() }} so'm
            </template>
            <template #item.actions="{ item }">
              <v-btn size="small" icon="mdi-eye" variant="text" :to="`/client/bookings/${item.id}`" />
            </template>
          </v-data-table>
        </v-card>
      </v-col>
      <v-col cols="12" md="4">
        <v-card class="pa-4" height="100%">
          <v-card-title class="pa-0 mb-4 text-primary">Tezkor harakatlar</v-card-title>
          <v-btn block color="primary" class="mb-3" to="/client/bookings/new" prepend-icon="mdi-calendar-plus">
            Yangi bron
          </v-btn>
          <v-btn block variant="outlined" color="primary" class="mb-3" to="/client/invoices" prepend-icon="mdi-receipt">
            Invoicelar
          </v-btn>
          <v-btn block variant="outlined" color="primary" to="/client/menu" prepend-icon="mdi-food">
            Menyu ko'rish
          </v-btn>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/plugins/axios'

const loading = ref(true)
const deposit = ref(null)
const recentBookings = ref([])
const stats = ref({ totalBookings: 0, pendingBookings: 0, unpaidInvoices: 0 })

const bookingHeaders = [
  { title: 'Bron #', key: 'booking_number', width: '130px' },
  { title: 'Sana', key: 'event_date' },
  { title: 'Mehmonlar', key: 'guest_count' },
  { title: 'Summa', key: 'total_amount' },
  { title: 'Status', key: 'status' },
  { title: '', key: 'actions', sortable: false },
]

function statusColor(status) {
  const colors = { pending: 'warning', approved: 'success', rejected: 'error', cancelled: 'grey', completed: 'info' }
  return colors[status] || 'grey'
}

function statusLabel(status) {
  const labels = { pending: 'Kutilmoqda', approved: 'Tasdiqlangan', rejected: 'Rad etilgan', cancelled: 'Bekor qilindi', completed: 'Tugallandi' }
  return labels[status] || status
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
