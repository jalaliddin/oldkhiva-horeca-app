<template>
  <div>
    <div class="text-h5 font-weight-bold text-primary mb-6">Bronlar</div>

    <v-card class="mb-4">
      <v-card-text class="pb-0">
        <v-row>
          <v-col cols="12" md="4">
            <v-select v-model="statusFilter" label="Status" :items="statusOptions" hide-details density="compact" clearable @update:modelValue="fetchBookings" />
          </v-col>
          <v-col cols="12" md="3">
            <v-text-field v-model="dateFrom" label="Sanadan" type="date" hide-details density="compact" @change="fetchBookings" />
          </v-col>
          <v-col cols="12" md="3">
            <v-text-field v-model="dateTo" label="Sanagacha" type="date" hide-details density="compact" @change="fetchBookings" />
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <v-card>
      <v-data-table :headers="headers" :items="bookings" :loading="loading">
        <template #item.client="{ item }">
          {{ item.client?.company_name || item.client?.name }}
        </template>
        <template #item.status="{ item }">
          <v-chip :color="statusColor(item.status)" size="small" variant="tonal">
            {{ statusLabel(item.status) }}
          </v-chip>
        </template>
        <template #item.total_amount="{ item }">
          {{ Number(item.total_amount).toLocaleString() }} so'm
        </template>
        <template #item.actions="{ item }">
          <v-btn size="small" icon="mdi-eye" variant="text" :to="`/admin/bookings/${item.id}`" />
        </template>
      </v-data-table>
    </v-card>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/plugins/axios'

const loading = ref(true)
const bookings = ref([])
const statusFilter = ref(null)
const dateFrom = ref('')
const dateTo = ref('')

const statusOptions = [
  { title: 'Barcha', value: null },
  { title: 'Kutilmoqda', value: 'pending' },
  { title: 'Tasdiqlangan', value: 'approved' },
  { title: 'Rad etilgan', value: 'rejected' },
  { title: 'Tugallandi', value: 'completed' },
]

const headers = [
  { title: 'Bron #', key: 'booking_number' },
  { title: 'Mijoz', key: 'client' },
  { title: 'Tadbir sanasi', key: 'event_date' },
  { title: 'Mehmonlar', key: 'guest_count' },
  { title: 'Jami', key: 'total_amount' },
  { title: 'Status', key: 'status' },
  { title: '', key: 'actions', sortable: false },
]

function statusColor(s) {
  return { pending: 'warning', approved: 'success', rejected: 'error', cancelled: 'grey', completed: 'info' }[s] || 'grey'
}
function statusLabel(s) {
  return { pending: 'Kutilmoqda', approved: 'Tasdiqlangan', rejected: 'Rad etilgan', cancelled: 'Bekor qilindi', completed: 'Tugallandi' }[s] || s
}

async function fetchBookings() {
  loading.value = true
  try {
    const params = {}
    if (statusFilter.value) params.status = statusFilter.value
    if (dateFrom.value) params.date_from = dateFrom.value
    if (dateTo.value) params.date_to = dateTo.value
    const res = await api.get('/admin/bookings', { params })
    bookings.value = res.data.data.data || []
  } finally {
    loading.value = false
  }
}

onMounted(fetchBookings)
</script>
