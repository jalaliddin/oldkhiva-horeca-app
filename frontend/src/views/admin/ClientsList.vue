<template>
  <div>
    <div class="text-h5 font-weight-bold text-primary mb-6">Mijozlar</div>

    <v-card class="mb-4">
      <v-card-text class="pb-0">
        <v-row>
          <v-col cols="12" md="6">
            <v-text-field v-model="search" label="Qidirish" prepend-inner-icon="mdi-magnify" clearable hide-details density="compact" @input="fetchClients" />
          </v-col>
          <v-col cols="12" md="3">
            <v-select v-model="statusFilter" label="Status" :items="statusOptions" hide-details density="compact" clearable @update:modelValue="fetchClients" />
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <v-card>
      <v-data-table :headers="headers" :items="clients" :loading="loading">
        <template #item.is_active="{ item }">
          <v-chip :color="item.is_active ? 'success' : 'warning'" size="small" variant="tonal">
            {{ item.is_active ? 'Tasdiqlangan' : 'Kutilmoqda' }}
          </v-chip>
        </template>
        <template #item.contract_agreed="{ item }">
          <v-icon :color="item.contract_agreed ? 'success' : 'error'" size="small">
            {{ item.contract_agreed ? 'mdi-check-circle' : 'mdi-close-circle' }}
          </v-icon>
        </template>
        <template #item.actions="{ item }">
          <v-btn size="small" icon="mdi-eye" variant="text" :to="`/admin/clients/${item.id}`" />
        </template>
      </v-data-table>
    </v-card>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/plugins/axios'

const loading = ref(true)
const clients = ref([])
const search = ref('')
const statusFilter = ref(null)

const statusOptions = [
  { title: 'Barcha', value: null },
  { title: 'Tasdiqlangan', value: 'active' },
  { title: 'Kutilmoqda', value: 'inactive' },
]

const headers = [
  { title: 'Kompaniya', key: 'company_name' },
  { title: 'Ism', key: 'name' },
  { title: 'Email', key: 'email' },
  { title: 'Telefon', key: 'phone' },
  { title: 'Status', key: 'is_active' },
  { title: 'Shartnoma', key: 'contract_agreed' },
  { title: '', key: 'actions', sortable: false },
]

async function fetchClients() {
  loading.value = true
  try {
    const params = {}
    if (search.value) params.search = search.value
    if (statusFilter.value) params.status = statusFilter.value
    const res = await api.get('/admin/clients', { params })
    clients.value = res.data.data.data || []
  } finally {
    loading.value = false
  }
}

onMounted(fetchClients)
</script>
