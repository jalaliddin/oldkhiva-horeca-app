<template>
  <div>
    <div class="d-flex align-center mb-6">
      <v-btn icon="mdi-arrow-left" variant="text" to="/admin/clients" class="mr-3" />
      <div class="text-h5 font-weight-bold text-primary">{{ i18n.t('clients.detail') }}</div>
    </div>

    <v-row v-if="client">
      <v-col cols="12" md="6">
        <v-card class="mb-4">
          <v-card-title class="pa-4">
            {{ client.company_name || client.name }}
            <v-chip :color="client.is_active ? 'success' : 'warning'" size="small" variant="tonal" class="ml-3">
              {{ client.is_active ? i18n.t('bookingStatus.approved') : i18n.t('bookingStatus.pending') }}
            </v-chip>
          </v-card-title>
          <v-card-text>
            <v-list density="compact">
              <v-list-item :title="i18n.t('clients.name')" :subtitle="client.name" />
              <v-list-item title="Email" :subtitle="client.email" />
              <v-list-item :title="i18n.t('clients.phone')" :subtitle="client.phone" />
              <v-list-item :title="i18n.t('clients.director')" :subtitle="client.director_name" />
              <v-list-item :title="i18n.t('clients.inn')" :subtitle="client.inn" />
              <v-list-item :title="i18n.t('clients.bank')" :subtitle="client.bank_name" />
              <v-list-item title="MFO" :subtitle="client.mfo" />
              <v-list-item :title="i18n.t('clients.accountNum')" :subtitle="client.bank_account" />
              <v-list-item :title="i18n.t('clients.address')" :subtitle="client.address" />
            </v-list>
          </v-card-text>
          <v-card-actions class="pa-4">
            <v-btn v-if="!client.is_active" color="success" :loading="approving" @click="approveClient">
              <v-icon start>mdi-check</v-icon>
              {{ i18n.t('clients.approve') }}
            </v-btn>
            <v-btn v-if="client.is_active" color="error" variant="outlined" :loading="rejecting" @click="rejectClient">
              <v-icon start>mdi-close</v-icon>
              {{ i18n.t('clients.block') }}
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>

      <v-col cols="12" md="6">
        <v-card class="mb-4">
          <v-card-title class="pa-4 text-primary">{{ i18n.t('clients.financialStatus') }}</v-card-title>
          <v-card-text>
            <div class="text-h5 font-weight-bold text-primary">
              {{ Number(client.deposit?.balance || 0).toLocaleString() }} {{ i18n.t('common.currency') }}
            </div>
            <div class="text-caption text-medium-emphasis">{{ i18n.t('clients.depositBalance') }}</div>
          </v-card-text>
        </v-card>

        <v-card>
          <v-card-title class="pa-4 text-primary">{{ i18n.t('clients.bookings') }} ({{ client.bookings?.length || 0 }})</v-card-title>
          <v-list density="compact">
            <v-list-item
              v-for="booking in (client.bookings || []).slice(0, 5)"
              :key="booking.id"
              :title="booking.booking_number"
              :subtitle="`${booking.event_date} — ${Number(booking.total_amount).toLocaleString()} ${i18n.t('common.currency')}`"
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
import { useI18nStore } from '@/stores/i18n'
import api from '@/plugins/axios'

const route = useRoute()
const notification = useNotificationStore()
const i18n = useI18nStore()
const client = ref(null)
const approving = ref(false)
const rejecting = ref(false)

function statusColor(s) {
  return { pending: 'warning', approved: 'success', rejected: 'error', cancelled: 'grey', completed: 'info' }[s] || 'grey'
}
function statusLabel(s) { return i18n.t(`bookingStatus.${s}`) || s }

async function approveClient() {
  approving.value = true
  try {
    await api.post(`/admin/clients/${route.params.id}/approve`)
    notification.showSuccess(i18n.t('clients.successApprove'))
    await fetchClient()
  } catch {
    notification.showError(i18n.t('register.error'))
  } finally {
    approving.value = false
  }
}

async function rejectClient() {
  rejecting.value = true
  try {
    await api.post(`/admin/clients/${route.params.id}/reject`)
    notification.showSuccess(i18n.t('clients.successBlock'))
    await fetchClient()
  } catch {
    notification.showError(i18n.t('register.error'))
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
