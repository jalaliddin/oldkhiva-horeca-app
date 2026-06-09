<template>
  <div>
    <div class="text-h5 font-weight-bold text-primary mb-6">{{ i18n.t('services.title') }}</div>
    <v-row>
      <v-col cols="12" md="5">
        <v-card class="pa-4">
          <v-card-title class="pa-0 mb-4 text-primary">{{ editing ? i18n.t('services.edit') : i18n.t('services.newService') }}</v-card-title>
          <v-text-field v-model="form.name" :label="i18n.t('common.name')" class="mb-2" />
          <v-textarea v-model="form.description" :label="i18n.t('services.description')" rows="2" class="mb-2" />
          <v-text-field v-model.number="form.price" :label="i18n.t('services.priceLabel')" type="number" class="mb-2" />
          <v-select v-model="form.unit" :label="i18n.t('services.unit')" :items="['hour', 'event', 'person', 'day']" class="mb-3" />
          <v-switch v-model="form.is_active" :label="i18n.t('services.active')" color="primary" class="mb-3" />
          <v-btn color="primary" block @click="save">{{ i18n.t('common.save') }}</v-btn>
          <v-btn v-if="editing" variant="outlined" block class="mt-2" @click="cancelEdit">{{ i18n.t('common.cancel') }}</v-btn>
        </v-card>
      </v-col>
      <v-col cols="12" md="7">
        <v-card>
          <v-data-table :headers="headers" :items="services" :loading="loading" density="compact">
            <template #item.price="{ item }">{{ Number(item.price).toLocaleString() }} {{ i18n.t('common.currency') }}</template>
            <template #item.is_active="{ item }">
              <v-chip :color="item.is_active ? 'success' : 'grey'" size="x-small" variant="tonal">{{ item.is_active ? i18n.t('services.active') : i18n.t('services.inactive') }}</v-chip>
            </template>
            <template #item.actions="{ item }">
              <v-btn size="small" icon="mdi-pencil" variant="text" @click="editService(item)" />
              <v-btn size="small" icon="mdi-delete" variant="text" color="error" @click="deleteService(item.id)" />
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useNotificationStore } from '@/stores/notification'
import { useI18nStore } from '@/stores/i18n'
import api from '@/plugins/axios'

const notification = useNotificationStore()
const i18n = useI18nStore()
const services = ref([])
const loading = ref(true)
const editing = ref(null)
const form = ref({ name: '', description: '', price: 0, unit: 'event', is_active: true })

const headers = computed(() => [
  { title: i18n.t('common.name'), key: 'name' },
  { title: i18n.t('common.price'), key: 'price' },
  { title: i18n.t('services.unit'), key: 'unit' },
  { title: i18n.t('common.status'), key: 'is_active' },
  { title: '', key: 'actions', sortable: false },
])

function editService(s) {
  editing.value = s.id
  form.value = { name: s.name, description: s.description, price: s.price, unit: s.unit, is_active: s.is_active }
}

function cancelEdit() {
  editing.value = null
  form.value = { name: '', description: '', price: 0, unit: 'event', is_active: true }
}

async function save() {
  try {
    if (editing.value) {
      await api.put(`/admin/services/${editing.value}`, form.value)
      notification.showSuccess(i18n.t('services.updated'))
    } else {
      await api.post('/admin/services', form.value)
      notification.showSuccess(i18n.t('services.added'))
    }
    cancelEdit()
    await fetch()
  } catch {
    notification.showError(i18n.t('register.error'))
  }
}

async function deleteService(id) {
  await api.delete(`/admin/services/${id}`)
  notification.showSuccess(i18n.t('services.deleted'))
  await fetch()
}

async function fetch() {
  loading.value = true
  const res = await api.get('/admin/services')
  services.value = res.data.data
  loading.value = false
}

onMounted(fetch)
</script>
