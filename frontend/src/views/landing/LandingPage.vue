<template>
  <div id="top">
    <!-- Navbar -->
    <v-app-bar color="primary" elevation="0" style="position: fixed; z-index: 100;">
      <v-app-bar-title>
        <div style="display: flex; align-items: center; gap: 10px;">
          <img v-if="settings?.logo_image" :src="`/storage/${settings.logo_image}`" alt="OldKhiva" style="height: 40px; object-fit: contain;" />
          <div style="display: flex; align-items: baseline; gap: 6px;">
            <span class="text-white font-weight-bold text-h6">{{ settings?.brand_name || 'OLDKHIVA' }}</span>
            <span class="text-caption" style="color: #C8941A;">{{ settings?.brand_tagline || 'RESTAURANT' }}</span>
          </div>
        </div>
      </v-app-bar-title>
      <v-spacer />
      <v-btn variant="text" color="white" href="#top">Bosh sahifa</v-btn>
      <v-btn variant="text" color="white" href="#menu">Menyu</v-btn>
      <v-btn variant="text" color="white" href="#about">Haqimizda</v-btn>
      <v-btn variant="text" color="white" href="#contact">Bog'lanish</v-btn>
      <v-btn variant="text" color="white" href="https://travel.oldkhiva.uz" target="_blank" rel="noopener">
        <v-icon start size="small">mdi-airplane</v-icon>Travel
      </v-btn>
      <v-btn variant="text" color="white" href="https://bicycle.oldkhiva.uz" target="_blank" rel="noopener">
        <v-icon start size="small">mdi-bicycle</v-icon>Bicycle
      </v-btn>
      <v-btn color="accent" to="/login" class="ml-2">Kirish</v-btn>
      <v-btn variant="outlined" color="white" to="/register" class="ml-2 mr-3">Ro'yxatdan o'tish</v-btn>
    </v-app-bar>

    <!-- Hero Section -->
    <section
      :style="{
        minHeight: '100vh',
        display: 'flex',
        alignItems: 'center',
        paddingTop: '64px',
        background: settings?.hero_image
          ? `linear-gradient(rgba(15,28,53,0.65), rgba(15,28,53,0.65)), url('/storage/${settings.hero_image}') center/cover no-repeat`
          : 'linear-gradient(135deg, #0F1C35 0%, #1A2744 60%, #2C3E6B 100%)'
      }"
    >
      <v-container>
        <v-row align="center" justify="center">
          <v-col cols="12" md="8" class="text-center">
            <div class="text-overline mb-4" style="color: #C8941A; letter-spacing: 4px;">XIVA, O'ZBEKISTON</div>
            <h1 class="text-white font-weight-bold mb-4" style="font-size: clamp(2.5rem, 6vw, 4.5rem); line-height: 1.1;">
              {{ settings?.hero_title || 'OldKhiva Restaurant' }}
            </h1>
            <p class="text-h6 mb-8" style="color: #B0BEC5; font-weight: 300;">
              {{ settings?.hero_subtitle || "Xiva tarixining ta'mi" }}
            </p>
            <v-btn color="accent" size="x-large" to="/register" class="mr-4">
              <v-icon start>mdi-handshake</v-icon>
              Hamkor bo'ling
            </v-btn>
            <v-btn variant="outlined" color="white" size="x-large" href="#menu">
              Menyu ko'rish
            </v-btn>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- Features Section -->
    <section class="py-16" style="background: #F5F6FA;">
      <v-container>
        <div class="text-center mb-12">
          <div class="text-overline" style="color: #C8941A;">AFZALLIKLARIMIZ</div>
          <h2 class="text-h4 font-weight-bold text-primary mt-2">Nima uchun OldKhiva?</h2>
        </div>
        <v-row>
          <v-col v-for="feature in features" :key="feature.title" cols="12" md="4">
            <v-card height="100%" class="pa-6 text-center">
              <v-icon :icon="feature.icon" size="56" color="accent" class="mb-4" />
              <v-card-title class="text-h6 text-primary">{{ feature.title }}</v-card-title>
              <v-card-text class="text-body-2">{{ feature.text }}</v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- Menu Preview Section -->
    <section id="menu" class="py-16">
      <v-container>
        <div class="text-center mb-10">
          <div class="text-overline" style="color: #C8941A;">BIZNING MENYU</div>
          <h2 class="text-h4 font-weight-bold text-primary mt-2">Taomlarimiz</h2>
        </div>
        <v-tabs v-model="activeTab" color="accent" class="mb-8" centered>
          <v-tab v-for="cat in menuCategories" :key="cat.id" :value="cat.id">
            {{ cat.name }}
          </v-tab>
        </v-tabs>
        <v-row>
          <v-col
            v-for="item in currentMenuItems"
            :key="item.id"
            cols="12" sm="6" md="3"
          >
            <v-card>
              <v-img
                v-if="item.image"
                :src="`/storage/${item.image}`"
                height="160"
                cover
              />
              <v-img
                v-else
                src="https://placehold.co/300x160/1A2744/C8941A?text=OldKhiva"
                height="160"
                cover
              />
              <v-card-title class="text-body-1">{{ item.name }}</v-card-title>
              <v-card-subtitle v-if="settings?.show_menu_prices !== false">
                <span style="color: #C8941A; font-weight: bold;">
                  {{ Number(item.price).toLocaleString('uz-UZ') }} so'm
                </span>
              </v-card-subtitle>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- About Section -->
    <section id="about" class="py-16" style="background: #F5F6FA;">
      <v-container>
        <v-row align="center">
          <v-col cols="12" md="6">
            <div class="text-overline mb-2" style="color: #C8941A;">HAQIMIZDA</div>
            <h2 class="text-h4 font-weight-bold text-primary mb-4">OldKhiva Restaurant</h2>
            <p class="text-body-1" style="color: #555; line-height: 1.8;">
              {{ settings?.about_text }}
            </p>
            <v-btn color="primary" class="mt-6" to="/register">
              Hamkor bo'lish
              <v-icon end>mdi-arrow-right</v-icon>
            </v-btn>
          </v-col>
          <v-col cols="12" md="6">
            <v-img
              v-if="settings?.about_image"
              :src="`/storage/${settings.about_image}`"
              rounded="xl"
              height="350"
              cover
            />
            <v-card v-else color="primary" height="350" class="d-flex align-center justify-center">
              <v-icon size="120" color="rgba(200, 148, 26, 0.5)">mdi-silverware-fork-knife</v-icon>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- Partnership CTA -->
    <section class="py-16" style="background: linear-gradient(135deg, #1A2744, #0F1C35);">
      <v-container>
        <v-row justify="center">
          <v-col cols="12" md="8" class="text-center">
            <h2 class="text-h4 font-weight-bold text-white mb-4">
              {{ settings?.partnership_title || "Hamkor bo'ling" }}
            </h2>
            <p class="text-body-1 mb-8" style="color: #B0BEC5;">
              {{ settings?.partnership_text }}
            </p>
            <v-btn color="accent" size="x-large" to="/register">
              <v-icon start>mdi-account-plus</v-icon>
              Ro'yxatdan o'tish
            </v-btn>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-16" style="background: #F5F6FA;">
      <v-container>
        <div class="text-center mb-10">
          <div class="text-overline" style="color: #C8941A;">BOG'LANISH</div>
          <h2 class="text-h4 font-weight-bold text-primary mt-2">Kontakt</h2>
        </div>
        <v-row>
          <v-col cols="12" md="4">
            <v-list>
              <v-list-item prepend-icon="mdi-phone" :title="settings?.contact_phone" subtitle="Telefon" />
              <v-list-item prepend-icon="mdi-email" :title="settings?.contact_email" subtitle="Email" />
              <v-list-item prepend-icon="mdi-map-marker" :title="settings?.contact_address" subtitle="Manzil" />
            </v-list>
          </v-col>
          <v-col cols="12" md="8">
            <v-card height="300" class="overflow-hidden">
              <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d500!2d60.375575!3d41.374735!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f18!3m3!1m2!1s0x0%3A0x0!2zNDHCsDIyJzI5LjAiTiA2MMKwMjInMzIuMSJF!5e0!3m2!1sen!2s!4v1234567890"
                width="100%"
                height="300"
                style="border: 0;"
                allowfullscreen
                loading="lazy"
              />
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- Footer -->
    <footer style="background: #0F1C35; padding: 30px 0;">
      <v-container>
        <v-row align="center">
          <v-col cols="12" md="6" class="text-white">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
              <img v-if="settings?.logo_image" :src="`/storage/${settings.logo_image}`" alt="OldKhiva" style="height: 32px; object-fit: contain; filter: brightness(0) invert(1);" />
              <div class="text-h6 font-weight-bold">{{ settings?.brand_name || 'OLDKHIVA' }}</div>
            </div>
            <div class="text-caption" style="color: #B0BEC5;">© {{ new Date().getFullYear() }} OldKhiva Restaurant. Barcha huquqlar himoyalangan.</div>
          </v-col>
          <v-col cols="12" md="6" class="text-right">
            <v-btn variant="text" color="white" to="/login">Kirish</v-btn>
            <v-btn variant="text" color="white" to="/register">Ro'yxatdan o'tish</v-btn>
          </v-col>
        </v-row>
      </v-container>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/plugins/axios'

const loading = ref(true)
const settings = ref({})
const menuCategories = ref([])
const activeTab = ref(null)

const features = computed(() => settings.value?.features || [])

const currentMenuItems = computed(() => {
  if (!activeTab.value) return []
  const cat = menuCategories.value.find(c => c.id === activeTab.value)
  return cat?.items || []
})

onMounted(async () => {
  try {
    const res = await api.get('/landing')
    settings.value = res.data.data.settings
    menuCategories.value = res.data.data.menu_categories
    if (menuCategories.value.length > 0) {
      activeTab.value = menuCategories.value[0].id
    }
  } finally {
    loading.value = false
  }
})
</script>
