<template>
  <div>
    <div class="text-h5 font-weight-bold text-primary mb-6">Xizmatlar</div>
    <v-row>
      <v-col cols="12" md="5">
        <v-card class="pa-4">
          <v-card-title class="pa-0 mb-4 text-primary">{{ editing ? 'Tahrirlash' : 'Yangi xizmat' }}</v-card-title>
          <v-text-field v-model="form.name" label="Nomi" class="mb-2" />
          <v-textarea v-model="form.description" label="Tavsif" rows="2" class="mb-2" />
          <v-text-field v-model.number="form.price" label="Narxi (so'm)" type="number" class="mb-2" />
          <v-select v-model="form.unit" label="O'lchov" :items="['hour', 'event', 'person', 'day']" class="mb-3" />
          <v-switch v-model="form.is_active" label="Faol" color="primary" class="mb-3" />
          <v-btn color="primary" block @click="save">Saqlash</v-btn>
          <v-btn v-if="editing" variant="outlined" block class="mt-2" @click="cancelEdit">Bekor</v-btn>
        </v-card>
      </v-col>
      <v-col cols="12" md="7">
        <v-card>
          <v-data-table :headers="headers" :items="services" :loading="loading" density="compact">
            <template #item.price="{ item }">{{ Number(item.price).toLocaleString() }} so'm</template>
            <template #item.is_active="{ item }">
              <v-chip :color="item.is_active ? 'success' : 'grey'" size="x-small" variant="tonal">{{ item.is_active ? 'Faol' : 'Nofaol' }}</v-chip>
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
import { ref, onMounted } from 'vue'
import { useNotificationStore } from '@/stores/notification'
import api from '@/plugins/axios'

const notification = useNotificationStore()
const services = ref([])
const loading = ref(true)
const editing = ref(null)
const form = ref({ name: '', description: '', price: 0, unit: 'event', is_active: true })

const headers = [
  { title: 'Nomi', key: 'name' },
  { title: 'Narxi', key: 'price' },
  { title: "O'lchov", key: 'unit' },
  { title: 'Status', key: 'is_active' },
  { title: '', key: 'actions', sortable: false },
]

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
      notification.showSuccess('Xizmat yangilandi!')
    } else {
      await api.post('/admin/services', form.value)
      notification.showSuccess("Xizmat qo'shildi!")
    }
    cancelEdit()
    await fetch()
  } catch {
    notification.showError('Xato yuz berdi')
  }
}

async function deleteService(id) {
  await api.delete(`/admin/services/${id}`)
  notification.showSuccess("Xizmat o'chirildi!")
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
