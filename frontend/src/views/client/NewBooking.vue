<template>
  <div>
    <div class="text-h5 font-weight-bold text-primary mb-6">Yangi bron</div>

    <v-stepper v-model="step" :items="['Asosiy', 'Menyu', 'Xizmatlar', 'Tasdiqlash']" flat>
      <template #item.1>
        <v-card flat>
          <v-card-text>
            <v-text-field v-model="form.event_date" label="Tadbir sanasi" type="date" class="mb-3" :min="today" />
            <v-text-field v-model="form.event_time" label="Vaqt" type="time" class="mb-3" />
            <v-text-field v-model.number="form.guest_count" label="Mehmonlar soni" type="number" min="1" class="mb-3" />
            <v-textarea v-model="form.notes" label="Izoh (ixtiyoriy)" rows="3" />
          </v-card-text>
          <v-card-actions class="pa-4">
            <v-spacer />
            <v-btn color="primary" :disabled="!form.event_date || !form.guest_count" @click="step = 2">Davom etish</v-btn>
          </v-card-actions>
        </v-card>
      </template>

      <template #item.2>
        <v-card flat>
          <v-card-text>
            <v-tabs v-model="activeMenuTab" color="accent" class="mb-4">
              <v-tab v-for="cat in menuCategories" :key="cat.id" :value="cat.id">{{ cat.name }}</v-tab>
            </v-tabs>
            <v-row>
              <v-col v-for="item in currentMenuItems" :key="item.id" cols="12" sm="6" md="4">
                <v-card :color="selectedMenuItems[item.id] ? 'primary' : ''" variant="outlined" class="pa-3">
                  <div class="d-flex align-center justify-space-between mb-2">
                    <div class="text-body-2 font-weight-medium">{{ item.name }}</div>
                    <span class="text-caption font-weight-bold" style="color: #C8941A;">{{ Number(item.price).toLocaleString() }} so'm</span>
                  </div>
                  <v-text-field
                    v-model.number="selectedMenuItems[item.id]"
                    density="compact"
                    label="Miqdor"
                    type="number"
                    :min="0"
                    hide-details
                    @update:modelValue="v => { if (!v) delete selectedMenuItems[item.id] }"
                  />
                </v-card>
              </v-col>
            </v-row>
          </v-card-text>
          <v-card-actions class="pa-4">
            <v-btn variant="outlined" @click="step = 1">Orqaga</v-btn>
            <v-spacer />
            <v-btn color="primary" @click="step = 3">Davom etish</v-btn>
          </v-card-actions>
        </v-card>
      </template>

      <template #item.3>
        <v-card flat>
          <v-card-text>
            <v-row>
              <v-col v-for="service in services" :key="service.id" cols="12" sm="6">
                <v-card variant="outlined" class="pa-3" :color="selectedServices[service.id] ? 'primary' : ''">
                  <div class="d-flex align-center justify-space-between mb-2">
                    <div class="text-body-2 font-weight-medium">{{ service.name }}</div>
                    <span class="text-caption font-weight-bold" style="color: #C8941A;">{{ Number(service.price).toLocaleString() }} so'm</span>
                  </div>
                  <div class="text-caption text-medium-emphasis mb-2">{{ service.description }}</div>
                  <v-text-field
                    v-model.number="selectedServices[service.id]"
                    density="compact"
                    label="Miqdor"
                    type="number"
                    :min="0"
                    hide-details
                    @update:modelValue="v => { if (!v) delete selectedServices[service.id] }"
                  />
                </v-card>
              </v-col>
            </v-row>
          </v-card-text>
          <v-card-actions class="pa-4">
            <v-btn variant="outlined" @click="step = 2">Orqaga</v-btn>
            <v-spacer />
            <v-btn color="primary" @click="step = 4">Davom etish</v-btn>
          </v-card-actions>
        </v-card>
      </template>

      <template #item.4>
        <v-card flat>
          <v-card-text>
            <v-card variant="tonal" color="primary" class="pa-4 mb-4">
              <v-row>
                <v-col cols="6"><div class="text-caption">Sana</div><div class="font-weight-bold">{{ form.event_date }}</div></v-col>
                <v-col cols="6"><div class="text-caption">Mehmonlar</div><div class="font-weight-bold">{{ form.guest_count }} kishi</div></v-col>
              </v-row>
            </v-card>

            <div v-if="summaryItems.length === 0" class="text-center text-medium-emphasis pa-4">
              Hech qanday mahsulot tanlanmagan
            </div>

            <v-list v-else lines="two">
              <v-list-item
                v-for="item in summaryItems"
                :key="item.key"
                :title="item.name"
                :subtitle="`${item.quantity} × ${Number(item.price).toLocaleString()} so'm`"
              >
                <template #append>
                  <span class="font-weight-bold">{{ Number(item.subtotal).toLocaleString() }} so'm</span>
                </template>
              </v-list-item>
            </v-list>

            <v-divider class="my-3" />
            <div class="d-flex justify-space-between text-h6">
              <span>Jami:</span>
              <span class="font-weight-bold text-primary">{{ Number(totalAmount).toLocaleString() }} so'm</span>
            </div>

            <v-alert v-if="errorMessage" type="error" variant="tonal" class="mt-4" rounded="lg">
              {{ errorMessage }}
            </v-alert>
          </v-card-text>
          <v-card-actions class="pa-4">
            <v-btn variant="outlined" @click="step = 3">Orqaga</v-btn>
            <v-spacer />
            <v-btn color="primary" :loading="submitting" :disabled="summaryItems.length === 0" @click="submitBooking">
              <v-icon start>mdi-calendar-check</v-icon>
              Bron so'rovini yuborish
            </v-btn>
          </v-card-actions>
        </v-card>
      </template>
    </v-stepper>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useNotificationStore } from '@/stores/notification'
import api from '@/plugins/axios'

const router = useRouter()
const notification = useNotificationStore()

const step = ref(1)
const submitting = ref(false)
const errorMessage = ref('')
const today = new Date().toISOString().split('T')[0]

const form = ref({ event_date: '', event_time: '', guest_count: 1, notes: '' })
const menuCategories = ref([])
const services = ref([])
const activeMenuTab = ref(null)
const selectedMenuItems = ref({})
const selectedServices = ref({})

const currentMenuItems = computed(() => {
  if (!activeMenuTab.value) return []
  const cat = menuCategories.value.find(c => c.id === activeMenuTab.value)
  return cat?.items || []
})

const allMenuItems = computed(() => menuCategories.value.flatMap(c => c.items))

const summaryItems = computed(() => {
  const items = []
  for (const [id, qty] of Object.entries(selectedMenuItems.value)) {
    if (qty > 0) {
      const item = allMenuItems.value.find(i => i.id === Number(id))
      if (item) items.push({ key: `m${id}`, type: 'menu_item', id: Number(id), name: item.name, price: item.price, quantity: qty, subtotal: item.price * qty })
    }
  }
  for (const [id, qty] of Object.entries(selectedServices.value)) {
    if (qty > 0) {
      const service = services.value.find(s => s.id === Number(id))
      if (service) items.push({ key: `s${id}`, type: 'service', id: Number(id), name: service.name, price: service.price, quantity: qty, subtotal: service.price * qty })
    }
  }
  return items
})

const totalAmount = computed(() => summaryItems.value.reduce((sum, i) => sum + i.subtotal, 0))

async function submitBooking() {
  submitting.value = true
  errorMessage.value = ''
  try {
    const items = summaryItems.value.map(i => ({ type: i.type, id: i.id, quantity: i.quantity }))
    await api.post('/bookings', { ...form.value, items })
    notification.showSuccess("Bron so'rovi muvaffaqiyatli yuborildi!")
    router.push('/client/bookings')
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Xato yuz berdi'
  } finally {
    submitting.value = false
  }
}

onMounted(async () => {
  const [menuRes, servicesRes] = await Promise.all([
    api.get('/menu'),
    api.get('/services'),
  ])
  menuCategories.value = menuRes.data.data
  services.value = servicesRes.data.data
  if (menuCategories.value.length > 0) activeMenuTab.value = menuCategories.value[0].id
})
</script>
