<template>
  <div>
    <div class="d-flex align-center justify-space-between mb-6">
      <div class="text-h5 font-weight-bold text-primary">Bronlarim</div>
      <v-btn color="primary" to="/client/bookings/new" prepend-icon="mdi-calendar-plus">
        Yangi bron
      </v-btn>
    </div>

    <v-card>
      <v-data-table
        :headers="headers"
        :items="bookings"
        :loading="loading"
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
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/plugins/axios'

const loading = ref(true)
const bookings = ref([])

const headers = [
  { title: 'Bron #', key: 'booking_number' },
  { title: 'Tadbir sanasi', key: 'event_date' },
  { title: 'Mehmonlar', key: 'guest_count' },
  { title: 'Jami summa', key: 'total_amount' },
  { title: 'Status', key: 'status' },
  { title: 'Sana', key: 'created_at' },
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
    const res = await api.get('/bookings?per_page=50')
    bookings.value = res.data.data.data || []
  } finally {
    loading.value = false
  }
})
</script>
