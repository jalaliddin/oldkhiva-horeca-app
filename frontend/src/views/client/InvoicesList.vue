<template>
  <div>
    <div class="text-h5 font-weight-bold text-primary mb-6">Invoicelar</div>
    <v-card>
      <v-data-table :headers="headers" :items="invoices" :loading="loading">
        <template #item.status="{ item }">
          <v-chip :color="statusColor(item.status)" size="small" variant="tonal">{{ statusLabel(item.status) }}</v-chip>
        </template>
        <template #item.total_amount="{ item }">{{ Number(item.total_amount).toLocaleString() }} so'm</template>
        <template #item.balance="{ item }">{{ Number(item.balance).toLocaleString() }} so'm</template>
        <template #item.actions="{ item }">
          <v-btn size="small" icon="mdi-eye" variant="text" :to="`/client/invoices/${item.id}`" />
        </template>
      </v-data-table>
    </v-card>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/plugins/axios'

const loading = ref(true)
const invoices = ref([])

const headers = [
  { title: 'Invoice #', key: 'invoice_number' },
  { title: 'Sana', key: 'created_at' },
  { title: "Jami summa", key: 'total_amount' },
  { title: "To'langan", key: 'paid_amount' },
  { title: 'Qoldiq', key: 'balance' },
  { title: 'Muddat', key: 'due_date' },
  { title: 'Status', key: 'status' },
  { title: '', key: 'actions', sortable: false },
]

function statusColor(s) {
  return { unpaid: 'warning', partial: 'info', paid: 'success', overdue: 'error' }[s] || 'grey'
}
function statusLabel(s) {
  return { unpaid: "To'lanmagan", partial: 'Qisman', paid: "To'langan", overdue: "Muddati o'tgan" }[s] || s
}

onMounted(async () => {
  try {
    const res = await api.get('/invoices?per_page=50')
    invoices.value = res.data.data.data || []
  } finally {
    loading.value = false
  }
})
</script>
