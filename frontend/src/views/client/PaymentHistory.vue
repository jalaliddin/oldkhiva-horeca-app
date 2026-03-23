<template>
  <div>
    <div class="text-h5 font-weight-bold text-primary mb-6">To'lovlar tarixi</div>
    <v-card>
      <v-data-table :headers="headers" :items="payments" :loading="loading">
        <template #item.type="{ item }">
          <v-chip :color="item.type === 'deposit' ? 'info' : 'success'" size="small" variant="tonal">
            {{ item.type === 'deposit' ? 'Depozit' : 'Invoice' }}
          </v-chip>
        </template>
        <template #item.amount="{ item }">
          <span class="font-weight-bold text-success">+ {{ Number(item.amount).toLocaleString() }} so'm</span>
        </template>
        <template #item.payment_method="{ item }">
          {{ methodLabel(item.payment_method) }}
        </template>
      </v-data-table>
    </v-card>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/plugins/axios'

const loading = ref(true)
const payments = ref([])

const headers = [
  { title: "To'lov #", key: 'payment_number' },
  { title: 'Sana', key: 'payment_date' },
  { title: 'Turi', key: 'type' },
  { title: 'Summa', key: 'amount' },
  { title: "To'lov usuli", key: 'payment_method' },
  { title: 'Izoh', key: 'notes' },
]

function methodLabel(m) {
  return { cash: 'Naqd', bank_transfer: 'Bank o\'tkazmasi', card: 'Karta' }[m] || m
}

onMounted(async () => {
  try {
    const res = await api.get('/payments?per_page=50')
    payments.value = res.data.data.data || []
  } finally {
    loading.value = false
  }
})
</script>
