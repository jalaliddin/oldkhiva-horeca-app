<template>
  <div id="top" class="landing">

    <!-- ─── Mobile Drawer ─── -->
    <v-navigation-drawer v-model="drawer" temporary location="right" width="280" color="#0F1C35">
      <div class="pa-5 pb-3">
        <div class="d-flex align-center gap-2 mb-6">
          <v-icon color="accent" size="28">mdi-silverware-fork-knife</v-icon>
          <span class="text-white font-weight-bold text-h6">{{ settings?.brand_name || 'OLDKHIVA' }}</span>
        </div>
        <v-list nav density="compact">
          <v-list-item rounded="lg" style="color:#B0BEC5" prepend-icon="mdi-home-outline" :title="t('nav.home')" href="#top" @click="drawer=false" />
          <v-list-item rounded="lg" style="color:#B0BEC5" prepend-icon="mdi-silverware-fork-knife" :title="t('nav.menu')" href="#menu" @click="drawer=false" />
          <v-list-item rounded="lg" style="color:#B0BEC5" prepend-icon="mdi-information-outline" :title="t('nav.about')" href="#about" @click="drawer=false" />
          <v-list-item rounded="lg" style="color:#B0BEC5" prepend-icon="mdi-phone-outline" :title="t('nav.contact')" href="#contact" @click="drawer=false" />
          <v-list-item rounded="lg" style="color:#B0BEC5" prepend-icon="mdi-airplane" title="Travel" href="https://travel.oldkhiva.uz" target="_blank" rel="noopener" @click="drawer=false" />
          <v-list-item rounded="lg" style="color:#B0BEC5" prepend-icon="mdi-bicycle" title="Bicycle" href="https://bicycle.oldkhiva.uz" target="_blank" rel="noopener" @click="drawer=false" />
        </v-list>
        <v-divider class="my-4" style="border-color:rgba(255,255,255,0.1)" />
        <div class="d-flex gap-2 flex-wrap mb-4">
          <v-btn v-for="lang in languages" :key="lang.code" size="small"
            :variant="locale === lang.code ? 'flat' : 'outlined'"
            :color="locale === lang.code ? 'accent' : 'white'"
            @click="setLocale(lang.code)">
            {{ lang.flag }} {{ lang.code.toUpperCase() }}
          </v-btn>
        </div>
        <v-btn color="accent" to="/login" block class="mb-2" rounded="lg" @click="drawer=false">{{ t('nav.login') }}</v-btn>
        <v-btn variant="outlined" color="white" to="/register" block rounded="lg" @click="drawer=false">{{ t('nav.register') }}</v-btn>
      </div>
    </v-navigation-drawer>

    <!-- ─── Navbar ─── -->
    <header class="landing-nav" :class="{ scrolled }">
      <div class="landing-nav__inner">
        <!-- Logo -->
        <a href="#top" class="nav-logo">
          <img v-if="settings?.logo_image" :src="`/storage/${settings.logo_image}`" alt="OldKhiva" style="height:36px;object-fit:contain" />
          <div v-else class="nav-logo__text">
            <span class="nav-logo__name">{{ settings?.brand_name || 'OLDKHIVA' }}</span>
            <span class="nav-logo__tag">{{ settings?.brand_tagline || 'RESTAURANT' }}</span>
          </div>
        </a>

        <!-- Desktop links -->
        <nav v-if="!mobile" class="nav-links">
          <a class="nav-link" href="#top">{{ t('nav.home') }}</a>
          <a class="nav-link" href="#menu">{{ t('nav.menu') }}</a>
          <a class="nav-link" href="#about">{{ t('nav.about') }}</a>
          <a class="nav-link" href="#contact">{{ t('nav.contact') }}</a>
          <a class="nav-link" href="https://travel.oldkhiva.uz" target="_blank" rel="noopener">Travel</a>
          <a class="nav-link" href="https://bicycle.oldkhiva.uz" target="_blank" rel="noopener">Bicycle</a>
        </nav>

        <div class="nav-actions" v-if="!mobile">
          <!-- Language -->
          <v-menu>
            <template #activator="{ props }">
              <button class="nav-lang-btn" v-bind="props">
                <v-icon size="16">mdi-translate</v-icon>
                {{ currentLang.flag }} {{ currentLang.code.toUpperCase() }}
                <v-icon size="14">mdi-chevron-down</v-icon>
              </button>
            </template>
            <v-list density="compact" min-width="150" rounded="lg" elevation="8">
              <v-list-item v-for="lang in languages" :key="lang.code"
                :active="locale === lang.code" active-color="accent" rounded="lg"
                @click="setLocale(lang.code)">
                <template #prepend>{{ lang.flag }}</template>
                <v-list-item-title class="ml-2">{{ lang.label }}</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
          <a href="/login" class="nav-btn nav-btn--outline">{{ t('nav.login') }}</a>
          <a href="/register" class="nav-btn nav-btn--gold">{{ t('nav.register') }}</a>
        </div>

        <!-- Mobile burger -->
        <button v-else class="nav-burger" @click="drawer=!drawer">
          <v-icon color="white" size="28">mdi-menu</v-icon>
        </button>
      </div>
    </header>

    <!-- ─── Hero ─── -->
    <section class="hero-section"
      :style="settings?.hero_image
        ? { background: `linear-gradient(rgba(15,28,53,0.82), rgba(15,28,53,0.72)), url('/storage/${settings.hero_image}') center/cover no-repeat` }
        : {}">
      <div class="hero-pattern" />
      <v-container style="position:relative;z-index:2">
        <v-row align="center" style="min-height:calc(100vh - 80px)">
          <v-col cols="12" md="7">
            <div class="hero-badge">
              <span class="hero-badge__line" />
              {{ t('hero.location') }}
            </div>
            <h1 class="hero-title">{{ ls('hero_title') || 'OldKhiva Restaurant' }}</h1>
            <p class="hero-sub">{{ ls('hero_subtitle') || t('hero.subtitle') }}</p>
            <div class="hero-actions">
              <a href="/register" class="hero-btn hero-btn--gold">
                <v-icon start size="18">mdi-handshake</v-icon>
                {{ t('hero.partner') }}
              </a>
              <a href="#menu" class="hero-btn hero-btn--outline">{{ t('hero.viewMenu') }}</a>
            </div>
            <!-- Stats -->
            <div class="hero-stats">
              <div class="hero-stat"><span class="hero-stat__num">500+</span><span class="hero-stat__label">Mehmon</span></div>
              <div class="hero-stat__divider" />
              <div class="hero-stat"><span class="hero-stat__num">100+</span><span class="hero-stat__label">Taom</span></div>
              <div class="hero-stat__divider" />
              <div class="hero-stat"><span class="hero-stat__num">4</span><span class="hero-stat__label">Til</span></div>
            </div>
          </v-col>
          <!-- Decorative card — desktop only -->
          <v-col v-if="!mobile" cols="12" md="5" class="d-flex justify-center align-center">
            <div class="hero-visual">
              <div class="hero-visual__frame">
                <v-img
                  v-if="settings?.about_image || settings?.hero_image"
                  :src="`/storage/${settings.about_image || settings.hero_image}`"
                  cover height="420" rounded="xl"
                />
                <div v-else class="hero-visual__placeholder">
                  <v-icon size="80" color="rgba(200,148,26,0.6)">mdi-silverware-fork-knife</v-icon>
                </div>
                <div class="hero-visual__badge">
                  <v-icon size="16" color="accent">mdi-star</v-icon>
                  Premium Restaurant
                </div>
              </div>
            </div>
          </v-col>
        </v-row>
      </v-container>
      <!-- Scroll indicator -->
      <div class="scroll-hint">
        <div class="scroll-hint__mouse"><div class="scroll-hint__wheel" /></div>
      </div>
    </section>

    <!-- ─── Features ─── -->
    <section v-if="features.length" class="section-light">
      <v-container>
        <div class="section-head">
          <span class="section-chip">{{ t('features.label') }}</span>
          <h2 class="section-title">{{ t('features.title') }}</h2>
        </div>
        <v-row>
          <v-col v-for="(feature, idx) in features" :key="idx" cols="12" sm="6" :md="Math.floor(12/Math.min(features.length,3))">
            <div class="feature-card">
              <div class="feature-card__icon-wrap">
                <v-icon :icon="feature.icon" size="32" color="accent" />
              </div>
              <h3 class="feature-card__title">{{ lf(feature, 'title') }}</h3>
              <p class="feature-card__text">{{ lf(feature, 'text') }}</p>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- ─── Menu Section ─── -->
    <section id="menu" class="section-white">
      <v-container>
        <div class="section-head">
          <span class="section-chip">{{ t('menu.label') }}</span>
          <h2 class="section-title">{{ t('menu.sectionTitle') }}</h2>
        </div>
        <!-- Category tabs -->
        <div class="menu-tabs">
          <button
            v-for="cat in menuCategories"
            :key="cat.id"
            class="menu-tab"
            :class="{ active: activeTab === cat.id }"
            @click="activeTab = cat.id; onCategoryChange()"
          >{{ cat.name }}</button>
        </div>

        <!-- Carousel -->
        <div v-if="menuPages.length" class="menu-carousel">
          <v-window v-model="menuPage" :touch="{ left: nextPage, right: prevPage }">
            <v-window-item v-for="(page, pageIdx) in menuPages" :key="pageIdx">
              <v-row>
                <v-col v-for="item in page" :key="item.id" cols="12" sm="6" md="3">
                  <div class="menu-card">
                    <div class="menu-card__img-wrap">
                      <v-img
                        :src="item.image ? `/storage/${item.image}` : 'https://placehold.co/400x260/1A2744/C8941A?text=OldKhiva'"
                        height="220" cover class="menu-card__img"
                      />
                      <div v-if="settings?.show_menu_prices !== false" class="menu-card__price-badge">
                        {{ Number(item.price).toLocaleString('uz-UZ') }} {{ t('menu.currency') }}
                      </div>
                    </div>
                    <div class="menu-card__body">
                      <span class="menu-card__name">{{ item.name }}</span>
                    </div>
                  </div>
                </v-col>
              </v-row>
            </v-window-item>
          </v-window>

          <!-- Carousel controls -->
          <div v-if="menuPages.length > 1" class="carousel-controls">
            <button class="carousel-arrow" @click="prevPage">
              <v-icon size="18">mdi-chevron-left</v-icon>
            </button>
            <div class="carousel-dots">
              <button
                v-for="(_, idx) in menuPages"
                :key="idx"
                class="carousel-dot"
                :class="{ active: menuPage === idx }"
                @click="goToPage(idx)"
              />
            </div>
            <button class="carousel-arrow" @click="nextPage">
              <v-icon size="18">mdi-chevron-right</v-icon>
            </button>
          </div>
        </div>
      </v-container>
    </section>

    <!-- ─── About ─── -->
    <section id="about" class="section-light">
      <v-container>
        <v-row align="center" :class="mobile ? '' : 'flex-row-reverse'">
          <!-- Image -->
          <v-col cols="12" md="6" class="mb-8 mb-md-0">
            <div class="about-img-wrap">
              <v-img
                v-if="settings?.about_image"
                :src="`/storage/${settings.about_image}`"
                cover height="400" rounded="xl" class="about-img"
              />
              <div v-else class="about-img-placeholder">
                <v-icon size="100" color="rgba(200,148,26,0.4)">mdi-silverware-fork-knife</v-icon>
              </div>
              <div class="about-img__badge">
                <v-icon size="14" color="accent">mdi-check-circle</v-icon>
                OldKhiva
              </div>
            </div>
          </v-col>
          <!-- Text -->
          <v-col cols="12" md="6">
            <span class="section-chip">{{ t('about.label') }}</span>
            <h2 class="section-title mt-3">OldKhiva Restaurant</h2>
            <p class="about-text">{{ ls('about_text') }}</p>
            <a href="/register" class="hero-btn hero-btn--dark mt-6 d-inline-flex">
              {{ t('about.cta') }}
              <v-icon end size="16">mdi-arrow-right</v-icon>
            </a>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- ─── CTA ─── -->
    <section class="cta-section">
      <div class="cta-pattern" />
      <v-container style="position:relative;z-index:2">
        <v-row justify="center">
          <v-col cols="12" md="8" class="text-center">
            <div class="cta-icon-wrap mb-6">
              <v-icon size="40" color="accent">mdi-handshake</v-icon>
            </div>
            <h2 class="cta-title">{{ ls('partnership_title') || t('cta.title') }}</h2>
            <p class="cta-sub">{{ ls('partnership_text') }}</p>
            <a href="/register" class="hero-btn hero-btn--gold mt-4">
              <v-icon start size="18">mdi-account-plus</v-icon>
              {{ t('nav.register') }}
            </a>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- ─── Contact ─── -->
    <section id="contact" class="section-white">
      <v-container>
        <div class="section-head">
          <span class="section-chip">{{ t('contact.label') }}</span>
          <h2 class="section-title">{{ t('contact.title') }}</h2>
        </div>
        <v-row class="mb-8">
          <v-col cols="12" sm="4">
            <div class="contact-card">
              <div class="contact-card__icon"><v-icon size="22" color="accent">mdi-phone</v-icon></div>
              <div class="contact-card__label">{{ t('contact.phone') }}</div>
              <div class="contact-card__value">{{ settings?.contact_phone || '—' }}</div>
            </div>
          </v-col>
          <v-col cols="12" sm="4">
            <div class="contact-card">
              <div class="contact-card__icon"><v-icon size="22" color="accent">mdi-email</v-icon></div>
              <div class="contact-card__label">{{ t('contact.email') }}</div>
              <div class="contact-card__value">{{ settings?.contact_email || '—' }}</div>
            </div>
          </v-col>
          <v-col cols="12" sm="4">
            <div class="contact-card">
              <div class="contact-card__icon"><v-icon size="22" color="accent">mdi-map-marker</v-icon></div>
              <div class="contact-card__label">{{ t('contact.address') }}</div>
              <div class="contact-card__value">{{ settings?.contact_address || '—' }}</div>
            </div>
          </v-col>
        </v-row>
        <div class="map-wrap">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d500!2d60.375575!3d41.374735!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f18!3m3!1m2!1s0x0%3A0x0!2zNDHCsDIyJzI5LjAiTiA2MMKwMjInMzIuMSJF!5e0!3m2!1sen!2s!4v1234567890"
            width="100%" height="340" style="border:0;display:block" allowfullscreen loading="lazy"
          />
        </div>
      </v-container>
    </section>

    <!-- ─── Footer ─── -->
    <footer class="site-footer">
      <v-container>
        <v-row>
          <v-col cols="12" md="5">
            <div class="footer-logo">
              <img v-if="settings?.logo_image" :src="`/storage/${settings.logo_image}`" alt="OldKhiva" style="height:32px;filter:brightness(0) invert(1)" />
              <span v-else class="footer-logo__name">{{ settings?.brand_name || 'OLDKHIVA' }}</span>
            </div>
            <p class="footer-desc">{{ ls('hero_subtitle') || t('hero.subtitle') }}</p>
          </v-col>
          <v-col cols="6" md="3">
            <div class="footer-heading">{{ t('nav.menu') }}</div>
            <div class="footer-links">
              <a class="footer-link" href="#top">{{ t('nav.home') }}</a>
              <a class="footer-link" href="#menu">{{ t('nav.menu') }}</a>
              <a class="footer-link" href="#about">{{ t('nav.about') }}</a>
              <a class="footer-link" href="#contact">{{ t('nav.contact') }}</a>
            </div>
          </v-col>
          <v-col cols="6" md="4">
            <div class="footer-heading">{{ t('contact.title') }}</div>
            <div class="footer-links">
              <span class="footer-link">{{ settings?.contact_phone }}</span>
              <span class="footer-link">{{ settings?.contact_email }}</span>
              <span class="footer-link">{{ settings?.contact_address }}</span>
            </div>
            <div class="d-flex gap-2 mt-4">
              <a href="/login" class="footer-btn">{{ t('nav.login') }}</a>
              <a href="/register" class="footer-btn footer-btn--gold">{{ t('nav.register') }}</a>
            </div>
          </v-col>
        </v-row>
        <div class="footer-bottom">
          <span>© {{ new Date().getFullYear() }} OldKhiva Restaurant. {{ t('footer.rights') }}</span>
          <!-- Language in footer -->
          <div class="footer-langs">
            <button v-for="lang in languages" :key="lang.code"
              class="footer-lang" :class="{ active: locale === lang.code }"
              @click="setLocale(lang.code)">
              {{ lang.flag }} {{ lang.code.toUpperCase() }}
            </button>
          </div>
        </div>
      </v-container>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useDisplay } from 'vuetify'
import { useI18nStore, languages } from '@/stores/i18n'
import api from '@/plugins/axios'

const { mobile } = useDisplay()
const i18n = useI18nStore()
const drawer = ref(false)
const loading = ref(true)
const settings = ref({})
const menuCategories = ref([])
const activeTab = ref(null)
const scrolled = ref(false)

// Carousel
const menuPage = ref(0)
const ITEMS_PER_PAGE = 4
let carouselTimer = null

const locale = computed(() => i18n.locale)
const currentLang = computed(() => i18n.currentLang)

function t(key) { return i18n.t(key) }
function setLocale(code) { i18n.setLocale(code) }

function ls(key) {
  const loc = i18n.locale
  if (loc === 'uz') return settings.value?.[key] ?? ''
  return settings.value?.[`${key}_${loc}`] || settings.value?.[key] || ''
}
function lf(feature, key) {
  const loc = i18n.locale
  if (loc === 'uz') return feature[key] ?? ''
  return feature[`${key}_${loc}`] || feature[key] || ''
}

const features = computed(() => settings.value?.features || [])
const currentMenuItems = computed(() => {
  if (!activeTab.value) return []
  const cat = menuCategories.value.find(c => c.id === activeTab.value)
  return cat?.items || []
})
const menuPages = computed(() => {
  const items = currentMenuItems.value
  if (!items.length) return []
  const pages = []
  for (let i = 0; i < items.length; i += ITEMS_PER_PAGE) pages.push(items.slice(i, i + ITEMS_PER_PAGE))
  return pages
})

function nextPage() { if (!menuPages.value.length) return; menuPage.value = (menuPage.value + 1) % menuPages.value.length }
function prevPage() { if (!menuPages.value.length) return; menuPage.value = (menuPage.value - 1 + menuPages.value.length) % menuPages.value.length }
function goToPage(idx) { menuPage.value = idx; restartCarousel() }
function startCarousel() { clearInterval(carouselTimer); if (menuPages.value.length > 1) carouselTimer = setInterval(nextPage, 3500) }
function restartCarousel() { clearInterval(carouselTimer); if (menuPages.value.length > 1) carouselTimer = setInterval(nextPage, 3500) }
function onCategoryChange() { menuPage.value = 0; restartCarousel() }

watch(menuPages, () => { menuPage.value = 0; restartCarousel() })

function onScroll() { scrolled.value = window.scrollY > 60 }

onUnmounted(() => { clearInterval(carouselTimer); window.removeEventListener('scroll', onScroll) })

onMounted(async () => {
  window.addEventListener('scroll', onScroll, { passive: true })
  try {
    const res = await api.get('/landing')
    settings.value = res.data.data.settings
    menuCategories.value = res.data.data.menu_categories
    if (menuCategories.value.length > 0) activeTab.value = menuCategories.value[0].id
  } finally {
    loading.value = false
    startCarousel()
  }
})
</script>

<style scoped>
/* ── Global ── */
.landing { font-family: 'Inter', sans-serif; color: #1a1a2e; }

/* ── Navbar ── */
.landing-nav {
  position: fixed; top: 0; left: 0; right: 0; z-index: 200;
  transition: background 0.3s, box-shadow 0.3s;
  background: transparent;
}
.landing-nav.scrolled {
  background: rgba(15, 28, 53, 0.96);
  backdrop-filter: blur(16px);
  box-shadow: 0 2px 24px rgba(0,0,0,0.25);
}
.landing-nav__inner {
  max-width: 1280px; margin: 0 auto;
  display: flex; align-items: center; gap: 8px;
  padding: 0 24px; height: 72px;
}
.nav-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; flex-shrink: 0; }
.nav-logo__text { display: flex; flex-direction: column; line-height: 1; }
.nav-logo__name { font-size: 1.1rem; font-weight: 800; color: #fff; letter-spacing: 2px; }
.nav-logo__tag  { font-size: 0.6rem; color: #C8941A; letter-spacing: 3px; }

.nav-links { display: flex; align-items: center; gap: 2px; margin-left: 32px; }
.nav-link {
  color: rgba(255,255,255,0.8); text-decoration: none; font-size: 0.875rem;
  padding: 6px 12px; border-radius: 8px; transition: all 0.2s;
}
.nav-link:hover { color: #fff; background: rgba(255,255,255,0.08); }

.nav-actions { display: flex; align-items: center; gap: 8px; margin-left: auto; }

.nav-lang-btn {
  display: flex; align-items: center; gap: 4px;
  color: rgba(255,255,255,0.8); background: transparent; border: none; cursor: pointer;
  font-size: 0.8rem; padding: 6px 10px; border-radius: 8px; transition: all 0.2s;
}
.nav-lang-btn:hover { color: #fff; background: rgba(255,255,255,0.1); }

.nav-btn {
  padding: 7px 18px; border-radius: 8px; font-size: 0.85rem; font-weight: 600;
  text-decoration: none; transition: all 0.2s; cursor: pointer;
}
.nav-btn--outline {
  border: 1px solid rgba(255,255,255,0.4); color: #fff;
}
.nav-btn--outline:hover { border-color: #fff; background: rgba(255,255,255,0.08); }
.nav-btn--gold { background: #C8941A; color: #0F1C35; }
.nav-btn--gold:hover { background: #d9a222; }

.nav-burger { background: none; border: none; cursor: pointer; margin-left: auto; padding: 4px; }

/* ── Hero ── */
.hero-section {
  position: relative; min-height: 100vh; display: flex; align-items: center;
  padding-top: 72px;
  background: linear-gradient(135deg, #0F1C35 0%, #1A2744 55%, #2a3a5e 100%);
  overflow: hidden;
}
.hero-pattern {
  position: absolute; inset: 0; opacity: 0.04;
  background-image: radial-gradient(circle, #C8941A 1px, transparent 1px);
  background-size: 32px 32px;
}
.hero-badge {
  display: inline-flex; align-items: center; gap: 10px;
  color: #C8941A; font-size: 0.75rem; font-weight: 700;
  letter-spacing: 4px; text-transform: uppercase; margin-bottom: 24px;
}
.hero-badge__line { width: 32px; height: 2px; background: #C8941A; border-radius: 2px; flex-shrink: 0; }

.hero-title {
  font-size: clamp(2.8rem, 6vw, 5rem); font-weight: 900; color: #fff;
  line-height: 1.05; margin: 0 0 20px; letter-spacing: -1px;
}
.hero-sub {
  font-size: 1.125rem; color: rgba(255,255,255,0.65); font-weight: 300;
  line-height: 1.7; margin-bottom: 40px; max-width: 520px;
}
.hero-actions { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 48px; }

.hero-btn {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 14px 28px; border-radius: 10px; font-size: 0.95rem; font-weight: 700;
  text-decoration: none; cursor: pointer; transition: all 0.25s; border: none;
}
.hero-btn--gold   { background: #C8941A; color: #0F1C35; }
.hero-btn--gold:hover { background: #d9a222; transform: translateY(-2px); box-shadow: 0 8px 24px rgba(200,148,26,0.4); }
.hero-btn--outline { border: 2px solid rgba(255,255,255,0.4); color: #fff; background: transparent; }
.hero-btn--outline:hover { border-color: #fff; background: rgba(255,255,255,0.08); }
.hero-btn--dark   { background: #0F1C35; color: #fff; }
.hero-btn--dark:hover { background: #1A2744; transform: translateY(-2px); }

.hero-stats { display: flex; align-items: center; gap: 24px; }
.hero-stat { display: flex; flex-direction: column; gap: 2px; }
.hero-stat__num  { font-size: 1.6rem; font-weight: 900; color: #C8941A; line-height: 1; }
.hero-stat__label { font-size: 0.75rem; color: rgba(255,255,255,0.5); letter-spacing: 1px; }
.hero-stat__divider { width: 1px; height: 36px; background: rgba(255,255,255,0.15); }

/* Hero visual */
.hero-visual { position: relative; }
.hero-visual__frame {
  position: relative; border-radius: 20px; overflow: visible;
}
.hero-visual__frame::before {
  content: ''; position: absolute; inset: -3px; border-radius: 22px;
  background: linear-gradient(135deg, #C8941A, transparent 60%);
  z-index: -1;
}
.hero-visual__placeholder {
  height: 420px; background: rgba(255,255,255,0.04); border-radius: 20px;
  display: flex; align-items: center; justify-content: center;
  border: 1px solid rgba(255,255,255,0.08);
}
.hero-visual__badge {
  position: absolute; bottom: -16px; right: -16px;
  background: #0F1C35; border: 1px solid rgba(200,148,26,0.4);
  color: #fff; padding: 8px 16px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;
  display: flex; align-items: center; gap: 6px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.4);
}

/* Scroll hint */
.scroll-hint {
  position: absolute; bottom: 32px; left: 50%; transform: translateX(-50%);
  display: flex; flex-direction: column; align-items: center; gap: 6px;
}
.scroll-hint__mouse {
  width: 22px; height: 34px; border: 2px solid rgba(255,255,255,0.3); border-radius: 12px;
  display: flex; justify-content: center; padding-top: 5px;
}
.scroll-hint__wheel {
  width: 3px; height: 6px; background: rgba(255,255,255,0.5);
  border-radius: 2px; animation: scroll-anim 1.6s ease-in-out infinite;
}
@keyframes scroll-anim {
  0%, 100% { transform: translateY(0); opacity: 1; }
  60% { transform: translateY(10px); opacity: 0; }
}

/* ── Sections ── */
.section-light { background: #F5F6FA; padding: 80px 0; }
.section-white { background: #fff; padding: 80px 0; }

.section-head { text-align: center; margin-bottom: 52px; }
.section-chip {
  display: inline-block; padding: 4px 14px; border-radius: 100px;
  background: rgba(200,148,26,0.12); color: #C8941A;
  font-size: 0.7rem; font-weight: 700; letter-spacing: 3px; text-transform: uppercase;
}
.section-title {
  font-size: clamp(1.6rem, 3vw, 2.4rem); font-weight: 800; color: #0F1C35;
  margin: 12px 0 0; letter-spacing: -0.5px;
}

/* ── Features ── */
.feature-card {
  background: #fff; border-radius: 16px; padding: 32px 28px;
  height: 100%; transition: transform 0.25s, box-shadow 0.25s;
  box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}
.feature-card:hover { transform: translateY(-6px); box-shadow: 0 12px 32px rgba(0,0,0,0.12); }
.feature-card__icon-wrap {
  width: 60px; height: 60px; border-radius: 16px;
  background: linear-gradient(135deg, rgba(200,148,26,0.15) 0%, rgba(200,148,26,0.05) 100%);
  display: flex; align-items: center; justify-content: center; margin-bottom: 20px;
}
.feature-card__title { font-size: 1.05rem; font-weight: 700; color: #0F1C35; margin: 0 0 10px; }
.feature-card__text  { font-size: 0.875rem; color: #6b7280; line-height: 1.65; margin: 0; }

/* ── Menu ── */
.menu-tabs { display: flex; gap: 8px; justify-content: center; flex-wrap: wrap; margin-bottom: 40px; }
.menu-tab {
  padding: 9px 22px; border-radius: 100px; font-size: 0.875rem; font-weight: 600;
  border: 2px solid #e5e7eb; color: #6b7280; background: transparent; cursor: pointer;
  transition: all 0.2s;
}
.menu-tab:hover { border-color: #C8941A; color: #C8941A; }
.menu-tab.active { background: #C8941A; border-color: #C8941A; color: #fff; }

.menu-carousel { }
.menu-card { border-radius: 16px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08); transition: transform 0.25s, box-shadow 0.25s; background: #fff; }
.menu-card:hover { transform: translateY(-5px); box-shadow: 0 16px 40px rgba(0,0,0,0.14); }
.menu-card__img-wrap { position: relative; }
.menu-card__img { display: block; }
.menu-card__price-badge {
  position: absolute; bottom: 10px; right: 10px;
  background: rgba(15,28,53,0.88); color: #C8941A; font-weight: 700;
  font-size: 0.8rem; padding: 4px 10px; border-radius: 8px;
  backdrop-filter: blur(6px); border: 1px solid rgba(200,148,26,0.3);
}
.menu-card__body { padding: 14px 16px; }
.menu-card__name { font-size: 0.95rem; font-weight: 600; color: #0F1C35; }

/* Carousel controls */
.carousel-controls { display: flex; align-items: center; justify-content: center; gap: 12px; margin-top: 28px; }
.carousel-arrow {
  width: 36px; height: 36px; border-radius: 50%;
  border: 1.5px solid #e5e7eb; background: #fff; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  transition: all 0.2s; color: #0F1C35;
}
.carousel-arrow:hover { border-color: #C8941A; color: #C8941A; }
.carousel-dots { display: flex; gap: 6px; align-items: center; }
.carousel-dot {
  width: 8px; height: 8px; border-radius: 50%; background: #e5e7eb;
  border: none; cursor: pointer; transition: all 0.25s; padding: 0;
}
.carousel-dot.active { background: #C8941A; width: 22px; border-radius: 4px; }

/* ── About ── */
.about-img-wrap { position: relative; }
.about-img { display: block; }
.about-img-placeholder {
  height: 400px; background: linear-gradient(135deg, #0F1C35, #1A2744);
  border-radius: 16px; display: flex; align-items: center; justify-content: center;
}
.about-img__badge {
  position: absolute; top: 24px; left: -16px;
  background: #C8941A; color: #0F1C35; font-size: 0.8rem; font-weight: 700;
  padding: 8px 16px; border-radius: 0 8px 8px 0; display: flex; align-items: center; gap: 6px;
  box-shadow: 0 4px 16px rgba(200,148,26,0.4);
}
.about-text { color: #4b5563; line-height: 1.85; font-size: 0.975rem; margin: 16px 0 0; }

/* ── CTA ── */
.cta-section {
  position: relative; padding: 96px 0; overflow: hidden;
  background: linear-gradient(135deg, #0F1C35 0%, #1a2a4a 50%, #0F1C35 100%);
}
.cta-pattern {
  position: absolute; inset: 0; opacity: 0.03;
  background-image: radial-gradient(circle, #C8941A 1px, transparent 1px);
  background-size: 28px 28px;
}
.cta-icon-wrap {
  width: 72px; height: 72px; border-radius: 20px; margin: 0 auto;
  background: rgba(200,148,26,0.12); border: 1px solid rgba(200,148,26,0.3);
  display: flex; align-items: center; justify-content: center;
}
.cta-title { font-size: clamp(1.6rem, 3vw, 2.5rem); font-weight: 800; color: #fff; margin: 0 0 16px; }
.cta-sub   { color: rgba(255,255,255,0.6); font-size: 1rem; line-height: 1.7; max-width: 500px; margin: 0 auto; }

/* ── Contact ── */
.contact-card {
  border-radius: 16px; padding: 28px 24px; background: #F5F6FA;
  border: 1px solid #e9ecef; transition: all 0.25s; height: 100%;
}
.contact-card:hover { border-color: rgba(200,148,26,0.4); box-shadow: 0 8px 24px rgba(0,0,0,0.07); }
.contact-card__icon {
  width: 44px; height: 44px; background: rgba(200,148,26,0.1); border-radius: 12px;
  display: flex; align-items: center; justify-content: center; margin-bottom: 14px;
}
.contact-card__label { font-size: 0.7rem; color: #C8941A; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 6px; }
.contact-card__value { font-size: 0.925rem; color: #0F1C35; font-weight: 600; }

.map-wrap { border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }

/* ── Footer ── */
.site-footer { background: #0a1628; padding: 64px 0 0; }
.footer-logo { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
.footer-logo__name { font-size: 1.2rem; font-weight: 800; color: #fff; letter-spacing: 2px; }
.footer-desc { color: rgba(255,255,255,0.45); font-size: 0.875rem; line-height: 1.7; max-width: 280px; }
.footer-heading { color: rgba(255,255,255,0.35); font-size: 0.65rem; letter-spacing: 3px; text-transform: uppercase; margin-bottom: 16px; font-weight: 600; }
.footer-links { display: flex; flex-direction: column; gap: 10px; }
.footer-link { color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.875rem; transition: color 0.2s; }
.footer-link:hover { color: #C8941A; }
.footer-btn {
  padding: 7px 16px; border-radius: 8px; font-size: 0.8rem; font-weight: 600;
  border: 1px solid rgba(255,255,255,0.2); color: #fff; text-decoration: none;
  transition: all 0.2s; display: inline-block;
}
.footer-btn:hover { border-color: rgba(255,255,255,0.5); }
.footer-btn--gold { background: #C8941A; border-color: #C8941A; color: #0F1C35; }
.footer-btn--gold:hover { background: #d9a222; }
.footer-bottom {
  margin-top: 48px; padding: 20px 0; border-top: 1px solid rgba(255,255,255,0.08);
  display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
  color: rgba(255,255,255,0.35); font-size: 0.8rem;
}
.footer-langs { display: flex; gap: 8px; }
.footer-lang {
  background: transparent; border: 1px solid rgba(255,255,255,0.12);
  color: rgba(255,255,255,0.4); font-size: 0.72rem; padding: 3px 8px; border-radius: 6px;
  cursor: pointer; transition: all 0.2s;
}
.footer-lang:hover, .footer-lang.active { border-color: #C8941A; color: #C8941A; }
</style>
