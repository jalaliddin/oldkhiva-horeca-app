<template>
  <div>
    <div class="d-flex align-center mb-6">
      <v-btn icon="mdi-arrow-left" variant="text" to="/client/bookings" class="mr-3" />
      <div class="text-h5 font-weight-bold text-primary">Bron tafsiloti</div>
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
                <div class="text-caption text-medium-emphasis">Tadbir sanasi</div>
                <div class="font-weight-medium">{{ booking.event_date }}</div>
              </v-col>
              <v-col cols="6">
                <div class="text-caption text-medium-emphasis">Mehmonlar soni</div>
                <div class="font-weight-medium">{{ booking.guest_count }} kishi</div>
              </v-col>
              <v-col v-if="booking.notes" cols="12">
                <div class="text-caption text-medium-emphasis">Izoh</div>
                <div class="text-body-2">{{ booking.notes }}</div>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>

        <v-card>
          <v-card-title class="pa-4">Buyurtma tarkibi</v-card-title>
          <v-data-table
            :headers="itemHeaders"
            :items="booking.items || []"
            density="compact"
            hide-default-footer
          >
            <template #item.item_price="{ item }">
              {{ Number(item.item_price).toLocaleString() }} so'm
            </template>
            <template #item.subtotal="{ item }">
              {{ Number(item.subtotal).toLocaleString() }} so'm
            </template>
          </v-data-table>
          <v-divider />
          <div class="pa-4 text-right">
            <span class="text-h6 font-weight-bold text-primary">
              Jami: {{ Number(booking.total_amount).toLocaleString() }} so'm
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
            <div class="text-body-2 mb-1">Jami: {{ Number(booking.invoice.total_amount).toLocaleString() }} so'm</div>
            <div class="text-body-2 mb-3">Qoldiq: {{ Number(booking.invoice.balance).toLocaleString() }} so'm</div>
            <v-btn
              block
              color="primary"
              size="small"
              :to="`/client/invoices/${booking.invoice.id}`"
            >
              Ko'rish
            </v-btn>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/plugins/axios'

const route = useRoute()
const booking = ref(null)

const itemHeaders = [
  { title: 'Nomi', key: 'item_name' },
  { title: 'Narxi', key: 'item_price' },
  { title: 'Miqdor', key: 'quantity' },
  { title: 'Jami', key: 'subtotal' },
]

function statusColor(s) {
  return { pending: 'warning', approved: 'success', rejected: 'error', cancelled: 'grey', completed: 'info' }[s] || 'grey'
}
function statusLabel(s) {
  return { pending: 'Kutilmoqda', approved: 'Tasdiqlangan', rejected: 'Rad etilgan', cancelled: 'Bekor qilindi', completed: 'Tugallandi' }[s] || s
}
function invoiceStatusColor(s) {
  return { unpaid: 'warning', partial: 'info', paid: 'success', overdue: 'error' }[s] || 'grey'
}
function invoiceStatusLabel(s) {
  return { unpaid: "To'lanmagan", partial: 'Qisman', paid: "To'langan", overdue: 'Muddati o\'tgan' }[s] || s
}

onMounted(async () => {
  const res = await api.get(`/bookings/${route.params.id}`)
  booking.value = res.data.data
})
</script>
