<template>
  <div>
    <div class="text-h5 font-weight-bold text-primary mb-6">Menyu</div>

    <v-skeleton-loader v-if="loading" type="card" />

    <template v-else>
      <v-tabs v-model="activeTab" color="accent" class="mb-6">
        <v-tab v-for="cat in categories" :key="cat.id" :value="cat.id">
          {{ cat.name }}
        </v-tab>
      </v-tabs>

      <v-row>
        <v-col
          v-for="item in currentItems"
          :key="item.id"
          cols="12" sm="6" md="4" lg="3"
        >
          <v-card height="100%">
            <v-img
              :src="item.image ? `/storage/${item.image}` : 'https://placehold.co/300x160/1A2744/C8941A?text=OldKhiva'"
              height="160"
              cover
            />
            <v-card-title class="text-body-1 pb-1">{{ item.name }}</v-card-title>
            <v-card-text>
              <div v-if="item.description" class="text-caption text-medium-emphasis mb-2">{{ item.description }}</div>
              <div class="d-flex align-center justify-space-between">
                <span class="text-body-1 font-weight-bold" style="color: #C8941A;">
                  {{ Number(item.price).toLocaleString() }} so'm
                </span>
                <v-chip size="small" color="primary" variant="tonal">{{ item.unit }}</v-chip>
              </div>
              <div class="text-caption text-medium-emphasis mt-1">Min buyurtma: {{ item.min_order }} {{ item.unit }}</div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/plugins/axios'

const loading = ref(true)
const categories = ref([])
const activeTab = ref(null)

const currentItems = computed(() => {
  if (!activeTab.value) return []
  const cat = categories.value.find(c => c.id === activeTab.value)
  return cat?.items || []
})

onMounted(async () => {
  try {
    const res = await api.get('/menu')
    categories.value = res.data.data
    if (categories.value.length > 0) activeTab.value = categories.value[0].id
  } finally {
    loading.value = false
  }
})
</script>
