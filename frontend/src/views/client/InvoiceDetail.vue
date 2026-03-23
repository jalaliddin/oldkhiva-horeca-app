<template>
  <div>
    <div class="d-flex align-center mb-6">
      <v-btn icon="mdi-arrow-left" variant="text" to="/client/invoices" class="mr-3" />
      <div class="text-h5 font-weight-bold text-primary">Invoice tafsiloti</div>
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
              <v-col cols="6"><div class="text-caption">Sana</div><div>{{ invoice.created_at?.split('T')[0] }}</div></v-col>
              <v-col cols="6"><div class="text-caption">To'lov muddati</div><div>{{ invoice.due_date }}</div></v-col>
            </v-row>
          </v-card-text>
        </v-card>

        <v-card>
          <v-card-title class="pa-4">Buyurtma tarkibi</v-card-title>
          <v-data-table
            :headers="itemHeaders"
            :items="invoice.booking?.items || []"
            density="compact"
            hide-default-footer
          >
            <template #item.item_price="{ item }">{{ Number(item.item_price).toLocaleString() }} so'm</template>
            <template #item.subtotal="{ item }">{{ Number(item.subtotal).toLocaleString() }} so'm</template>
          </v-data-table>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card class="mb-4">
          <v-card-title class="pa-4 text-primary">To'lov holati</v-card-title>
          <v-card-text>
            <div class="d-flex justify-space-between mb-2">
              <span>Subtotal:</span>
              <span>{{ Number(invoice.subtotal).toLocaleString() }} so'm</span>
            </div>
            <div class="d-flex justify-space-between mb-2 font-weight-bold text-h6">
              <span>Jami:</span>
              <span>{{ Number(invoice.total_amount).toLocaleString() }} so'm</span>
            </div>
            <v-divider class="my-2" />
            <div class="d-flex justify-space-between mb-1 text-success">
              <span>To'langan:</span>
              <span>{{ Number(invoice.paid_amount).toLocaleString() }} so'm</span>
            </div>
            <div class="d-flex justify-space-between font-weight-bold text-error">
              <span>Qoldiq:</span>
              <span>{{ Number(invoice.balance).toLocaleString() }} so'm</span>
            </div>
          </v-card-text>
          <v-card-actions class="pa-4">
            <v-btn block color="primary" prepend-icon="mdi-download" :loading="downloading" @click="downloadPdf">
              PDF yuklab olish
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useNotificationStore } from '@/stores/notification'
import api from '@/plugins/axios'

const route = useRoute()
const notification = useNotificationStore()
const invoice = ref(null)
const downloading = ref(false)

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
    notification.showError('Fayl topilmadi yoki hali yaratilmagan')
  } finally {
    downloading.value = false
  }
}

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

onMounted(async () => {
  const res = await api.get(`/invoices/${route.params.id}`)
  invoice.value = res.data.data
})
</script>
