<template>
  <div>
    <div class="text-h5 font-weight-bold text-primary mb-6">{{ i18n.t('payments.history') }}</div>
    <v-card>
      <v-data-table :headers="headers" :items="payments" :loading="loading">
        <template #item.type="{ item }">
          <v-chip :color="item.type === 'deposit' ? 'info' : 'success'" size="small" variant="tonal">
            {{ item.type === 'deposit' ? i18n.t('payments.depositType') : 'Invoice' }}
          </v-chip>
        </template>
        <template #item.amount="{ item }">
          <span class="font-weight-bold text-success">+ {{ Number(item.amount).toLocaleString() }} {{ i18n.t('common.currency') }}</span>
        </template>
        <template #item.payment_method="{ item }">
          {{ methodLabel(item.payment_method) }}
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
const payments = ref([])

const headers = computed(() => [
  { title: i18n.t('payments.num'), key: 'payment_number' },
  { title: i18n.t('payments.date'), key: 'payment_date' },
  { title: i18n.t('payments.type'), key: 'type' },
  { title: i18n.t('common.amount'), key: 'amount' },
  { title: i18n.t('payments.method'), key: 'payment_method' },
  { title: i18n.t('common.notes'), key: 'notes' },
])

function methodLabel(m) {
  return i18n.t(`paymentMethod.${m}`) || m
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
