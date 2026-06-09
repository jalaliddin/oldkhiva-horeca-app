<template>
  <v-card rounded="xl" class="pa-2">
    <v-card-text class="pa-6">
      <div class="d-flex align-center justify-space-between mb-6">
        <div class="text-h6 font-weight-bold text-primary">{{ i18n.t('login.title') }}</div>
        <LanguageSwitcher btn-variant="outlined" btn-color="primary" />
      </div>

      <v-form @submit.prevent="handleLogin">
        <v-text-field
          v-model="form.email"
          :label="i18n.t('login.email')"
          type="email"
          prepend-inner-icon="mdi-email-outline"
          :error-messages="errors.email"
          class="mb-3"
        />
        <v-text-field
          v-model="form.password"
          :label="i18n.t('login.password')"
          :type="showPassword ? 'text' : 'password'"
          prepend-inner-icon="mdi-lock-outline"
          :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
          @click:append-inner="showPassword = !showPassword"
          :error-messages="errors.password"
          class="mb-4"
        />

        <v-alert v-if="errorMessage" type="error" variant="tonal" class="mb-4" rounded="lg">
          {{ errorMessage }}
        </v-alert>

        <v-btn
          type="submit"
          color="primary"
          block
          size="large"
          :loading="loading"
        >
          {{ i18n.t('login.submit') }}
        </v-btn>
      </v-form>

      <div class="text-center mt-6">
        <span class="text-body-2 text-medium-emphasis">{{ i18n.t('login.noAccount') }} </span>
        <v-btn variant="text" color="accent" size="small" to="/register">
          {{ i18n.t('login.register') }}
        </v-btn>
      </div>
    </v-card-text>
  </v-card>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useNotificationStore } from '@/stores/notification'
import { useI18nStore } from '@/stores/i18n'
import LanguageSwitcher from '@/components/LanguageSwitcher.vue'

const auth = useAuthStore()
const notification = useNotificationStore()
const i18n = useI18nStore()
const router = useRouter()

const form = ref({ email: '', password: '' })
const errors = ref({})
const errorMessage = ref('')
const loading = ref(false)
const showPassword = ref(false)

async function handleLogin() {
  loading.value = true
  errors.value = {}
  errorMessage.value = ''
  try {
    const data = await auth.login(form.value)
    notification.showSuccess(i18n.t('login.success'))
    const user = data.data.user
    router.push(user.role === 'admin' ? '/admin' : '/client')
  } catch (err) {
    if (err.response?.data?.errors) {
      errors.value = err.response.data.errors
    } else {
      errorMessage.value = err.response?.data?.message || i18n.t('login.error')
    }
  } finally {
    loading.value = false
  }
}
</script>
