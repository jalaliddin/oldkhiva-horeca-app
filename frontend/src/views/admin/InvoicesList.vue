<template>
  <div>
    <div class="text-h5 font-weight-bold text-primary mb-6">Invoicelar</div>
    <v-card>
      <v-card-text class="pb-0">
        <v-select v-model="statusFilter" label="Status" :items="statusOptions" hide-details density="compact" clearable class="mb-2" style="max-width: 200px" @update:modelValue="fetchInvoices" />
      </v-card-text>
      <v-data-table :headers="headers" :items="invoices" :loading="loading">
        <template #item.client="{ item }">{{ item.client?.company_name }}</template>
        <template #item.status="{ item }">
          <v-chip :color="statusColor(item.status)" size="small" variant="tonal">{{ statusLabel(item.status) }}</v-chip>
        </template>
        <template #item.total_amount="{ item }">{{ Number(item.total_amount).toLocaleString() }} so'm</template>
        <template #item.balance="{ item }">{{ Number(item.balance).toLocaleString() }} so'm</template>
        <template #item.actions="{ item }">
          <v-btn size="small" icon="mdi-eye" variant="text" @click="openDetail(item)" />
          <v-btn size="small" icon="mdi-download" variant="text" :loading="downloadingId === item.id" @click="downloadInvoice(item)" />
        </template>
      </v-data-table>
    </v-card>

    <!-- Invoice detail dialog -->
    <v-dialog v-model="detailDialog" max-width="700">
      <v-card v-if="selectedInvoice">
        <v-card-title class="pa-4 d-flex align-center justify-space-between">
          <span>{{ selectedInvoice.invoice_number }}</span>
          <v-chip :color="statusColor(selectedInvoice.status)" size="small" variant="tonal">
            {{ statusLabel(selectedInvoice.status) }}
          </v-chip>
        </v-card-title>
        <v-card-text>
          <v-row class="mb-4">
            <v-col cols="6">
              <div class="text-caption text-medium-emphasis">Mijoz</div>
              <div class="font-weight-medium">{{ selectedInvoice.client?.company_name }}</div>
            </v-col>
            <v-col cols="6">
              <div class="text-caption text-medium-emphasis">Sana</div>
              <div>{{ selectedInvoice.created_at?.split('T')[0] }}</div>
            </v-col>
            <v-col cols="6">
              <div class="text-caption text-medium-emphasis">To'lov muddati</div>
              <div>{{ selectedInvoice.due_date }}</div>
            </v-col>
            <v-col cols="6">
              <div class="text-caption text-medium-emphasis">Bron</div>
              <div>{{ selectedInvoice.booking?.booking_number }}</div>
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
          <v-btn
            color="primary"
            prepend-icon="mdi-download"
            :loading="downloadingId === selectedInvoice.id"
            @click="downloadInvoice(selectedInvoice)"
          >
            PDF yuklab olish
          </v-btn>
          <v-spacer />
          <v-btn @click="detailDialog = false">Yopish</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useNotificationStore } from '@/stores/notification'
import api from '@/plugins/axios'

const notification = useNotificationStore()
const loading = ref(true)
const invoices = ref([])
const statusFilter = ref(null)
const detailDialog = ref(false)
const selectedInvoice = ref(null)
const downloadingId = ref(null)

const statusOptions = [
  { title: 'Barcha', value: null },
  { title: "To'lanmagan", value: 'unpaid' },
  { title: 'Qisman', value: 'partial' },
  { title: "To'langan", value: 'paid' },
  { title: "Muddati o'tgan", value: 'overdue' },
]

const headers = [
  { title: 'Invoice #', key: 'invoice_number' },
  { title: 'Mijoz', key: 'client' },
  { title: 'Sana', key: 'created_at' },
  { title: 'Jami', key: 'total_amount' },
  { title: 'Qoldiq', key: 'balance' },
  { title: 'Muddat', key: 'due_date' },
  { title: 'Status', key: 'status' },
  { title: '', key: 'actions', sortable: false },
]

const itemHeaders = [
  { title: 'Nomi', key: 'item_name' },
  { title: 'Narxi', key: 'item_price' },
  { title: 'Miqdor', key: 'quantity' },
  { title: 'Jami', key: 'subtotal' },
]

function statusColor(s) {
  return { unpaid: 'warning', partial: 'info', paid: 'success', overdue: 'error' }[s] || 'grey'
}
function statusLabel(s) {
  return { unpaid: "To'lanmagan", partial: 'Qisman', paid: "To'langan", overdue: "Muddati o'tgan" }[s] || s
}

async function openDetail(invoice) {
  // Load full detail with booking items
  const res = await api.get(`/admin/invoices/${invoice.id}`)
  selectedInvoice.value = res.data.data
  detailDialog.value = true
}

async function downloadInvoice(invoice) {
  downloadingId.value = invoice.id
  try {
    const res = await api.get(`/admin/invoices/${invoice.id}/download`, { responseType: 'blob' })
    const url = URL.createObjectURL(new Blob([res.data], { type: 'application/pdf' }))
    const link = document.createElement('a')
    link.href = url
    link.download = `Invoice-${invoice.invoice_number}.pdf`
    link.click()
    URL.revokeObjectURL(url)
  } catch {
    notification.showError('Fayl topilmadi yoki hali yaratilmagan')
  } finally {
    downloadingId.value = null
  }
}

async function fetchInvoices() {
  loading.value = true
  try {
    const params = statusFilter.value ? { status: statusFilter.value } : {}
    const res = await api.get('/admin/invoices', { params })
    invoices.value = res.data.data.data || []
  } finally {
    loading.value = false
  }
}

onMounted(fetchInvoices)
</script>
