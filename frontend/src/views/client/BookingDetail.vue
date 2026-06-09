<template>
  <div>
    <div class="d-flex align-center mb-6">
      <v-btn icon="mdi-arrow-left" variant="text" to="/client/bookings" class="mr-3" />
      <div class="text-h5 font-weight-bold text-primary">{{ i18n.t('bookingDetail.title') }}</div>
    </div>

    <v-row v-if="booking">
      <v-col cols="12" md="8">
        <v-card class="mb-4">
          <v-card-title class="pa-4">
            {{ booking.booking_number }}
            <v-chip :color="statusColor(booking.status)" size="small" variant="tonal" class="ml-3">
              {{ statusLabel(booking.status) }}
            </v-chip>
          </v-card-title>
          <v-card-text>
            <v-row>
              <v-col cols="6">
                <div class="text-caption text-medium-emphasis">{{ i18n.t('bookingDetail.eventDate') }}</div>
                <div class="font-weight-medium">{{ booking.event_date }}</div>
              </v-col>
              <v-col cols="6">
                <div class="text-caption text-medium-emphasis">{{ i18n.t('bookingDetail.guestCount') }}</div>
                <div class="font-weight-medium">{{ booking.guest_count }} {{ i18n.t('bookingDetail.guestSuffix') }}</div>
              </v-col>
              <v-col v-if="booking.notes" cols="12">
                <div class="text-caption text-medium-emphasis">{{ i18n.t('bookingDetail.notes') }}</div>
                <div class="text-body-2">{{ booking.notes }}</div>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>

        <v-card>
          <v-card-title class="pa-4">{{ i18n.t('bookingDetail.orderItems') }}</v-card-title>
          <v-data-table
            :headers="itemHeaders"
            :items="booking.items || []"
            density="compact"
            hide-default-footer
          >
            <template #item.item_price="{ item }">
              {{ Number(item.item_price).toLocaleString() }} {{ i18n.t('common.currency') }}
            </template>
            <template #item.subtotal="{ item }">
              {{ Number(item.subtotal).toLocaleString() }} {{ i18n.t('common.currency') }}
            </template>
          </v-data-table>
          <v-divider />
          <div class="pa-4 text-right">
            <span class="text-h6 font-weight-bold text-primary">
              {{ i18n.t('common.total') }}: {{ Number(booking.total_amount).toLocaleString() }} {{ i18n.t('common.currency') }}
            </span>
          </div>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card v-if="booking.invoice" class="mb-4">
          <v-card-title class="pa-4 text-primary">Invoice</v-card-title>
          <v-card-text>
            <div class="mb-2"><strong>{{ booking.invoice.invoice_number }}</strong></div>
            <v-chip :color="invoiceStatusColor(booking.invoice.status)" size="small" variant="tonal" class="mb-3">
              {{ invoiceStatusLabel(booking.invoice.status) }}
            </v-chip>
            <div class="text-body-2 mb-1">{{ i18n.t('common.total') }}: {{ Number(booking.invoice.total_amount).toLocaleString() }} {{ i18n.t('common.currency') }}</div>
            <div class="text-body-2 mb-3">{{ i18n.t('common.balance') }}: {{ Number(booking.invoice.balance).toLocaleString() }} {{ i18n.t('common.currency') }}</div>
            <v-btn block color="primary" size="small" :to="`/client/invoices/${booking.invoice.id}`">
              {{ i18n.t('common.view') }}
            </v-btn>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useI18nStore } from '@/stores/i18n'
import api from '@/plugins/axios'

const route = useRoute()
const i18n = useI18nStore()
const booking = ref(null)

const itemHeaders = computed(() => [
  { title: i18n.t('common.name'), key: 'item_name' },
  { title: i18n.t('common.price'), key: 'item_price' },
  { title: i18n.t('common.quantity'), key: 'quantity' },
  { title: i18n.t('common.total'), key: 'subtotal' },
])

function statusColor(s) {
  return { pending: 'warning', approved: 'success', rejected: 'error', cancelled: 'grey', completed: 'info' }[s] || 'grey'
}
function statusLabel(s) { return i18n.t(`bookingStatus.${s}`) || s }
function invoiceStatusColor(s) {
  return { unpaid: 'warning', partial: 'info', paid: 'success', overdue: 'error' }[s] || 'grey'
}
function invoiceStatusLabel(s) { return i18n.t(`invoiceStatus.${s}`) || s }

onMounted(async () => {
  const res = await api.get(`/bookings/${route.params.id}`)
  booking.value = res.data.data
})
</script>
