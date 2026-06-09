<template>
  <div>
    <div class="d-flex align-center mb-6">
      <v-btn icon="mdi-arrow-left" variant="text" to="/admin/bookings" class="mr-3" />
      <div class="text-h5 font-weight-bold text-primary">{{ i18n.t('bookingDetail.title') }}</div>
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
              <v-col cols="6"><div class="text-caption">{{ i18n.t('bookings.client') }}</div><div class="font-weight-medium">{{ booking.client?.company_name }}</div></v-col>
              <v-col cols="6"><div class="text-caption">{{ i18n.t('bookingDetail.eventDate') }}</div><div class="font-weight-medium">{{ booking.event_date }}</div></v-col>
              <v-col cols="6"><div class="text-caption">{{ i18n.t('bookings.guests') }}</div><div class="font-weight-medium">{{ booking.guest_count }} {{ i18n.t('bookingDetail.guestSuffix') }}</div></v-col>
              <v-col cols="6"><div class="text-caption">{{ i18n.t('common.total') }}</div><div class="font-weight-medium text-primary">{{ Number(booking.total_amount).toLocaleString() }} {{ i18n.t('common.currency') }}</div></v-col>
              <v-col v-if="booking.notes" cols="12"><div class="text-caption">{{ i18n.t('bookingDetail.notes') }}</div><div class="text-body-2">{{ booking.notes }}</div></v-col>
            </v-row>
          </v-card-text>
        </v-card>

        <v-card class="mb-4">
          <v-card-title class="pa-4">{{ i18n.t('bookingDetail.orderItems') }}</v-card-title>
          <v-data-table :headers="itemHeaders" :items="booking.items || []" density="compact" hide-default-footer>
            <template #item.item_price="{ item }">{{ Number(item.item_price).toLocaleString() }} {{ i18n.t('common.currency') }}</template>
            <template #item.subtotal="{ item }">{{ Number(item.subtotal).toLocaleString() }} {{ i18n.t('common.currency') }}</template>
          </v-data-table>
        </v-card>

        <!-- Admin actions -->
        <v-card v-if="booking.status === 'pending'">
          <v-card-title class="pa-4 text-primary">{{ i18n.t('bookingDetail.adminAction') }}</v-card-title>
          <v-card-text>
            <v-textarea v-model="adminNotes" :label="i18n.t('bookingDetail.adminNotes')" rows="3" class="mb-4" />
            <v-row>
              <v-col>
                <v-btn color="success" block :loading="approving" @click="approveBooking">
                  <v-icon start>mdi-check</v-icon>
                  {{ i18n.t('bookingDetail.approve') }}
                </v-btn>
              </v-col>
              <v-col>
                <v-btn color="error" variant="outlined" block :loading="rejecting" @click="rejectBooking">
                  <v-icon start>mdi-close</v-icon>
                  {{ i18n.t('bookingDetail.reject') }}
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
            <div class="text-body-2 mb-1">{{ i18n.t('common.total') }}: {{ Number(booking.invoice.total_amount).toLocaleString() }} {{ i18n.t('common.currency') }}</div>
            <div class="text-body-2 mb-3">{{ i18n.t('common.balance') }}: {{ Number(booking.invoice.balance).toLocaleString() }} {{ i18n.t('common.currency') }}</div>
            <v-row dense>
              <v-col>
                <v-btn block size="small" color="primary" @click="openInvoiceDetail">{{ i18n.t('common.view') }}</v-btn>
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
              <div class="text-caption text-medium-emphasis">{{ i18n.t('bookings.client') }}</div>
              <div class="font-weight-medium">{{ selectedInvoice.client?.company_name }}</div>
            </v-col>
            <v-col cols="6">
              <div class="text-caption text-medium-emphasis">{{ i18n.t('invoices.payDue') }}</div>
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
            <template #item.item_price="{ item }">{{ Number(item.item_price).toLocaleString() }} {{ i18n.t('common.currency') }}</template>
            <template #item.subtotal="{ item }">{{ Number(item.subtotal).toLocaleString() }} {{ i18n.t('common.currency') }}</template>
          </v-data-table>
          <v-divider class="mb-3" />
          <div class="d-flex justify-space-between mb-1">
            <span>{{ i18n.t('common.total') }}:</span>
            <span class="font-weight-bold">{{ Number(selectedInvoice.total_amount).toLocaleString() }} {{ i18n.t('common.currency') }}</span>
          </div>
          <div class="d-flex justify-space-between mb-1 text-success">
            <span>{{ i18n.t('common.paid') }}:</span>
            <span>{{ Number(selectedInvoice.paid_amount).toLocaleString() }} {{ i18n.t('common.currency') }}</span>
          </div>
          <div class="d-flex justify-space-between font-weight-bold text-error">
            <span>{{ i18n.t('common.balance') }}:</span>
            <span>{{ Number(selectedInvoice.balance).toLocaleString() }} {{ i18n.t('common.currency') }}</span>
          </div>
        </v-card-text>
        <v-card-actions class="pa-4">
          <v-btn color="primary" prepend-icon="mdi-download" :loading="downloadingInvoice" @click="downloadInvoice">{{ i18n.t('invoices.downloadPdf') }}</v-btn>
          <v-spacer />
          <v-btn @click="invoiceDialog = false">{{ i18n.t('common.close') }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Confirm dialogs -->
    <v-dialog v-model="confirmApprove" max-width="400">
      <v-card>
        <v-card-title>{{ i18n.t('bookingDetail.confirmApproveTitle') }}</v-card-title>
        <v-card-text>{{ i18n.t('bookingDetail.confirmApproveText') }}</v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn @click="confirmApprove = false">{{ i18n.t('common.cancel') }}</v-btn>
          <v-btn color="success" :loading="approving" @click="doApprove">{{ i18n.t('bookingDetail.approve') }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useNotificationStore } from '@/stores/notification'
import { useI18nStore } from '@/stores/i18n'
import api from '@/plugins/axios'

const route = useRoute()
const notification = useNotificationStore()
const i18n = useI18nStore()
const booking = ref(null)
const adminNotes = ref('')
const approving = ref(false)
const rejecting = ref(false)
const confirmApprove = ref(false)
const invoiceDialog = ref(false)
const selectedInvoice = ref(null)
const downloadingInvoice = ref(false)

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

function approveBooking() {
  confirmApprove.value = true
}

async function doApprove() {
  approving.value = true
  confirmApprove.value = false
  try {
    await api.post(`/admin/bookings/${route.params.id}/approve`, { admin_notes: adminNotes.value })
    notification.showSuccess(i18n.t('bookingDetail.successApprove'))
    await fetchBooking()
  } catch {
    notification.showError(i18n.t('register.error'))
  } finally {
    approving.value = false
  }
}

async function rejectBooking() {
  rejecting.value = true
  try {
    await api.post(`/admin/bookings/${route.params.id}/reject`, { admin_notes: adminNotes.value })
    notification.showSuccess(i18n.t('bookingDetail.successReject'))
    await fetchBooking()
  } catch {
    notification.showError(i18n.t('register.error'))
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
    notification.showError(i18n.t('invoices.fileNotFound'))
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
