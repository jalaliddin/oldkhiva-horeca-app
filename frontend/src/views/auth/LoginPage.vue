<template>
  <v-card rounded="xl" class="pa-2">
    <v-card-text class="pa-6">
      <div class="text-h6 font-weight-bold text-primary mb-6">Tizimga kirish</div>

      <v-form @submit.prevent="handleLogin">
        <v-text-field
          v-model="form.email"
          label="Email"
          type="email"
          prepend-inner-icon="mdi-email-outline"
          :error-messages="errors.email"
          class="mb-3"
        />
        <v-text-field
          v-model="form.password"
          label="Parol"
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
          Kirish
        </v-btn>
      </v-form>

      <div class="text-center mt-6">
        <span class="text-body-2 text-medium-emphasis">Hisobingiz yo'qmi? </span>
        <v-btn variant="text" color="accent" size="small" to="/register">
          Ro'yhatdan o'ting
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

const auth = useAuthStore()
const notification = useNotificationStore()
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
    notification.showSuccess('Muvaffaqiyatli kirdingiz!')
    const user = data.data.user
    router.push(user.role === 'admin' ? '/admin' : '/client')
  } catch (err) {
    if (err.response?.data?.errors) {
      errors.value = err.response.data.errors
    } else {
      errorMessage.value = err.response?.data?.message || 'Xato yuz berdi'
    }
  } finally {
    loading.value = false
  }
}
</script>
