<template>
  <div>
    <div class="d-flex align-center justify-space-between mb-6">
      <div class="text-h5 font-weight-bold text-primary">{{ i18n.t('bookings.myTitle') }}</div>
      <v-btn color="primary" to="/client/bookings/new" prepend-icon="mdi-calendar-plus">
        {{ i18n.t('bookings.newBooking') }}
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
          {{ Number(item.total_amount).toLocaleString() }} {{ i18n.t('common.currency') }}
        </template>
        <template #item.actions="{ item }">
          <v-btn size="small" icon="mdi-eye" variant="text" :to="`/client/bookings/${item.id}`" />
        </template>
      </v-data-table>
    </v-card>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useI18nStore } from '@/stores/i18n'
import api from '@/plugins/axios'

const i18n = useI18nStore()
const loading = ref(true)
const bookings = ref([])

const headers = computed(() => [
  { title: i18n.t('bookings.bookingNum'), key: 'booking_number' },
  { title: i18n.t('bookings.eventDate'), key: 'event_date' },
  { title: i18n.t('bookings.guests'), key: 'guest_count' },
  { title: i18n.t('bookings.total'), key: 'total_amount' },
  { title: i18n.t('common.status'), key: 'status' },
  { title: i18n.t('common.date'), key: 'created_at' },
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
    const res = await api.get('/bookings?per_page=50')
    bookings.value = res.data.data.data || []
  } finally {
    loading.value = false
  }
})
</script>
