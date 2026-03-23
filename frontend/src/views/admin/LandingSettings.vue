<template>
  <div>
    <div class="text-h5 font-weight-bold text-primary mb-6">Landing Page Sozlamalari</div>

    <v-tabs v-model="tab" color="accent" class="mb-6">
      <v-tab value="requisites">
        <v-icon start size="small">mdi-bank</v-icon>
        Rekvizitlar (PDF)
      </v-tab>
      <v-tab value="main">Asosiy</v-tab>
      <v-tab value="about">Haqimizda</v-tab>
      <v-tab value="contact">Kontakt</v-tab>
      <v-tab value="features">Xususiyatlar</v-tab>
    </v-tabs>

    <v-window v-model="tab">
      <!-- Requisites (PDF) -->
      <v-window-item value="requisites">
        <v-card class="pa-4">
          <v-alert type="info" variant="tonal" rounded="lg" class="mb-4">
            Bu ma'lumotlar invoice PDF da ko'rinadi. To'ldiring va saqlang.
          </v-alert>
          <v-row>
            <v-col cols="12" md="6">
              <div class="text-subtitle-2 text-primary font-weight-bold mb-3">Restoran ma'lumotlari</div>
              <v-text-field v-model="settings.restaurant_name" label="Restoran nomi" prepend-inner-icon="mdi-store" class="mb-2" />
              <v-text-field v-model="settings.restaurant_address" label="Manzil" prepend-inner-icon="mdi-map-marker" class="mb-2" />
              <v-text-field v-model="settings.restaurant_phone" label="Telefon" prepend-inner-icon="mdi-phone" class="mb-2" />
              <v-text-field v-model="settings.restaurant_email" label="Email" prepend-inner-icon="mdi-email" class="mb-2" />
              <v-text-field v-model="settings.restaurant_inn" label="INN/STIR" prepend-inner-icon="mdi-identifier" class="mb-2" />
              <v-text-field v-model="settings.restaurant_website" label="Veb sayt" prepend-inner-icon="mdi-web" class="mb-2" />
            </v-col>
            <v-col cols="12" md="6">
              <div class="text-subtitle-2 text-primary font-weight-bold mb-3">Bank rekvizitlari</div>
              <v-text-field v-model="settings.restaurant_bank" label="Bank nomi" prepend-inner-icon="mdi-bank" class="mb-2" />
              <v-text-field v-model="settings.restaurant_mfo" label="MFO" prepend-inner-icon="mdi-numeric" class="mb-2" />
              <v-text-field v-model="settings.restaurant_account" label="Hisob raqam" prepend-inner-icon="mdi-credit-card" class="mb-2" />
            </v-col>
          </v-row>
          <v-btn color="primary" :loading="saving" @click="saveSettings">
            <v-icon start>mdi-content-save</v-icon>
            Saqlash
          </v-btn>
        </v-card>
      </v-window-item>

      <!-- Main -->
      <v-window-item value="main">
        <v-card class="pa-4">
          <v-text-field v-model="settings.hero_title" label="Hero sarlavha" class="mb-3" />
          <v-text-field v-model="settings.hero_subtitle" label="Hero subtitle" class="mb-3" />
          <v-text-field v-model="settings.partnership_title" label="Hamkorlik sarlavhasi" class="mb-3" />
          <v-textarea v-model="settings.partnership_text" label="Hamkorlik matni" rows="3" class="mb-4" />

          <!-- Brand text -->
          <div class="text-subtitle-2 font-weight-bold mb-2">Brend nomi (navbar va footer)</div>
          <v-row dense class="mb-4">
            <v-col cols="12" md="6">
              <v-text-field v-model="settings.brand_name" label="Asosiy nom (masalan: OLDKHIVA)" density="compact" />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field v-model="settings.brand_tagline" label="Qo'shimcha (masalan: RESTAURANT)" density="compact" />
            </v-col>
          </v-row>

          <!-- Logo -->
          <div class="text-subtitle-2 font-weight-bold mb-2">Logo</div>
          <div v-if="settings.logo_image" class="d-flex align-center gap-3 mb-3">
            <v-img :src="`/storage/${settings.logo_image}`" height="60" max-width="200" contain class="bg-grey-lighten-4 rounded-lg flex-grow-0" style="border: 1px solid #eee;" />
            <v-btn color="error" variant="tonal" size="small" :loading="removingLogo" @click="removeLogo">
              <v-icon start size="small">mdi-delete</v-icon>O'chirish
            </v-btn>
          </div>
          <v-file-input v-model="logoImageFile" label="Logo yuklash (PNG/SVG)" accept="image/*" prepend-icon="mdi-image" class="mb-2" />
          <v-btn color="secondary" :loading="uploadingLogo" @click="uploadLogoImage" class="mb-4">Logo yuklash</v-btn>

          <!-- Hero image -->
          <div class="text-subtitle-2 font-weight-bold mb-2">Hero rasm</div>
          <v-img
            v-if="settings.hero_image"
            :src="`/storage/${settings.hero_image}`"
            height="160"
            cover
            rounded="lg"
            class="mb-3"
          />
          <v-file-input v-model="heroImageFile" label="Hero rasm yuklash" accept="image/*" prepend-icon="mdi-image" class="mb-3" />
          <v-row>
            <v-col>
              <v-btn color="secondary" :loading="uploadingHero" @click="uploadHeroImage">Rasmni yuklash</v-btn>
            </v-col>
            <v-col class="text-right">
              <v-btn color="primary" :loading="saving" @click="saveSettings">Saqlash</v-btn>
            </v-col>
          </v-row>
        </v-card>
      </v-window-item>

      <!-- About -->
      <v-window-item value="about">
        <v-card class="pa-4">
          <v-textarea v-model="settings.about_text" label="Haqimizda matni" rows="6" class="mb-4" />

          <!-- About image -->
          <div class="text-subtitle-2 font-weight-bold mb-2">Haqimizda rasmi</div>
          <v-img
            v-if="settings.about_image"
            :src="`/storage/${settings.about_image}`"
            height="200"
            cover
            rounded="lg"
            class="mb-3"
          />
          <v-file-input v-model="aboutImageFile" label="Rasm yuklash" accept="image/*" prepend-icon="mdi-image" class="mb-3" />
          <v-row>
            <v-col>
              <v-btn color="secondary" :loading="uploadingAbout" @click="uploadAboutImage">Rasmni yuklash</v-btn>
            </v-col>
            <v-col class="text-right">
              <v-btn color="primary" :loading="saving" @click="saveSettings">Saqlash</v-btn>
            </v-col>
          </v-row>
        </v-card>
      </v-window-item>

      <!-- Contact -->
      <v-window-item value="contact">
        <v-card class="pa-4">
          <v-text-field v-model="settings.contact_phone" label="Telefon" class="mb-3" />
          <v-text-field v-model="settings.contact_email" label="Email" class="mb-3" />
          <v-text-field v-model="settings.contact_address" label="Manzil" class="mb-4" />
          <v-btn color="primary" :loading="saving" @click="saveSettings">Saqlash</v-btn>
        </v-card>
      </v-window-item>

      <!-- Features -->
      <v-window-item value="features">
        <v-card class="pa-4">
          <div v-for="(feature, idx) in features" :key="idx" class="mb-4 pa-3" style="border: 1px solid #eee; border-radius: 8px;">
            <div class="text-caption mb-2">Xususiyat {{ idx + 1 }}</div>
            <v-row dense>
              <v-col cols="12" md="4">
                <v-text-field v-model="feature.icon" label="Icon (mdi-...)" density="compact" />
              </v-col>
              <v-col cols="12" md="4">
                <v-text-field v-model="feature.title" label="Sarlavha" density="compact" />
              </v-col>
              <v-col cols="12" md="4">
                <v-text-field v-model="feature.text" label="Matn" density="compact" />
              </v-col>
            </v-row>
          </div>
          <v-btn color="primary" :loading="saving" @click="saveFeaturesSettings">Saqlash</v-btn>
        </v-card>
      </v-window-item>
    </v-window>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useNotificationStore } from '@/stores/notification'
import api from '@/plugins/axios'

const notification = useNotificationStore()
const tab = ref('main')
const saving = ref(false)
const uploadingHero = ref(false)
const uploadingAbout = ref(false)
const uploadingLogo = ref(false)
const removingLogo = ref(false)
const heroImageFile = ref(null)
const aboutImageFile = ref(null)
const logoImageFile = ref(null)
const settings = ref({})
const features = ref([])

async function saveSettings() {
  saving.value = true
  try {
    const settingsToSave = Object.entries(settings.value)
      .filter(([key]) => !['features', 'gallery_images'].includes(key))
      .map(([key, value]) => ({ key, value }))

    await api.post('/admin/landing-settings', { settings: settingsToSave })
    notification.showSuccess('Sozlamalar saqlandi!')
  } catch {
    notification.showError('Xato yuz berdi')
  } finally {
    saving.value = false
  }
}

async function saveFeaturesSettings() {
  saving.value = true
  try {
    await api.post('/admin/landing-settings', {
      settings: [{ key: 'features', value: JSON.stringify(features.value) }]
    })
    notification.showSuccess('Xususiyatlar saqlandi!')
  } catch {
    notification.showError('Xato yuz berdi')
  } finally {
    saving.value = false
  }
}

async function doUpload(key, fileRaw, loadingRef) {
  const file = Array.isArray(fileRaw) ? fileRaw[0] : fileRaw
  if (!file) { notification.showError('Avval rasm tanlang'); return }
  loadingRef.value = true
  try {
    const formData = new FormData()
    formData.append('key', key)
    formData.append('image', file)
    const res = await api.post('/admin/landing-settings/upload-image', formData)
    settings.value[key] = res.data.data.path
    notification.showSuccess('Rasm yuklandi!')
  } catch {
    notification.showError('Xato yuz berdi')
  } finally {
    loadingRef.value = false
  }
}

function uploadHeroImage() {
  doUpload('hero_image', heroImageFile.value, uploadingHero)
}

function uploadAboutImage() {
  doUpload('about_image', aboutImageFile.value, uploadingAbout)
}

function uploadLogoImage() {
  doUpload('logo_image', logoImageFile.value, uploadingLogo)
}

async function removeLogo() {
  removingLogo.value = true
  try {
    await api.post('/admin/landing-settings', {
      settings: [{ key: 'logo_image', value: '' }]
    })
    settings.value.logo_image = ''
    notification.showSuccess('Logo o\'chirildi')
  } catch {
    notification.showError('Xato yuz berdi')
  } finally {
    removingLogo.value = false
  }
}

onMounted(async () => {
  const res = await api.get('/admin/landing-settings')
  const settingsList = res.data.data
  settingsList.forEach(s => {
    settings.value[s.key] = s.value
  })
  try {
    features.value = JSON.parse(settings.value.features || '[]')
  } catch {
    features.value = []
  }
})
</script>
