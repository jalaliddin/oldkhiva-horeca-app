<template>
  <div>
    <div class="text-h5 font-weight-bold text-primary mb-6">{{ i18n.t('bookings.title') }}</div>

    <v-card class="mb-4">
      <v-card-text class="pb-0">
        <v-row>
          <v-col cols="12" md="4">
            <v-select v-model="statusFilter" :label="i18n.t('common.status')" :items="statusOptions" hide-details density="compact" clearable @update:modelValue="fetchBookings" />
          </v-col>
          <v-col cols="12" md="3">
            <v-text-field v-model="dateFrom" :label="i18n.t('bookings.dateFrom')" type="date" hide-details density="compact" @change="fetchBookings" />
          </v-col>
          <v-col cols="12" md="3">
            <v-text-field v-model="dateTo" :label="i18n.t('bookings.dateTo')" type="date" hide-details density="compact" @change="fetchBookings" />
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
          {{ Number(item.total_amount).toLocaleString() }} {{ i18n.t('common.currency') }}
        </template>
        <template #item.actions="{ item }">
          <v-btn size="small" icon="mdi-eye" variant="text" :to="`/admin/bookings/${item.id}`" />
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
const statusFilter = ref(null)
const dateFrom = ref('')
const dateTo = ref('')

const statusOptions = computed(() => [
  { title: i18n.t('common.all'), value: null },
  { title: i18n.t('bookingStatus.pending'), value: 'pending' },
  { title: i18n.t('bookingStatus.approved'), value: 'approved' },
  { title: i18n.t('bookingStatus.rejected'), value: 'rejected' },
  { title: i18n.t('bookingStatus.completed'), value: 'completed' },
])

const headers = computed(() => [
  { title: i18n.t('bookings.bookingNum'), key: 'booking_number' },
  { title: i18n.t('bookings.client'), key: 'client' },
  { title: i18n.t('bookings.eventDate'), key: 'event_date' },
  { title: i18n.t('bookings.guests'), key: 'guest_count' },
  { title: i18n.t('common.total'), key: 'total_amount' },
  { title: i18n.t('common.status'), key: 'status' },
  { title: '', key: 'actions', sortable: false },
])

function statusColor(s) {
  return { pending: 'warning', approved: 'success', rejected: 'error', cancelled: 'grey', completed: 'info' }[s] || 'grey'
}
function statusLabel(s) { return i18n.t(`bookingStatus.${s}`) || s }

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
