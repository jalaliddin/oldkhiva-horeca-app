<template>
  <div>
    <div class="text-h5 font-weight-bold text-primary mb-6">{{ i18n.t('clients.title') }}</div>

    <v-card class="mb-4">
      <v-card-text class="pb-0">
        <v-row>
          <v-col cols="12" md="6">
            <v-text-field v-model="search" :label="i18n.t('common.search')" prepend-inner-icon="mdi-magnify" clearable hide-details density="compact" @input="fetchClients" />
          </v-col>
          <v-col cols="12" md="3">
            <v-select v-model="statusFilter" :label="i18n.t('common.status')" :items="statusOptions" hide-details density="compact" clearable @update:modelValue="fetchClients" />
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <v-card>
      <v-data-table :headers="headers" :items="clients" :loading="loading">
        <template #item.is_active="{ item }">
          <v-chip :color="item.is_active ? 'success' : 'warning'" size="small" variant="tonal">
            {{ item.is_active ? i18n.t('bookingStatus.approved') : i18n.t('bookingStatus.pending') }}
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
import { ref, computed, onMounted } from 'vue'
import { useI18nStore } from '@/stores/i18n'
import api from '@/plugins/axios'

const i18n = useI18nStore()
const loading = ref(true)
const clients = ref([])
const search = ref('')
const statusFilter = ref(null)

const statusOptions = computed(() => [
  { title: i18n.t('common.all'), value: null },
  { title: i18n.t('bookingStatus.approved'), value: 'active' },
  { title: i18n.t('bookingStatus.pending'), value: 'inactive' },
])

const headers = computed(() => [
  { title: i18n.t('clients.company'), key: 'company_name' },
  { title: i18n.t('clients.name'), key: 'name' },
  { title: 'Email', key: 'email' },
  { title: i18n.t('clients.phone'), key: 'phone' },
  { title: i18n.t('common.status'), key: 'is_active' },
  { title: i18n.t('clients.contract'), key: 'contract_agreed' },
  { title: '', key: 'actions', sortable: false },
])

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
