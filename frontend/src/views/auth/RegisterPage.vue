<template>
  <v-card rounded="xl" class="pa-2">
    <v-card-text class="pa-6">
      <div class="d-flex align-center justify-space-between mb-2">
        <div class="text-h6 font-weight-bold text-primary">{{ i18n.t('register.title') }}</div>
        <LanguageSwitcher btn-variant="outlined" btn-color="primary" />
      </div>
      <div class="text-caption text-medium-emphasis mb-6">{{ i18n.t('register.subtitle') }}</div>

      <v-stepper v-model="step" :items="stepItems" hide-actions flat>
        <template #item.1>
          <v-form @submit.prevent="step = 2">
            <v-text-field v-model="form.name" :label="i18n.t('register.fullName')" prepend-inner-icon="mdi-account" :error-messages="errors.name" class="mb-2" />
            <v-text-field v-model="form.company_name" :label="i18n.t('register.companyName')" prepend-inner-icon="mdi-domain" :error-messages="errors.company_name" class="mb-2" />
            <v-text-field v-model="form.email" label="Email" type="email" prepend-inner-icon="mdi-email" :error-messages="errors.email" class="mb-2" />
            <v-text-field v-model="form.phone" :label="i18n.t('register.phone')" prepend-inner-icon="mdi-phone" :error-messages="errors.phone" class="mb-2" />
            <v-text-field v-model="form.password" :label="i18n.t('register.password')" type="password" prepend-inner-icon="mdi-lock" :error-messages="errors.password" class="mb-2" />
            <v-text-field v-model="form.password_confirmation" :label="i18n.t('register.confirmPassword')" type="password" prepend-inner-icon="mdi-lock-check" class="mb-4" />
            <v-btn color="primary" block @click="step = 2">{{ i18n.t('common.next') }}</v-btn>
          </v-form>
        </template>

        <template #item.2>
          <v-form @submit.prevent="step = 3">
            <v-text-field v-model="form.director_name" :label="i18n.t('register.directorName')" prepend-inner-icon="mdi-account-tie" :error-messages="errors.director_name" class="mb-2" />
            <v-text-field v-model="form.inn" :label="i18n.t('register.inn')" prepend-inner-icon="mdi-identifier" :error-messages="errors.inn" class="mb-2" />
            <v-text-field v-model="form.bank_name" :label="i18n.t('register.bankName')" prepend-inner-icon="mdi-bank" :error-messages="errors.bank_name" class="mb-2" />
            <v-text-field v-model="form.mfo" :label="i18n.t('register.mfo')" prepend-inner-icon="mdi-numeric" :error-messages="errors.mfo" class="mb-2" />
            <v-text-field v-model="form.bank_account" :label="i18n.t('register.bankAccount')" prepend-inner-icon="mdi-credit-card" :error-messages="errors.bank_account" class="mb-2" />
            <v-text-field v-model="form.address" :label="i18n.t('register.address')" prepend-inner-icon="mdi-map-marker" :error-messages="errors.address" class="mb-4" />
            <v-row>
              <v-col><v-btn variant="outlined" block @click="step = 1">{{ i18n.t('common.back') }}</v-btn></v-col>
              <v-col><v-btn color="primary" block @click="step = 3">{{ i18n.t('common.next') }}</v-btn></v-col>
            </v-row>
          </v-form>
        </template>

        <template #item.3>
          <v-card variant="tonal" color="primary" class="mb-4 pa-4">
            <div class="text-body-2">
              <div><strong>{{ i18n.t('register.company') }}:</strong> {{ form.company_name }}</div>
              <div><strong>Email:</strong> {{ form.email }}</div>
              <div><strong>{{ i18n.t('register.inn') }}:</strong> {{ form.inn }}</div>
              <div><strong>{{ i18n.t('register.bankName') }}:</strong> {{ form.bank_name }}</div>
            </div>
          </v-card>

          <v-checkbox v-model="agreed" :label="i18n.t('register.confirmData')" color="primary" />

          <v-alert v-if="errorMessage" type="error" variant="tonal" class="mb-4" rounded="lg">
            {{ errorMessage }}
          </v-alert>

          <v-alert v-if="successMessage" type="success" variant="tonal" class="mb-4" rounded="lg">
            {{ successMessage }}
          </v-alert>

          <v-row>
            <v-col><v-btn variant="outlined" block @click="step = 2">{{ i18n.t('common.back') }}</v-btn></v-col>
            <v-col>
              <v-btn
                color="primary"
                block
                :loading="loading"
                :disabled="!agreed"
                @click="handleRegister"
              >
                {{ i18n.t('common.submit') }}
              </v-btn>
            </v-col>
          </v-row>
        </template>
      </v-stepper>

      <div class="text-center mt-4">
        <span class="text-body-2 text-medium-emphasis">{{ i18n.t('register.hasAccount') }} </span>
        <v-btn variant="text" color="accent" size="small" to="/login">{{ i18n.t('register.login') }}</v-btn>
      </div>
    </v-card-text>
  </v-card>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useI18nStore } from '@/stores/i18n'
import LanguageSwitcher from '@/components/LanguageSwitcher.vue'

const auth = useAuthStore()
const i18n = useI18nStore()
const step = ref(1)
const agreed = ref(false)
const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const errors = ref({})

const stepItems = computed(() => [
  { title: i18n.t('register.step1'), value: 1 },
  { title: i18n.t('register.step2'), value: 2 },
  { title: i18n.t('register.step3'), value: 3 },
])

const form = ref({
  name: '', email: '', password: '', password_confirmation: '',
  company_name: '', phone: '', director_name: '',
  inn: '', bank_name: '', mfo: '', bank_account: '', address: '',
})

async function handleRegister() {
  loading.value = true
  errorMessage.value = ''
  errors.value = {}
  try {
    await auth.register(form.value)
    successMessage.value = i18n.t('register.successMsg')
  } catch (err) {
    if (err.response?.data?.errors) {
      errors.value = err.response.data.errors
      step.value = 1
    } else {
      errorMessage.value = err.response?.data?.message || i18n.t('register.error')
    }
  } finally {
    loading.value = false
  }
}
</script>
