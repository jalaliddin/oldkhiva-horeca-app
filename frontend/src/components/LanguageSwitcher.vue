<template>
  <v-menu>
    <template #activator="{ props }">
      <v-btn v-bind="props" :variant="btnVariant" :color="btnColor" size="small">
        <v-icon start size="small">mdi-translate</v-icon>
        {{ i18n.currentLang.flag }} {{ i18n.locale.toUpperCase() }}
        <v-icon end size="small">mdi-chevron-down</v-icon>
      </v-btn>
    </template>
    <v-list density="compact" min-width="150">
      <v-list-item
        v-for="lang in languages"
        :key="lang.code"
        :active="i18n.locale === lang.code"
        active-color="accent"
        @click="i18n.setLocale(lang.code)"
      >
        <template #prepend>{{ lang.flag }}</template>
        <v-list-item-title class="ml-2">{{ lang.label }}</v-list-item-title>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script setup>
import { useI18nStore, languages } from '@/stores/i18n'
const i18n = useI18nStore()

defineProps({
  btnVariant: { type: String, default: 'text' },
  btnColor: { type: String, default: 'white' },
})
</script>
