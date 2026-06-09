<template>
  <div>
    <div class="text-h5 font-weight-bold text-primary mb-6">{{ i18n.t('contract.title') }}</div>

    <div v-if="loading" class="d-flex justify-center pa-10">
      <v-progress-circular indeterminate color="primary" />
    </div>

    <template v-else>
      <v-alert v-if="user?.contract_agreed" type="success" variant="tonal" rounded="xl" class="mb-6">
        <div class="text-body-1 font-weight-medium">{{ i18n.t('contract.signed') }}</div>
        <div class="text-caption mt-1">{{ i18n.t('contract.signedDesc') }}</div>
      </v-alert>

      <v-card v-if="!user?.contract_agreed" color="warning" variant="tonal" class="mb-6 pa-4">
        <div class="d-flex align-center">
          <v-icon class="mr-3">mdi-alert-circle</v-icon>
          <div>{{ i18n.t('contract.warningText') }}</div>
        </div>
      </v-card>

      <v-card v-if="contract" class="mb-6">
        <v-card-title class="pa-4 text-primary">{{ contract.title }}</v-card-title>
        <v-card-text>
          <p class="text-body-2 text-medium-emphasis mb-4">{{ contract.description }}</p>
          <v-btn color="primary" prepend-icon="mdi-download" :loading="downloading" @click="downloadContract">
            {{ i18n.t('contract.download') }}
          </v-btn>
        </v-card-text>
      </v-card>

      <v-card v-if="!user?.contract_agreed && contract">
        <v-card-title class="pa-4 text-primary">{{ i18n.t('contract.confirmTitle') }}</v-card-title>
        <v-card-text>
          <v-checkbox v-model="agreed" color="primary" :label="i18n.t('contract.agreeCheckbox')" />
          <v-btn color="primary" :loading="saving" :disabled="!agreed" @click="agreeContract">
            <v-icon start>mdi-check-circle</v-icon>
            {{ i18n.t('contract.confirmTitle') }}
          </v-btn>
        </v-card-text>
      </v-card>

      <v-card v-if="!contract" variant="tonal" color="info" class="pa-6 text-center">
        <v-icon size="48" class="mb-3">mdi-file-document-outline</v-icon>
        <div>{{ i18n.t('contract.noContract') }}</div>
      </v-card>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useNotificationStore } from '@/stores/notification'
import { useI18nStore } from '@/stores/i18n'
import api from '@/plugins/axios'

const auth = useAuthStore()
const notification = useNotificationStore()
const i18n = useI18nStore()
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
    notification.showError(i18n.t('contract.downloadError'))
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
    notification.showSuccess(i18n.t('contract.successMsg'))
  } catch {
    notification.showError(i18n.t('register.error'))
  } finally {
    saving.value = false
  }
}
</script>
