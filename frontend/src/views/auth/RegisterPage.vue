<template>
  <v-card rounded="xl" class="pa-2">
    <v-card-text class="pa-6">
      <div class="text-h6 font-weight-bold text-primary mb-2">Ro'yhatdan o'tish</div>
      <div class="text-caption text-medium-emphasis mb-6">Turistik firma sifatida ro'yhatdan o'ting</div>

      <v-stepper v-model="step" :items="stepItems" hide-actions flat>
        <template #item.1>
          <v-form @submit.prevent="step = 2">
            <v-text-field v-model="form.name" label="To'liq ism" prepend-inner-icon="mdi-account" :error-messages="errors.name" class="mb-2" />
            <v-text-field v-model="form.company_name" label="Kompaniya nomi" prepend-inner-icon="mdi-domain" :error-messages="errors.company_name" class="mb-2" />
            <v-text-field v-model="form.email" label="Email" type="email" prepend-inner-icon="mdi-email" :error-messages="errors.email" class="mb-2" />
            <v-text-field v-model="form.phone" label="Telefon" prepend-inner-icon="mdi-phone" :error-messages="errors.phone" class="mb-2" />
            <v-text-field v-model="form.password" label="Parol" type="password" prepend-inner-icon="mdi-lock" :error-messages="errors.password" class="mb-2" />
            <v-text-field v-model="form.password_confirmation" label="Parolni tasdiqlang" type="password" prepend-inner-icon="mdi-lock-check" class="mb-4" />
            <v-btn color="primary" block @click="step = 2">Davom etish</v-btn>
          </v-form>
        </template>

        <template #item.2>
          <v-form @submit.prevent="step = 3">
            <v-text-field v-model="form.director_name" label="Direktor ismi" prepend-inner-icon="mdi-account-tie" :error-messages="errors.director_name" class="mb-2" />
            <v-text-field v-model="form.inn" label="INN/STIR" prepend-inner-icon="mdi-identifier" :error-messages="errors.inn" class="mb-2" />
            <v-text-field v-model="form.bank_name" label="Bank nomi" prepend-inner-icon="mdi-bank" :error-messages="errors.bank_name" class="mb-2" />
            <v-text-field v-model="form.mfo" label="MFO" prepend-inner-icon="mdi-numeric" :error-messages="errors.mfo" class="mb-2" />
            <v-text-field v-model="form.bank_account" label="Hisob raqam" prepend-inner-icon="mdi-credit-card" :error-messages="errors.bank_account" class="mb-2" />
            <v-text-field v-model="form.address" label="Manzil" prepend-inner-icon="mdi-map-marker" :error-messages="errors.address" class="mb-4" />
            <v-row>
              <v-col><v-btn variant="outlined" block @click="step = 1">Orqaga</v-btn></v-col>
              <v-col><v-btn color="primary" block @click="step = 3">Davom etish</v-btn></v-col>
            </v-row>
          </v-form>
        </template>

        <template #item.3>
          <v-card variant="tonal" color="primary" class="mb-4 pa-4">
            <div class="text-body-2">
              <div><strong>Kompaniya:</strong> {{ form.company_name }}</div>
              <div><strong>Email:</strong> {{ form.email }}</div>
              <div><strong>INN:</strong> {{ form.inn }}</div>
              <div><strong>Bank:</strong> {{ form.bank_name }}</div>
            </div>
          </v-card>

          <v-checkbox v-model="agreed" label="Barcha ma'lumotlar to'g'ri ekanligini tasdiqlayman" color="primary" />

          <v-alert v-if="errorMessage" type="error" variant="tonal" class="mb-4" rounded="lg">
            {{ errorMessage }}
          </v-alert>

          <v-alert v-if="successMessage" type="success" variant="tonal" class="mb-4" rounded="lg">
            {{ successMessage }}
          </v-alert>

          <v-row>
            <v-col><v-btn variant="outlined" block @click="step = 2">Orqaga</v-btn></v-col>
            <v-col>
              <v-btn
                color="primary"
                block
                :loading="loading"
                :disabled="!agreed"
                @click="handleRegister"
              >
                Yuborish
              </v-btn>
            </v-col>
          </v-row>
        </template>
      </v-stepper>

      <div class="text-center mt-4">
        <span class="text-body-2 text-medium-emphasis">Allaqachon hisobingiz bormi? </span>
        <v-btn variant="text" color="accent" size="small" to="/login">Kirish</v-btn>
      </div>
    </v-card-text>
  </v-card>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const step = ref(1)
const agreed = ref(false)
const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const errors = ref({})

const stepItems = [
  { title: 'Asosiy', value: 1 },
  { title: 'Rekvizitlar', value: 2 },
  { title: 'Tasdiqlash', value: 3 },
]

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
    successMessage.value = 'Arizangiz qabul qilindi! Admin tasdiqlashini kuting.'
  } catch (err) {
    if (err.response?.data?.errors) {
      errors.value = err.response.data.errors
      step.value = 1
    } else {
      errorMessage.value = err.response?.data?.message || 'Xato yuz berdi'
    }
  } finally {
    loading.value = false
  }
}
</script>
