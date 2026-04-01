<template>
  <div id="top">
    <!-- Mobile Drawer -->
    <v-navigation-drawer v-model="drawer" temporary location="right" color="primary">
      <v-list nav>
        <v-list-item prepend-icon="mdi-home" :title="t('nav.home')" href="#top" @click="drawer = false" />
        <v-list-item prepend-icon="mdi-silverware-fork-knife" :title="t('nav.menu')" href="#menu" @click="drawer = false" />
        <v-list-item prepend-icon="mdi-information" :title="t('nav.about')" href="#about" @click="drawer = false" />
        <v-list-item prepend-icon="mdi-phone" :title="t('nav.contact')" href="#contact" @click="drawer = false" />
        <v-list-item prepend-icon="mdi-airplane" title="Travel" href="https://travel.oldkhiva.uz" target="_blank" rel="noopener" @click="drawer = false" />
        <v-list-item prepend-icon="mdi-bicycle" title="Bicycle" href="https://bicycle.oldkhiva.uz" target="_blank" rel="noopener" @click="drawer = false" />
        <v-divider class="my-2" />
        <!-- Language switcher in drawer -->
        <v-list-item>
          <div style="display: flex; gap: 6px; flex-wrap: wrap; padding: 4px 0;">
            <v-btn
              v-for="lang in languages"
              :key="lang.code"
              :variant="locale === lang.code ? 'flat' : 'outlined'"
              :color="locale === lang.code ? 'accent' : 'white'"
              size="small"
              @click="setLocale(lang.code)"
            >{{ lang.flag }} {{ lang.code.toUpperCase() }}</v-btn>
          </div>
        </v-list-item>
        <v-divider class="my-2" />
        <v-list-item>
          <v-btn color="accent" to="/login" block class="mb-2" @click="drawer = false">{{ t('nav.login') }}</v-btn>
        </v-list-item>
        <v-list-item>
          <v-btn variant="outlined" color="white" to="/register" block @click="drawer = false">{{ t('nav.register') }}</v-btn>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

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
      <!-- Desktop menu -->
      <template v-if="!mobile">
        <v-btn variant="text" color="white" href="#top">{{ t('nav.home') }}</v-btn>
        <v-btn variant="text" color="white" href="#menu">{{ t('nav.menu') }}</v-btn>
        <v-btn variant="text" color="white" href="#about">{{ t('nav.about') }}</v-btn>
        <v-btn variant="text" color="white" href="#contact">{{ t('nav.contact') }}</v-btn>
        <v-btn variant="text" color="white" href="https://travel.oldkhiva.uz" target="_blank" rel="noopener">
          <v-icon start size="small">mdi-airplane</v-icon>Travel
        </v-btn>
        <v-btn variant="text" color="white" href="https://bicycle.oldkhiva.uz" target="_blank" rel="noopener">
          <v-icon start size="small">mdi-bicycle</v-icon>Bicycle
        </v-btn>

        <!-- Language switcher -->
        <v-menu>
          <template #activator="{ props }">
            <v-btn variant="text" color="white" v-bind="props" class="ml-1">
              <v-icon start size="small">mdi-translate</v-icon>
              {{ currentLang.flag }} {{ currentLang.code.toUpperCase() }}
              <v-icon end size="small">mdi-chevron-down</v-icon>
            </v-btn>
          </template>
          <v-list density="compact" min-width="150">
            <v-list-item
              v-for="lang in languages"
              :key="lang.code"
              :active="locale === lang.code"
              active-color="accent"
              @click="setLocale(lang.code)"
            >
              <template #prepend>{{ lang.flag }}</template>
              <v-list-item-title class="ml-2">{{ lang.label }}</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>

        <v-btn color="accent" to="/login" class="ml-2">{{ t('nav.login') }}</v-btn>
        <v-btn variant="outlined" color="white" to="/register" class="ml-2 mr-3">{{ t('nav.register') }}</v-btn>
      </template>
      <!-- Mobile hamburger -->
      <v-app-bar-nav-icon v-else color="white" @click="drawer = !drawer" />
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
            <div class="text-overline mb-4" style="color: #C8941A; letter-spacing: 4px;">{{ t('hero.location') }}</div>
            <h1 class="text-white font-weight-bold mb-4" style="font-size: clamp(2.5rem, 6vw, 4.5rem); line-height: 1.1;">
              {{ settings?.hero_title || 'OldKhiva Restaurant' }}
            </h1>
            <p class="text-h6 mb-8" style="color: #B0BEC5; font-weight: 300;">
              {{ settings?.hero_subtitle || t('hero.subtitle') }}
            </p>
            <v-btn color="accent" size="x-large" to="/register" class="mr-4">
              <v-icon start>mdi-handshake</v-icon>
              {{ t('hero.partner') }}
            </v-btn>
            <v-btn variant="outlined" color="white" size="x-large" href="#menu">
              {{ t('hero.viewMenu') }}
            </v-btn>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- Features Section -->
    <section v-if="features.length" class="py-16" style="background: #F5F6FA;">
      <v-container>
        <div class="text-center mb-12">
          <div class="text-overline" style="color: #C8941A;">{{ t('features.label') }}</div>
          <h2 class="text-h4 font-weight-bold text-primary mt-2">{{ t('features.title') }}</h2>
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
          <div class="text-overline" style="color: #C8941A;">{{ t('menu.label') }}</div>
          <h2 class="text-h4 font-weight-bold text-primary mt-2">{{ t('menu.title') }}</h2>
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
                  {{ Number(item.price).toLocaleString('uz-UZ') }} {{ t('menu.currency') }}
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
            <div class="text-overline mb-2" style="color: #C8941A;">{{ t('about.label') }}</div>
            <h2 class="text-h4 font-weight-bold text-primary mb-4">OldKhiva Restaurant</h2>
            <p class="text-body-1" style="color: #555; line-height: 1.8;">
              {{ settings?.about_text }}
            </p>
            <v-btn color="primary" class="mt-6" to="/register">
              {{ t('about.cta') }}
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
              {{ settings?.partnership_title || t('cta.title') }}
            </h2>
            <p class="text-body-1 mb-8" style="color: #B0BEC5;">
              {{ settings?.partnership_text }}
            </p>
            <v-btn color="accent" size="x-large" to="/register">
              <v-icon start>mdi-account-plus</v-icon>
              {{ t('nav.register') }}
            </v-btn>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-16" style="background: #F5F6FA;">
      <v-container>
        <div class="text-center mb-10">
          <div class="text-overline" style="color: #C8941A;">{{ t('contact.label') }}</div>
          <h2 class="text-h4 font-weight-bold text-primary mt-2">{{ t('contact.title') }}</h2>
        </div>
        <v-row>
          <v-col cols="12" md="4">
            <v-list>
              <v-list-item prepend-icon="mdi-phone" :title="settings?.contact_phone" :subtitle="t('contact.phone')" />
              <v-list-item prepend-icon="mdi-email" :title="settings?.contact_email" :subtitle="t('contact.email')" />
              <v-list-item prepend-icon="mdi-map-marker" :title="settings?.contact_address" :subtitle="t('contact.address')" />
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
            <div class="text-caption" style="color: #B0BEC5;">© {{ new Date().getFullYear() }} OldKhiva Restaurant. {{ t('footer.rights') }}</div>
          </v-col>
          <v-col cols="12" md="6" class="text-right">
            <v-btn variant="text" color="white" to="/login">{{ t('nav.login') }}</v-btn>
            <v-btn variant="text" color="white" to="/register">{{ t('nav.register') }}</v-btn>
          </v-col>
        </v-row>
      </v-container>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useDisplay } from 'vuetify'
import api from '@/plugins/axios'

const { mobile } = useDisplay()
const drawer = ref(false)
const loading = ref(true)
const settings = ref({})
const menuCategories = ref([])
const activeTab = ref(null)

// ── i18n ──────────────────────────────────────────────────
const translations = {
  uz: {
    nav: { home: 'Bosh sahifa', menu: 'Menyu', about: 'Haqimizda', contact: "Bog'lanish", login: 'Kirish', register: "Ro'yxatdan o'tish" },
    hero: { location: "XIVA, O'ZBEKISTON", subtitle: "Xiva tarixining ta'mi", partner: "Hamkor bo'ling", viewMenu: "Menyu ko'rish" },
    features: { label: 'AFZALLIKLARIMIZ', title: 'Nima uchun OldKhiva?' },
    menu: { label: 'BIZNING MENYU', title: 'Taomlarimiz', currency: "so'm" },
    about: { label: 'HAQIMIZDA', cta: "Hamkor bo'lish" },
    cta: { title: "Hamkor bo'ling" },
    contact: { label: "BOG'LANISH", title: 'Kontakt', phone: 'Telefon', email: 'Email', address: 'Manzil' },
    footer: { rights: 'Barcha huquqlar himoyalangan.' },
  },
  en: {
    nav: { home: 'Home', menu: 'Menu', about: 'About', contact: 'Contact', login: 'Login', register: 'Register' },
    hero: { location: 'KHIVA, UZBEKISTAN', subtitle: 'The taste of Khiva history', partner: 'Become a Partner', viewMenu: 'View Menu' },
    features: { label: 'OUR ADVANTAGES', title: 'Why OldKhiva?' },
    menu: { label: 'OUR MENU', title: 'Our Dishes', currency: 'UZS' },
    about: { label: 'ABOUT US', cta: 'Become a Partner' },
    cta: { title: 'Become a Partner' },
    contact: { label: 'CONTACT', title: 'Contact Us', phone: 'Phone', email: 'Email', address: 'Address' },
    footer: { rights: 'All rights reserved.' },
  },
  ru: {
    nav: { home: 'Главная', menu: 'Меню', about: 'О нас', contact: 'Контакт', login: 'Войти', register: 'Регистрация' },
    hero: { location: 'ХИВА, УЗБЕКИСТАН', subtitle: 'Вкус истории Хивы', partner: 'Стать партнёром', viewMenu: 'Смотреть меню' },
    features: { label: 'НАШИ ПРЕИМУЩЕСТВА', title: 'Почему OldKhiva?' },
    menu: { label: 'НАШЕ МЕНЮ', title: 'Наши блюда', currency: 'сум' },
    about: { label: 'О НАС', cta: 'Стать партнёром' },
    cta: { title: 'Стать партнёром' },
    contact: { label: 'КОНТАКТ', title: 'Свяжитесь с нами', phone: 'Телефон', email: 'Email', address: 'Адрес' },
    footer: { rights: 'Все права защищены.' },
  },
  de: {
    nav: { home: 'Startseite', menu: 'Speisekarte', about: 'Über uns', contact: 'Kontakt', login: 'Anmelden', register: 'Registrieren' },
    hero: { location: 'CHIWA, USBEKISTAN', subtitle: 'Der Geschmack der Geschichte Chiwas', partner: 'Partner werden', viewMenu: 'Speisekarte ansehen' },
    features: { label: 'UNSERE VORTEILE', title: 'Warum OldKhiva?' },
    menu: { label: 'UNSERE SPEISEKARTE', title: 'Unsere Gerichte', currency: 'UZS' },
    about: { label: 'ÜBER UNS', cta: 'Partner werden' },
    cta: { title: 'Partner werden' },
    contact: { label: 'KONTAKT', title: 'Kontaktieren Sie uns', phone: 'Telefon', email: 'E-Mail', address: 'Adresse' },
    footer: { rights: 'Alle Rechte vorbehalten.' },
  },
}

const languages = [
  { code: 'uz', label: "O'zbek", flag: '🇺🇿' },
  { code: 'en', label: 'English', flag: '🇬🇧' },
  { code: 'ru', label: 'Русский', flag: '🇷🇺' },
  { code: 'de', label: 'Deutsch', flag: '🇩🇪' },
]

const locale = ref(localStorage.getItem('lang') || 'uz')
const currentLang = computed(() => languages.find(l => l.code === locale.value))

function t(key) {
  return key.split('.').reduce((obj, k) => obj?.[k], translations[locale.value]) ?? key
}

function setLocale(code) {
  locale.value = code
  localStorage.setItem('lang', code)
}
// ─────────────────────────────────────────────────────────

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
