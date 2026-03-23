<template>
  <div>
    <div class="d-flex align-center mb-6">
      <v-btn icon="mdi-arrow-left" variant="text" to="/admin/bookings" class="mr-3" />
      <div class="text-h5 font-weight-bold text-primary">Bron tafsiloti</div>
    </div>

    <v-row v-if="booking">
      <v-col cols="12" md="8">
        <v-card class="mb-4">
          <v-card-title class="pa-4">
            {{ booking.booking_number }}
            <v-chip :color="statusColor(booking.status)" size="small" variant="tonal" class="ml-3">{{ statusLabel(booking.status) }}</v-chip>
          </v-card-title>
          <v-card-text>
            <v-row>
              <v-col cols="6"><div class="text-caption">Mijoz</div><div class="font-weight-medium">{{ booking.client?.company_name }}</div></v-col>
              <v-col cols="6"><div class="text-caption">Tadbir sanasi</div><div class="font-weight-medium">{{ booking.event_date }}</div></v-col>
              <v-col cols="6"><div class="text-caption">Mehmonlar</div><div class="font-weight-medium">{{ booking.guest_count }} kishi</div></v-col>
              <v-col cols="6"><div class="text-caption">Jami summa</div><div class="font-weight-medium text-primary">{{ Number(booking.total_amount).toLocaleString() }} so'm</div></v-col>
              <v-col v-if="booking.notes" cols="12"><div class="text-caption">Izoh</div><div class="text-body-2">{{ booking.notes }}</div></v-col>
            </v-row>
          </v-card-text>
        </v-card>

        <v-card class="mb-4">
          <v-card-title class="pa-4">Buyurtma tarkibi</v-card-title>
          <v-data-table :headers="itemHeaders" :items="booking.items || []" density="compact" hide-default-footer>
            <template #item.item_price="{ item }">{{ Number(item.item_price).toLocaleString() }} so'm</template>
            <template #item.subtotal="{ item }">{{ Number(item.subtotal).toLocaleString() }} so'm</template>
          </v-data-table>
        </v-card>

        <!-- Admin actions -->
        <v-card v-if="booking.status === 'pending'">
          <v-card-title class="pa-4 text-primary">Admin amali</v-card-title>
          <v-card-text>
            <v-textarea v-model="adminNotes" label="Admin izohi" rows="3" class="mb-4" />
            <v-row>
              <v-col>
                <v-btn color="success" block :loading="approving" @click="approveBooking">
                  <v-icon start>mdi-check</v-icon>
                  Tasdiqlash
                </v-btn>
              </v-col>
              <v-col>
                <v-btn color="error" variant="outlined" block :loading="rejecting" @click="rejectBooking">
                  <v-icon start>mdi-close</v-icon>
                  Rad etish
                </v-btn>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card v-if="booking.invoice" class="mb-4">
          <v-card-title class="pa-4 text-primary">Invoice</v-card-title>
          <v-card-text>
            <div class="font-weight-bold mb-2">{{ booking.invoice.invoice_number }}</div>
            <v-chip :color="invoiceStatusColor(booking.invoice.status)" size="small" variant="tonal" class="mb-3">
              {{ invoiceStatusLabel(booking.invoice.status) }}
            </v-chip>
            <div class="text-body-2 mb-1">Jami: {{ Number(booking.invoice.total_amount).toLocaleString() }} so'm</div>
            <div class="text-body-2 mb-3">Qoldiq: {{ Number(booking.invoice.balance).toLocaleString() }} so'm</div>
            <v-row dense>
              <v-col>
                <v-btn block size="small" color="primary" @click="openInvoiceDetail">Ko'rish</v-btn>
              </v-col>
              <v-col>
                <v-btn block size="small" color="secondary" :loading="downloadingInvoice" @click="downloadInvoice">PDF</v-btn>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Invoice detail dialog -->
    <v-dialog v-model="invoiceDialog" max-width="700">
      <v-card v-if="selectedInvoice">
        <v-card-title class="pa-4 d-flex align-center justify-space-between">
          <span>{{ selectedInvoice.invoice_number }}</span>
          <v-chip :color="invoiceStatusColor(selectedInvoice.status)" size="small" variant="tonal">
            {{ invoiceStatusLabel(selectedInvoice.status) }}
          </v-chip>
        </v-card-title>
        <v-card-text>
          <v-row class="mb-4">
            <v-col cols="6">
              <div class="text-caption text-medium-emphasis">Mijoz</div>
              <div class="font-weight-medium">{{ selectedInvoice.client?.company_name }}</div>
            </v-col>
            <v-col cols="6">
              <div class="text-caption text-medium-emphasis">To'lov muddati</div>
              <div>{{ selectedInvoice.due_date }}</div>
            </v-col>
          </v-row>
          <v-data-table
            v-if="selectedInvoice.booking?.items?.length"
            :headers="itemHeaders"
            :items="selectedInvoice.booking.items"
            density="compact"
            hide-default-footer
            class="mb-4"
          >
            <template #item.item_price="{ item }">{{ Number(item.item_price).toLocaleString() }} so'm</template>
            <template #item.subtotal="{ item }">{{ Number(item.subtotal).toLocaleString() }} so'm</template>
          </v-data-table>
          <v-divider class="mb-3" />
          <div class="d-flex justify-space-between mb-1">
            <span>Jami:</span>
            <span class="font-weight-bold">{{ Number(selectedInvoice.total_amount).toLocaleString() }} so'm</span>
          </div>
          <div class="d-flex justify-space-between mb-1 text-success">
            <span>To'langan:</span>
            <span>{{ Number(selectedInvoice.paid_amount).toLocaleString() }} so'm</span>
          </div>
          <div class="d-flex justify-space-between font-weight-bold text-error">
            <span>Qoldiq:</span>
            <span>{{ Number(selectedInvoice.balance).toLocaleString() }} so'm</span>
          </div>
        </v-card-text>
        <v-card-actions class="pa-4">
          <v-btn color="primary" prepend-icon="mdi-download" :loading="downloadingInvoice" @click="downloadInvoice">PDF yuklab olish</v-btn>
          <v-spacer />
          <v-btn @click="invoiceDialog = false">Yopish</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Confirm dialogs -->
    <v-dialog v-model="confirmApprove" max-width="400">
      <v-card>
        <v-card-title>Bronni tasdiqlash</v-card-title>
        <v-card-text>Bu bronni tasdiqlaysizmi? Invoice avtomatik yaratiladi.</v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn @click="confirmApprove = false">Bekor</v-btn>
          <v-btn color="success" :loading="approving" @click="doApprove">Tasdiqlash</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useNotificationStore } from '@/stores/notification'
import api from '@/plugins/axios'

const route = useRoute()
const notification = useNotificationStore()
const booking = ref(null)
const adminNotes = ref('')
const approving = ref(false)
const rejecting = ref(false)
const confirmApprove = ref(false)
const invoiceDialog = ref(false)
const selectedInvoice = ref(null)
const downloadingInvoice = ref(false)

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
  return { unpaid: "To'lanmagan", partial: 'Qisman', paid: "To'langan", overdue: "Muddati o'tgan" }[s] || s
}

function approveBooking() {
  confirmApprove.value = true
}

async function doApprove() {
  approving.value = true
  confirmApprove.value = false
  try {
    await api.post(`/admin/bookings/${route.params.id}/approve`, { admin_notes: adminNotes.value })
    notification.showSuccess('Bron tasdiqlandi va invoice yaratildi!')
    await fetchBooking()
  } catch {
    notification.showError('Xato yuz berdi')
  } finally {
    approving.value = false
  }
}

async function rejectBooking() {
  rejecting.value = true
  try {
    await api.post(`/admin/bookings/${route.params.id}/reject`, { admin_notes: adminNotes.value })
    notification.showSuccess('Bron rad etildi.')
    await fetchBooking()
  } catch {
    notification.showError('Xato yuz berdi')
  } finally {
    rejecting.value = false
  }
}

async function openInvoiceDetail() {
  const res = await api.get(`/admin/invoices/${booking.value.invoice.id}`)
  selectedInvoice.value = res.data.data
  invoiceDialog.value = true
}

async function downloadInvoice() {
  downloadingInvoice.value = true
  try {
    const inv = selectedInvoice.value || booking.value.invoice
    const res = await api.get(`/admin/invoices/${inv.id}/download`, { responseType: 'blob' })
    const url = URL.createObjectURL(new Blob([res.data], { type: 'application/pdf' }))
    const link = document.createElement('a')
    link.href = url
    link.download = `Invoice-${inv.invoice_number}.pdf`
    link.click()
    URL.revokeObjectURL(url)
  } catch {
    notification.showError('Fayl topilmadi')
  } finally {
    downloadingInvoice.value = false
  }
}

async function fetchBooking() {
  const res = await api.get(`/admin/bookings/${route.params.id}`)
  booking.value = res.data.data
}

onMounted(fetchBooking)
</script>
