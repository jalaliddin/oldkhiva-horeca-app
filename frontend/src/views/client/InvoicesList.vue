<template>
  <div>
    <div class="text-h5 font-weight-bold text-primary mb-6">{{ i18n.t('invoices.title') }}</div>
    <v-card>
      <v-data-table :headers="headers" :items="invoices" :loading="loading">
        <template #item.status="{ item }">
          <v-chip :color="statusColor(item.status)" size="small" variant="tonal">{{ statusLabel(item.status) }}</v-chip>
        </template>
        <template #item.total_amount="{ item }">{{ Number(item.total_amount).toLocaleString() }} {{ i18n.t('common.currency') }}</template>
        <template #item.balance="{ item }">{{ Number(item.balance).toLocaleString() }} {{ i18n.t('common.currency') }}</template>
        <template #item.actions="{ item }">
          <v-btn size="small" icon="mdi-eye" variant="text" :to="`/client/invoices/${item.id}`" />
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
const invoices = ref([])

const headers = computed(() => [
  { title: i18n.t('invoices.num'), key: 'invoice_number' },
  { title: i18n.t('invoices.date'), key: 'created_at' },
  { title: i18n.t('invoices.totalAmount'), key: 'total_amount' },
  { title: i18n.t('invoices.paidAmount'), key: 'paid_amount' },
  { title: i18n.t('invoices.balance'), key: 'balance' },
  { title: i18n.t('invoices.dueDate'), key: 'due_date' },
  { title: i18n.t('common.status'), key: 'status' },
  { title: '', key: 'actions', sortable: false },
])

function statusColor(s) {
  return { unpaid: 'warning', partial: 'info', paid: 'success', overdue: 'error' }[s] || 'grey'
}
function statusLabel(s) { return i18n.t(`invoiceStatus.${s}`) || s }

onMounted(async () => {
  try {
    const res = await api.get('/invoices?per_page=50')
    invoices.value = res.data.data.data || []
  } finally {
    loading.value = false
  }
})
</script>
