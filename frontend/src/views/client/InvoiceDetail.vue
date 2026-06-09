<template>
  <div>
    <div class="d-flex align-center mb-6">
      <v-btn icon="mdi-arrow-left" variant="text" to="/client/invoices" class="mr-3" />
      <div class="text-h5 font-weight-bold text-primary">{{ i18n.t('invoices.detail') }}</div>
    </div>

    <v-row v-if="invoice">
      <v-col cols="12" md="8">
        <v-card class="mb-4">
          <v-card-title class="pa-4">
            {{ invoice.invoice_number }}
            <v-chip :color="statusColor(invoice.status)" size="small" variant="tonal" class="ml-3">
              {{ statusLabel(invoice.status) }}
            </v-chip>
          </v-card-title>
          <v-card-text>
            <v-row>
              <v-col cols="6"><div class="text-caption">{{ i18n.t('invoices.date') }}</div><div>{{ invoice.created_at?.split('T')[0] }}</div></v-col>
              <v-col cols="6"><div class="text-caption">{{ i18n.t('invoices.payDue') }}</div><div>{{ invoice.due_date }}</div></v-col>
            </v-row>
          </v-card-text>
        </v-card>

        <v-card>
          <v-card-title class="pa-4">{{ i18n.t('invoices.orderItems') }}</v-card-title>
          <v-data-table
            :headers="itemHeaders"
            :items="invoice.booking?.items || []"
            density="compact"
            hide-default-footer
          >
            <template #item.item_price="{ item }">{{ Number(item.item_price).toLocaleString() }} {{ i18n.t('common.currency') }}</template>
            <template #item.subtotal="{ item }">{{ Number(item.subtotal).toLocaleString() }} {{ i18n.t('common.currency') }}</template>
          </v-data-table>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card class="mb-4">
          <v-card-title class="pa-4 text-primary">{{ i18n.t('invoices.paymentStatus') }}</v-card-title>
          <v-card-text>
            <div class="d-flex justify-space-between mb-2">
              <span>{{ i18n.t('invoices.subtotal') }}:</span>
              <span>{{ Number(invoice.subtotal).toLocaleString() }} {{ i18n.t('common.currency') }}</span>
            </div>
            <div class="d-flex justify-space-between mb-2 font-weight-bold text-h6">
              <span>{{ i18n.t('common.total') }}:</span>
              <span>{{ Number(invoice.total_amount).toLocaleString() }} {{ i18n.t('common.currency') }}</span>
            </div>
            <v-divider class="my-2" />
            <div class="d-flex justify-space-between mb-1 text-success">
              <span>{{ i18n.t('common.paid') }}:</span>
              <span>{{ Number(invoice.paid_amount).toLocaleString() }} {{ i18n.t('common.currency') }}</span>
            </div>
            <div class="d-flex justify-space-between font-weight-bold text-error">
              <span>{{ i18n.t('common.balance') }}:</span>
              <span>{{ Number(invoice.balance).toLocaleString() }} {{ i18n.t('common.currency') }}</span>
            </div>
          </v-card-text>
          <v-card-actions class="pa-4">
            <v-btn block color="primary" prepend-icon="mdi-download" :loading="downloading" @click="downloadPdf">
              {{ i18n.t('invoices.downloadPdf') }}
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
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
const invoice = ref(null)
const downloading = ref(false)

const itemHeaders = computed(() => [
  { title: i18n.t('common.name'), key: 'item_name' },
  { title: i18n.t('common.price'), key: 'item_price' },
  { title: i18n.t('common.quantity'), key: 'quantity' },
  { title: i18n.t('common.total'), key: 'subtotal' },
])

async function downloadPdf() {
  downloading.value = true
  try {
    const res = await api.get(`/invoices/${route.params.id}/download`, { responseType: 'blob' })
    const url = URL.createObjectURL(new Blob([res.data], { type: 'application/pdf' }))
    const link = document.createElement('a')
    link.href = url
    link.download = `Invoice-${invoice.value?.invoice_number || route.params.id}.pdf`
    link.click()
    URL.revokeObjectURL(url)
  } catch {
    notification.showError(i18n.t('invoices.fileNotFound'))
  } finally {
    downloading.value = false
  }
}

function statusColor(s) {
  return { unpaid: 'warning', partial: 'info', paid: 'success', overdue: 'error' }[s] || 'grey'
}
function statusLabel(s) { return i18n.t(`invoiceStatus.${s}`) || s }

onMounted(async () => {
  const res = await api.get(`/invoices/${route.params.id}`)
  invoice.value = res.data.data
})
</script>
