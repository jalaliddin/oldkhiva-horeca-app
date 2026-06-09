<template>
  <div>
    <div class="text-h5 font-weight-bold text-primary mb-6">{{ i18n.t('payments.title') }}</div>

    <v-tabs v-model="activeTab" color="accent" class="mb-6">
      <v-tab value="payments">{{ i18n.t('payments.invoicePayments') }}</v-tab>
      <v-tab value="deposits">{{ i18n.t('payments.deposits') }}</v-tab>
      <v-tab value="add">{{ i18n.t('payments.addTab') }}</v-tab>
    </v-tabs>

    <v-window v-model="activeTab">
      <v-window-item value="payments">
        <v-card>
          <v-data-table :headers="paymentHeaders" :items="payments.filter(p => p.type === 'invoice_payment')" :loading="loading">
            <template #item.client="{ item }">{{ item.client?.company_name }}</template>
            <template #item.amount="{ item }">{{ Number(item.amount).toLocaleString() }} {{ i18n.t('common.currency') }}</template>
            <template #item.payment_method="{ item }">{{ methodLabel(item.payment_method) }}</template>
          </v-data-table>
        </v-card>
      </v-window-item>

      <v-window-item value="deposits">
        <v-card>
          <v-data-table :headers="depositHeaders" :items="deposits" :loading="loading">
            <template #item.client="{ item }">{{ item.client?.company_name }}</template>
            <template #item.balance="{ item }">
              <span class="font-weight-bold text-primary">{{ Number(item.balance).toLocaleString() }} {{ i18n.t('common.currency') }}</span>
            </template>
          </v-data-table>
        </v-card>
      </v-window-item>

      <v-window-item value="add">
        <v-card class="pa-4">
          <v-card-title class="pa-0 mb-4 text-primary">{{ i18n.t('payments.addNew') }}</v-card-title>
          <v-row>
            <v-col cols="12" md="6">
              <v-select v-model="payForm.type" :label="i18n.t('payments.paymentType')" :items="typeOptions" class="mb-3" />
              <v-autocomplete v-model="payForm.client_id" :label="i18n.t('payments.client')" :items="clientOptions" item-title="label" item-value="id" class="mb-3" @update:modelValue="loadClientInvoices" />
              <v-select v-if="payForm.type === 'invoice_payment'" v-model="payForm.invoice_id" label="Invoice" :items="invoiceOptions" item-title="label" item-value="id" class="mb-3" />
              <v-text-field v-model.number="payForm.amount" :label="i18n.t('payments.amountLabel')" type="number" class="mb-3" />
              <v-text-field v-model="payForm.payment_date" :label="i18n.t('payments.paymentDate')" type="date" class="mb-3" />
              <v-select v-model="payForm.payment_method" :label="i18n.t('payments.method')" :items="methodOptions" class="mb-3" />
              <v-text-field v-model="payForm.reference" :label="i18n.t('payments.bankRef')" class="mb-3" />
              <v-textarea v-model="payForm.notes" :label="i18n.t('payments.notesOpt')" rows="2" class="mb-4" />
              <v-btn color="primary" block :loading="submitting" @click="submitPayment">
                {{ i18n.t('payments.savePayment') }}
              </v-btn>
            </v-col>
          </v-row>
        </v-card>
      </v-window-item>
    </v-window>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useNotificationStore } from '@/stores/notification'
import { useI18nStore } from '@/stores/i18n'
import api from '@/plugins/axios'

const notification = useNotificationStore()
const i18n = useI18nStore()
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

const typeOptions = computed(() => [
  { title: i18n.t('payments.invoiceType'), value: 'invoice_payment' },
  { title: i18n.t('payments.depositType'), value: 'deposit' },
])

const methodOptions = computed(() => [
  { title: i18n.t('paymentMethod.bank_transfer'), value: 'bank_transfer' },
  { title: i18n.t('paymentMethod.cash'), value: 'cash' },
  { title: i18n.t('paymentMethod.card'), value: 'card' },
])

const paymentHeaders = computed(() => [
  { title: i18n.t('payments.num'), key: 'payment_number' },
  { title: i18n.t('payments.client'), key: 'client' },
  { title: i18n.t('payments.date'), key: 'payment_date' },
  { title: i18n.t('common.amount'), key: 'amount' },
  { title: i18n.t('payments.method'), key: 'payment_method' },
  { title: i18n.t('common.notes'), key: 'notes' },
])

const depositHeaders = computed(() => [
  { title: i18n.t('payments.client'), key: 'client' },
  { title: i18n.t('common.balance'), key: 'balance' },
  { title: i18n.t('payments.lastUpdated'), key: 'updated_at' },
])

const clientOptions = ref([])
const invoiceOptions = ref([])

function methodLabel(m) {
  return i18n.t(`paymentMethod.${m}`) || m
}

async function loadClientInvoices(clientId) {
  if (!clientId || payForm.value.type !== 'invoice_payment') return
  const res = await api.get('/admin/invoices', { params: { client_id: clientId, status: 'unpaid' } })
  const items = res.data.data.data || []
  invoiceOptions.value = items.map(i => ({
    id: i.id,
    label: `${i.invoice_number} — ${Number(i.balance).toLocaleString()} ${i18n.t('common.currency')} ${i18n.t('common.balance')}`,
  }))
}

async function submitPayment() {
  submitting.value = true
  try {
    const endpoint = payForm.value.type === 'deposit' ? '/admin/deposits' : '/admin/payments'
    await api.post(endpoint, payForm.value)
    notification.showSuccess(i18n.t('payments.successMsg'))
    payForm.value = { ...payForm.value, client_id: null, invoice_id: null, amount: null, reference: '', notes: '' }
    await fetchData()
    activeTab.value = 'payments'
  } catch (err) {
    notification.showError(err.response?.data?.message || i18n.t('register.error'))
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
