<template>
  <div>
    <div class="text-h5 font-weight-bold text-primary mb-6">To'lovlar</div>

    <v-tabs v-model="activeTab" color="accent" class="mb-6">
      <v-tab value="payments">Invoice To'lovlar</v-tab>
      <v-tab value="deposits">Depozitlar</v-tab>
      <v-tab value="add">+ To'lov kiritish</v-tab>
    </v-tabs>

    <v-window v-model="activeTab">
      <v-window-item value="payments">
        <v-card>
          <v-data-table :headers="paymentHeaders" :items="payments.filter(p => p.type === 'invoice_payment')" :loading="loading">
            <template #item.client="{ item }">{{ item.client?.company_name }}</template>
            <template #item.amount="{ item }">{{ Number(item.amount).toLocaleString() }} so'm</template>
            <template #item.payment_method="{ item }">{{ methodLabel(item.payment_method) }}</template>
          </v-data-table>
        </v-card>
      </v-window-item>

      <v-window-item value="deposits">
        <v-card>
          <v-data-table :headers="depositHeaders" :items="deposits" :loading="loading">
            <template #item.client="{ item }">{{ item.client?.company_name }}</template>
            <template #item.balance="{ item }">
              <span class="font-weight-bold text-primary">{{ Number(item.balance).toLocaleString() }} so'm</span>
            </template>
          </v-data-table>
        </v-card>
      </v-window-item>

      <v-window-item value="add">
        <v-card class="pa-4">
          <v-card-title class="pa-0 mb-4 text-primary">Yangi to'lov kiritish</v-card-title>
          <v-row>
            <v-col cols="12" md="6">
              <v-select v-model="payForm.type" label="To'lov turi" :items="typeOptions" class="mb-3" />
              <v-autocomplete v-model="payForm.client_id" label="Mijoz" :items="clientOptions" item-title="label" item-value="id" class="mb-3" @update:modelValue="loadClientInvoices" />
              <v-select v-if="payForm.type === 'invoice_payment'" v-model="payForm.invoice_id" label="Invoice" :items="invoiceOptions" item-title="label" item-value="id" class="mb-3" />
              <v-text-field v-model.number="payForm.amount" label="Summa (so'm)" type="number" class="mb-3" />
              <v-text-field v-model="payForm.payment_date" label="To'lov sanasi" type="date" class="mb-3" />
              <v-select v-model="payForm.payment_method" label="To'lov usuli" :items="methodOptions" class="mb-3" />
              <v-text-field v-model="payForm.reference" label="Bank referans (ixtiyoriy)" class="mb-3" />
              <v-textarea v-model="payForm.notes" label="Izoh (ixtiyoriy)" rows="2" class="mb-4" />
              <v-btn color="primary" block :loading="submitting" @click="submitPayment">
                To'lovni saqlash
              </v-btn>
            </v-col>
          </v-row>
        </v-card>
      </v-window-item>
    </v-window>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useNotificationStore } from '@/stores/notification'
import api from '@/plugins/axios'

const notification = useNotificationStore()
const loading = ref(true)
const submitting = ref(false)
const activeTab = ref('payments')
const payments = ref([])
const deposits = ref([])
const clients = ref([])
const clientInvoices = ref([])

const payForm = ref({
  type: 'invoice_payment',
  client_id: null,
  invoice_id: null,
  amount: null,
  payment_date: new Date().toISOString().split('T')[0],
  payment_method: 'bank_transfer',
  reference: '',
  notes: '',
})

const typeOptions = [
  { title: 'Invoice to\'lov', value: 'invoice_payment' },
  { title: 'Depozit', value: 'deposit' },
]

const methodOptions = [
  { title: 'Bank o\'tkazmasi', value: 'bank_transfer' },
  { title: 'Naqd', value: 'cash' },
  { title: 'Karta', value: 'card' },
]

const paymentHeaders = [
  { title: "To'lov #", key: 'payment_number' },
  { title: 'Mijoz', key: 'client' },
  { title: 'Sana', key: 'payment_date' },
  { title: 'Summa', key: 'amount' },
  { title: 'Usul', key: 'payment_method' },
  { title: 'Izoh', key: 'notes' },
]

const depositHeaders = [
  { title: 'Mijoz', key: 'client' },
  { title: 'Balans', key: 'balance' },
  { title: 'Oxirgi yangilanish', key: 'updated_at' },
]

const clientOptions = ref([])
const invoiceOptions = ref([])

function methodLabel(m) {
  return { cash: 'Naqd', bank_transfer: "Bank o'tkazmasi", card: 'Karta' }[m] || m
}

async function loadClientInvoices(clientId) {
  if (!clientId || payForm.value.type !== 'invoice_payment') return
  const res = await api.get('/admin/invoices', { params: { client_id: clientId, status: 'unpaid' } })
  const items = res.data.data.data || []
  invoiceOptions.value = items.map(i => ({
    id: i.id,
    label: `${i.invoice_number} — ${Number(i.balance).toLocaleString()} so'm qoldiq`,
  }))
}

async function submitPayment() {
  submitting.value = true
  try {
    const endpoint = payForm.value.type === 'deposit' ? '/admin/deposits' : '/admin/payments'
    await api.post(endpoint, payForm.value)
    notification.showSuccess("To'lov saqlandi!")
    payForm.value = { ...payForm.value, client_id: null, invoice_id: null, amount: null, reference: '', notes: '' }
    await fetchData()
    activeTab.value = 'payments'
  } catch (err) {
    notification.showError(err.response?.data?.message || 'Xato yuz berdi')
  } finally {
    submitting.value = false
  }
}

async function fetchData() {
  loading.value = true
  try {
    const [paymentsRes, depositsRes, clientsRes] = await Promise.all([
      api.get('/admin/payments?per_page=100'),
      api.get('/admin/deposits'),
      api.get('/admin/clients?per_page=100'),
    ])
    payments.value = paymentsRes.data.data.data || []
    deposits.value = depositsRes.data.data
    clients.value = clientsRes.data.data.data || []
    clientOptions.value = clients.value.map(c => ({ id: c.id, label: c.company_name || c.name }))
  } finally {
    loading.value = false
  }
}

onMounted(fetchData)
</script>
