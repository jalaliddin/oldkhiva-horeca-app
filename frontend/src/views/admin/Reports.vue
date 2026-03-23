<template>
  <div>
    <div class="text-h5 font-weight-bold text-primary mb-6">Hisobotlar</div>

    <v-tabs v-model="tab" color="accent" class="mb-6">
      <v-tab value="clients">
        <v-icon start size="small">mdi-account-group-outline</v-icon>
        Mijozlar hisoboti
      </v-tab>
      <v-tab value="invoices">
        <v-icon start size="small">mdi-receipt-text-outline</v-icon>
        Invoice hisoboti
      </v-tab>
    </v-tabs>

    <v-window v-model="tab">
      <!-- CLIENTS REPORT -->
      <v-window-item value="clients">
        <!-- Filters -->
        <v-card class="mb-4 pa-4">
          <v-row dense align="center">
            <v-col cols="12" sm="4">
              <v-text-field
                v-model="clientFilters.search"
                label="Qidirish"
                prepend-inner-icon="mdi-magnify"
                density="compact"
                hide-details
                clearable
                @update:modelValue="fetchClients"
              />
            </v-col>
            <v-col cols="12" sm="3">
              <v-select
                v-model="clientFilters.filter"
                :items="financialFilters"
                item-title="title"
                item-value="value"
                label="Moliyaviy holat"
                density="compact"
                hide-details
                @update:modelValue="fetchClients"
              />
            </v-col>
            <v-col cols="6" sm="2">
              <v-text-field
                v-model="clientFilters.date_from"
                label="Dan"
                type="date"
                density="compact"
                hide-details
                @update:modelValue="fetchClients"
              />
            </v-col>
            <v-col cols="6" sm="2">
              <v-text-field
                v-model="clientFilters.date_to"
                label="Gacha"
                type="date"
                density="compact"
                hide-details
                @update:modelValue="fetchClients"
              />
            </v-col>
            <v-col cols="12" sm="1">
              <v-btn icon="mdi-refresh" variant="text" :loading="clientLoading" @click="fetchClients" />
            </v-col>
          </v-row>
        </v-card>

        <!-- Summary cards -->
        <v-row class="mb-4">
          <v-col cols="6" sm="4" md="2">
            <v-card class="pa-3 text-center" color="primary" variant="tonal">
              <div class="text-h5 font-weight-bold">{{ clientSummary.total_clients }}</div>
              <div class="text-caption">Jami mijozlar</div>
            </v-card>
          </v-col>
          <v-col cols="6" sm="4" md="2">
            <v-card class="pa-3 text-center" color="error" variant="tonal">
              <div class="text-h5 font-weight-bold">{{ clientSummary.total_debtors }}</div>
              <div class="text-caption">Qarzdorlar</div>
            </v-card>
          </v-col>
          <v-col cols="6" sm="4" md="2">
            <v-card class="pa-3 text-center" color="success" variant="tonal">
              <div class="text-h5 font-weight-bold">{{ clientSummary.total_depositors }}</div>
              <div class="text-caption">Depozitchilar</div>
            </v-card>
          </v-col>
          <v-col cols="6" sm="4" md="2">
            <v-card class="pa-3 text-center" color="error" variant="tonal">
              <div class="text-caption mb-1">Umumiy qarz</div>
              <div class="text-body-1 font-weight-bold">{{ fmt(clientSummary.total_debt) }}</div>
            </v-card>
          </v-col>
          <v-col cols="6" sm="4" md="2">
            <v-card class="pa-3 text-center" color="success" variant="tonal">
              <div class="text-caption mb-1">Umumiy depozit</div>
              <div class="text-body-1 font-weight-bold">{{ fmt(clientSummary.total_deposit) }}</div>
            </v-card>
          </v-col>
          <v-col cols="6" sm="4" md="2">
            <v-card class="pa-3 text-center" color="info" variant="tonal">
              <div class="text-caption mb-1">Jami invoice</div>
              <div class="text-body-1 font-weight-bold">{{ fmt(clientSummary.total_invoiced) }}</div>
            </v-card>
          </v-col>
        </v-row>

        <!-- Table -->
        <v-card>
          <v-data-table
            :headers="clientHeaders"
            :items="clients"
            :loading="clientLoading"
            density="compact"
          >
            <template #item.company_name="{ item }">
              <div class="font-weight-medium">{{ item.company_name }}</div>
              <div class="text-caption text-medium-emphasis">{{ item.email }}</div>
            </template>
            <template #item.financial_status="{ item }">
              <v-chip :color="statusColor(item.financial_status)" size="x-small" variant="tonal">
                {{ statusLabel(item.financial_status) }}
              </v-chip>
            </template>
            <template #item.deposit="{ item }">
              <span :class="item.deposit > 0 ? 'text-success font-weight-medium' : 'text-medium-emphasis'">
                {{ fmt(item.deposit) }}
              </span>
            </template>
            <template #item.debt="{ item }">
              <span :class="item.debt > 0 ? 'text-error font-weight-medium' : 'text-medium-emphasis'">
                {{ fmt(item.debt) }}
              </span>
            </template>
            <template #item.total_invoiced="{ item }">{{ fmt(item.total_invoiced) }}</template>
            <template #item.total_paid="{ item }">{{ fmt(item.total_paid) }}</template>
            <template #item.is_active="{ item }">
              <v-icon :color="item.is_active ? 'success' : 'grey'" size="18">
                {{ item.is_active ? 'mdi-check-circle' : 'mdi-close-circle' }}
              </v-icon>
            </template>
          </v-data-table>
        </v-card>
      </v-window-item>

      <!-- INVOICES REPORT -->
      <v-window-item value="invoices">
        <!-- Filters -->
        <v-card class="mb-4 pa-4">
          <v-row dense align="center">
            <v-col cols="12" sm="3">
              <v-select
                v-model="invoiceFilters.status"
                :items="invoiceStatuses"
                item-title="title"
                item-value="value"
                label="Status"
                density="compact"
                hide-details
                clearable
                @update:modelValue="fetchInvoices"
              />
            </v-col>
            <v-col cols="6" sm="3">
              <v-text-field
                v-model="invoiceFilters.date_from"
                label="Dan"
                type="date"
                density="compact"
                hide-details
                @update:modelValue="fetchInvoices"
              />
            </v-col>
            <v-col cols="6" sm="3">
              <v-text-field
                v-model="invoiceFilters.date_to"
                label="Gacha"
                type="date"
                density="compact"
                hide-details
                @update:modelValue="fetchInvoices"
              />
            </v-col>
            <v-col cols="12" sm="1">
              <v-btn icon="mdi-refresh" variant="text" :loading="invoiceLoading" @click="fetchInvoices" />
            </v-col>
          </v-row>
        </v-card>

        <!-- Summary cards -->
        <v-row class="mb-4">
          <v-col cols="6" sm="4" md="2">
            <v-card class="pa-3 text-center" variant="tonal">
              <div class="text-h5 font-weight-bold">{{ invoiceSummary.total }}</div>
              <div class="text-caption">Jami</div>
            </v-card>
          </v-col>
          <v-col cols="6" sm="4" md="2">
            <v-card class="pa-3 text-center" color="warning" variant="tonal">
              <div class="text-h5 font-weight-bold">{{ invoiceSummary.unpaid }}</div>
              <div class="text-caption">To'lanmagan</div>
            </v-card>
          </v-col>
          <v-col cols="6" sm="4" md="2">
            <v-card class="pa-3 text-center" color="info" variant="tonal">
              <div class="text-h5 font-weight-bold">{{ invoiceSummary.partial }}</div>
              <div class="text-caption">Qisman</div>
            </v-card>
          </v-col>
          <v-col cols="6" sm="4" md="2">
            <v-card class="pa-3 text-center" color="success" variant="tonal">
              <div class="text-h5 font-weight-bold">{{ invoiceSummary.paid }}</div>
              <div class="text-caption">To'langan</div>
            </v-card>
          </v-col>
          <v-col cols="6" sm="4" md="2">
            <v-card class="pa-3 text-center" color="error" variant="tonal">
              <div class="text-h5 font-weight-bold">{{ invoiceSummary.overdue }}</div>
              <div class="text-caption">Muddati o'tgan</div>
            </v-card>
          </v-col>
          <v-col cols="6" sm="4" md="2">
            <v-card class="pa-3 text-center" color="error" variant="tonal">
              <div class="text-caption mb-1">Qoldiq qarz</div>
              <div class="text-body-1 font-weight-bold">{{ fmt(invoiceSummary.total_balance) }}</div>
            </v-card>
          </v-col>
        </v-row>

        <!-- Amount summary row -->
        <v-row class="mb-4">
          <v-col cols="12" sm="4">
            <v-card class="pa-4" variant="outlined">
              <div class="text-caption text-medium-emphasis">Jami hisoblangan</div>
              <div class="text-h6 font-weight-bold text-primary">{{ fmt(invoiceSummary.total_amount) }}</div>
            </v-card>
          </v-col>
          <v-col cols="12" sm="4">
            <v-card class="pa-4" variant="outlined">
              <div class="text-caption text-medium-emphasis">Jami to'langan</div>
              <div class="text-h6 font-weight-bold text-success">{{ fmt(invoiceSummary.total_paid) }}</div>
            </v-card>
          </v-col>
          <v-col cols="12" sm="4">
            <v-card class="pa-4" variant="outlined">
              <div class="text-caption text-medium-emphasis">Umumiy qoldiq</div>
              <div class="text-h6 font-weight-bold text-error">{{ fmt(invoiceSummary.total_balance) }}</div>
            </v-card>
          </v-col>
        </v-row>

        <!-- Table -->
        <v-card>
          <v-data-table
            :headers="invoiceHeaders"
            :items="invoices"
            :loading="invoiceLoading"
            density="compact"
          >
            <template #item.client="{ item }">{{ item.client?.company_name }}</template>
            <template #item.status="{ item }">
              <v-chip :color="invStatusColor(item.status)" size="x-small" variant="tonal">
                {{ invStatusLabel(item.status) }}
              </v-chip>
            </template>
            <template #item.total_amount="{ item }">{{ fmt(item.total_amount) }}</template>
            <template #item.paid_amount="{ item }">
              <span class="text-success">{{ fmt(item.paid_amount) }}</span>
            </template>
            <template #item.balance="{ item }">
              <span :class="item.balance > 0 ? 'text-error font-weight-medium' : ''">
                {{ fmt(item.balance) }}
              </span>
            </template>
            <template #item.created_at="{ item }">{{ item.created_at?.split('T')[0] }}</template>
          </v-data-table>
        </v-card>
      </v-window-item>
    </v-window>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/plugins/axios'

const tab = ref('clients')

// ── Clients ──────────────────────────────────────────────
const clientLoading = ref(false)
const clients = ref([])
const clientSummary = ref({
  total_clients: 0, total_debtors: 0, total_depositors: 0,
  total_debt: 0, total_deposit: 0, total_invoiced: 0,
})
const clientFilters = ref({ search: '', filter: 'all', date_from: '', date_to: '' })

const financialFilters = [
  { title: 'Barchasi', value: 'all' },
  { title: 'Qarzdorlar', value: 'debtors' },
  { title: 'Depozitchilar', value: 'depositors' },
]

const clientHeaders = [
  { title: 'Kompaniya', key: 'company_name', minWidth: '180px' },
  { title: 'Mas\'ul shaxs', key: 'contact_person' },
  { title: 'Telefon', key: 'phone' },
  { title: 'Holat', key: 'financial_status' },
  { title: 'Depozit', key: 'deposit', align: 'end' },
  { title: 'Qarz', key: 'debt', align: 'end' },
  { title: 'Jami invoice', key: 'total_invoiced', align: 'end' },
  { title: "To'langan", key: 'total_paid', align: 'end' },
  { title: 'Bronlar', key: 'booking_count', align: 'center' },
  { title: 'Faol', key: 'is_active', align: 'center' },
  { title: "Ro'yxatdan o'tgan", key: 'registered_at' },
]

function statusColor(s) {
  return { debtor: 'error', depositor: 'success', clear: 'grey' }[s] || 'grey'
}
function statusLabel(s) {
  return { debtor: 'Qarzdor', depositor: 'Depozit', clear: 'Toza' }[s] || s
}

async function fetchClients() {
  clientLoading.value = true
  try {
    const params = {}
    if (clientFilters.value.search) params.search = clientFilters.value.search
    if (clientFilters.value.filter !== 'all') params.filter = clientFilters.value.filter
    if (clientFilters.value.date_from) params.date_from = clientFilters.value.date_from
    if (clientFilters.value.date_to) params.date_to = clientFilters.value.date_to
    const res = await api.get('/admin/reports/clients', { params })
    clients.value = res.data.data.clients
    clientSummary.value = res.data.data.summary
  } finally {
    clientLoading.value = false
  }
}

// ── Invoices ─────────────────────────────────────────────
const invoiceLoading = ref(false)
const invoices = ref([])
const invoiceSummary = ref({
  total: 0, unpaid: 0, partial: 0, paid: 0, overdue: 0,
  total_amount: 0, total_paid: 0, total_balance: 0,
})
const invoiceFilters = ref({ status: null, date_from: '', date_to: '' })

const invoiceStatuses = [
  { title: "To'lanmagan", value: 'unpaid' },
  { title: 'Qisman', value: 'partial' },
  { title: "To'langan", value: 'paid' },
  { title: "Muddati o'tgan", value: 'overdue' },
]

const invoiceHeaders = [
  { title: 'Invoice #', key: 'invoice_number' },
  { title: 'Mijoz', key: 'client' },
  { title: 'Sana', key: 'created_at' },
  { title: 'Muddat', key: 'due_date' },
  { title: 'Jami', key: 'total_amount', align: 'end' },
  { title: "To'langan", key: 'paid_amount', align: 'end' },
  { title: 'Qoldiq', key: 'balance', align: 'end' },
  { title: 'Status', key: 'status' },
]

function invStatusColor(s) {
  return { unpaid: 'warning', partial: 'info', paid: 'success', overdue: 'error' }[s] || 'grey'
}
function invStatusLabel(s) {
  return { unpaid: "To'lanmagan", partial: 'Qisman', paid: "To'langan", overdue: "Muddati o'tgan" }[s] || s
}

async function fetchInvoices() {
  invoiceLoading.value = true
  try {
    const params = {}
    if (invoiceFilters.value.status) params.status = invoiceFilters.value.status
    if (invoiceFilters.value.date_from) params.date_from = invoiceFilters.value.date_from
    if (invoiceFilters.value.date_to) params.date_to = invoiceFilters.value.date_to
    const res = await api.get('/admin/reports/invoices', { params })
    invoices.value = res.data.data.invoices
    invoiceSummary.value = res.data.data.summary
  } finally {
    invoiceLoading.value = false
  }
}

function fmt(val) {
  return Number(val || 0).toLocaleString('uz-UZ') + ' so\'m'
}

onMounted(() => {
  fetchClients()
  fetchInvoices()
})
</script>
