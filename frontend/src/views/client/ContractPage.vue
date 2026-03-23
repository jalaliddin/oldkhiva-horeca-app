<template>
  <div>
    <div class="text-h5 font-weight-bold text-primary mb-6">Shartnoma</div>

    <div v-if="loading" class="d-flex justify-center pa-10">
      <v-progress-circular indeterminate color="primary" />
    </div>

    <template v-else>
      <v-alert v-if="user?.contract_agreed" type="success" variant="tonal" rounded="xl" class="mb-6">
        <div class="text-body-1 font-weight-medium">Shartnoma imzolangan</div>
        <div class="text-caption mt-1">Siz shartnomani muvaffaqiyatli imzologansiz.</div>
      </v-alert>

      <v-card v-if="!user?.contract_agreed" color="warning" variant="tonal" class="mb-6 pa-4">
        <div class="d-flex align-center">
          <v-icon class="mr-3">mdi-alert-circle</v-icon>
          <div>Menyu va bron funksiyalaridan foydalanish uchun shartnomani imzolashingiz kerak.</div>
        </div>
      </v-card>

      <v-card v-if="contract" class="mb-6">
        <v-card-title class="pa-4 text-primary">{{ contract.title }}</v-card-title>
        <v-card-text>
          <p class="text-body-2 text-medium-emphasis mb-4">{{ contract.description }}</p>
          <v-btn
            color="primary"
            prepend-icon="mdi-download"
            :loading="downloading"
            @click="downloadContract"
          >
            Shartnomani yuklab olish
          </v-btn>
        </v-card-text>
      </v-card>

      <v-card v-if="!user?.contract_agreed && contract">
        <v-card-title class="pa-4 text-primary">Shartnomani tasdiqlash</v-card-title>
        <v-card-text>
          <v-checkbox
            v-model="agreed"
            color="primary"
            label="Men shartnoma shartlarini o'qidim va roziman"
          />
          <v-btn
            color="primary"
            :loading="saving"
            :disabled="!agreed"
            @click="agreeContract"
          >
            <v-icon start>mdi-check-circle</v-icon>
            Shartnomani tasdiqlash
          </v-btn>
        </v-card-text>
      </v-card>

      <v-card v-if="!contract" variant="tonal" color="info" class="pa-6 text-center">
        <v-icon size="48" class="mb-3">mdi-file-document-outline</v-icon>
        <div>Hozircha faol shartnoma mavjud emas. Admin bilan bog'laning.</div>
      </v-card>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useNotificationStore } from '@/stores/notification'
import api from '@/plugins/axios'

const auth = useAuthStore()
const notification = useNotificationStore()
const user = ref(auth.user)
const contract = ref(null)
const loading = ref(true)
const saving = ref(false)
const downloading = ref(false)
const agreed = ref(false)

onMounted(async () => {
  try {
    const res = await api.get('/contracts/active')
    contract.value = res.data.data
  } finally {
    loading.value = false
  }
})

async function downloadContract() {
  downloading.value = true
  try {
    const res = await api.get('/contracts/download', { responseType: 'blob' })
    const url = URL.createObjectURL(new Blob([res.data], { type: 'application/pdf' }))
    const link = document.createElement('a')
    link.href = url
    link.download = `Shartnoma-${contract.value?.title || 'OldKhiva'}.pdf`
    link.click()
    URL.revokeObjectURL(url)
  } catch {
    notification.showError('Yuklab olishda xato yuz berdi')
  } finally {
    downloading.value = false
  }
}

async function agreeContract() {
  saving.value = true
  try {
    await api.post('/contracts/agree')
    auth.updateUser({ contract_agreed: true })
    user.value = auth.user
    notification.showSuccess('Shartnoma muvaffaqiyatli imzolandi!')
  } catch {
    notification.showError('Xato yuz berdi')
  } finally {
    saving.value = false
  }
}
</script>
