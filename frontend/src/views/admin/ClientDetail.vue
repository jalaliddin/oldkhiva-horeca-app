<template>
  <div>
    <div class="d-flex align-center mb-6">
      <v-btn icon="mdi-arrow-left" variant="text" to="/admin/clients" class="mr-3" />
      <div class="text-h5 font-weight-bold text-primary">Mijoz tafsiloti</div>
    </div>

    <v-row v-if="client">
      <v-col cols="12" md="6">
        <v-card class="mb-4">
          <v-card-title class="pa-4">
            {{ client.company_name || client.name }}
            <v-chip :color="client.is_active ? 'success' : 'warning'" size="small" variant="tonal" class="ml-3">
              {{ client.is_active ? 'Tasdiqlangan' : 'Kutilmoqda' }}
            </v-chip>
          </v-card-title>
          <v-card-text>
            <v-list density="compact">
              <v-list-item title="Ism" :subtitle="client.name" />
              <v-list-item title="Email" :subtitle="client.email" />
              <v-list-item title="Telefon" :subtitle="client.phone" />
              <v-list-item title="Direktor" :subtitle="client.director_name" />
              <v-list-item title="INN" :subtitle="client.inn" />
              <v-list-item title="Bank" :subtitle="client.bank_name" />
              <v-list-item title="MFO" :subtitle="client.mfo" />
              <v-list-item title="Hisob raqam" :subtitle="client.bank_account" />
              <v-list-item title="Manzil" :subtitle="client.address" />
            </v-list>
          </v-card-text>
          <v-card-actions class="pa-4">
            <v-btn v-if="!client.is_active" color="success" :loading="approving" @click="approveClient">
              <v-icon start>mdi-check</v-icon>
              Tasdiqlash
            </v-btn>
            <v-btn v-if="client.is_active" color="error" variant="outlined" :loading="rejecting" @click="rejectClient">
              <v-icon start>mdi-close</v-icon>
              Bloklash
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>

      <v-col cols="12" md="6">
        <v-card class="mb-4">
          <v-card-title class="pa-4 text-primary">Moliyaviy holat</v-card-title>
          <v-card-text>
            <div class="text-h5 font-weight-bold text-primary">
              {{ Number(client.deposit?.balance || 0).toLocaleString() }} so'm
            </div>
            <div class="text-caption text-medium-emphasis">Depozit balansi</div>
          </v-card-text>
        </v-card>

        <v-card>
          <v-card-title class="pa-4 text-primary">Bronlar ({{ client.bookings?.length || 0 }})</v-card-title>
          <v-list density="compact">
            <v-list-item
              v-for="booking in (client.bookings || []).slice(0, 5)"
              :key="booking.id"
              :title="booking.booking_number"
              :subtitle="`${booking.event_date} — ${Number(booking.total_amount).toLocaleString()} so'm`"
              :to="`/admin/bookings/${booking.id}`"
            >
              <template #append>
                <v-chip :color="statusColor(booking.status)" size="x-small" variant="tonal">
                  {{ statusLabel(booking.status) }}
                </v-chip>
              </template>
            </v-list-item>
          </v-list>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useNotificationStore } from '@/stores/notification'
import api from '@/plugins/axios'

const route = useRoute()
const notification = useNotificationStore()
const client = ref(null)
const approving = ref(false)
const rejecting = ref(false)

function statusColor(s) {
  return { pending: 'warning', approved: 'success', rejected: 'error', cancelled: 'grey', completed: 'info' }[s] || 'grey'
}
function statusLabel(s) {
  return { pending: 'Kutilmoqda', approved: 'Tasdiqlangan', rejected: 'Rad etilgan', cancelled: 'Bekor qilindi', completed: 'Tugallandi' }[s] || s
}

async function approveClient() {
  approving.value = true
  try {
    await api.post(`/admin/clients/${route.params.id}/approve`)
    notification.showSuccess('Mijoz tasdiqlandi!')
    await fetchClient()
  } catch {
    notification.showError('Xato yuz berdi')
  } finally {
    approving.value = false
  }
}

async function rejectClient() {
  rejecting.value = true
  try {
    await api.post(`/admin/clients/${route.params.id}/reject`)
    notification.showSuccess("Mijoz bloklandi!")
    await fetchClient()
  } catch {
    notification.showError('Xato yuz berdi')
  } finally {
    rejecting.value = false
  }
}

async function fetchClient() {
  const res = await api.get(`/admin/clients/${route.params.id}`)
  client.value = res.data.data
}

onMounted(fetchClient)
</script>
