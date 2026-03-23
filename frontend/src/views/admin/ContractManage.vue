<template>
  <div>
    <div class="text-h5 font-weight-bold text-primary mb-6">Shartnomalar</div>

    <v-row>
      <v-col cols="12" md="6">
        <v-card class="mb-4">
          <v-card-title class="pa-4 text-primary">Yangi shartnoma yuklash</v-card-title>
          <v-card-text>
            <v-text-field v-model="form.title" label="Sarlavha" class="mb-3" />
            <v-textarea v-model="form.description" label="Tavsif" rows="3" class="mb-3" />
            <v-file-input v-model="form.file" label="PDF fayl" accept=".pdf" prepend-icon="mdi-paperclip" class="mb-4" />
            <v-btn color="primary" block :loading="uploading" @click="uploadContract">
              <v-icon start>mdi-upload</v-icon>
              Yuklash
            </v-btn>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="6">
        <v-card>
          <v-card-title class="pa-4 text-primary">Mavjud shartnomalar</v-card-title>
          <v-list>
            <v-list-item
              v-for="contract in contracts"
              :key="contract.id"
              :title="contract.title"
              :subtitle="contract.created_at?.split('T')[0]"
            >
              <template #append>
                <v-chip v-if="contract.is_active" color="success" size="small" class="mr-2">Faol</v-chip>
                <v-btn v-else size="small" variant="outlined" @click="activateContract(contract.id)">
                  Faollashtirish
                </v-btn>
              </template>
            </v-list-item>
          </v-list>
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
const contracts = ref([])
const uploading = ref(false)
const form = ref({ title: '', description: '', file: null })

async function uploadContract() {
  if (!form.value.file || !form.value.title) return
  uploading.value = true
  try {
    const formData = new FormData()
    formData.append('title', form.value.title)
    formData.append('description', form.value.description)
    formData.append('file', form.value.file[0] || form.value.file)
    await api.post('/admin/contracts', formData)
    notification.showSuccess('Shartnoma yuklandi!')
    form.value = { title: '', description: '', file: null }
    await fetchContracts()
  } catch {
    notification.showError('Xato yuz berdi')
  } finally {
    uploading.value = false
  }
}

async function activateContract(id) {
  await api.put(`/admin/contracts/${id}/activate`)
  notification.showSuccess('Shartnoma faollashtirildi!')
  await fetchContracts()
}

async function fetchContracts() {
  const res = await api.get('/admin/contracts')
  contracts.value = res.data.data
}

onMounted(fetchContracts)
</script>
