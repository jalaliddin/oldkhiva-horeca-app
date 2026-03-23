<template>
  <div>
    <div class="text-h5 font-weight-bold text-primary mb-6">Menyu boshqaruvi</div>

    <v-tabs v-model="tab" color="accent" class="mb-6">
      <v-tab value="categories">Kategoriyalar</v-tab>
      <v-tab value="items">Taomlar</v-tab>
    </v-tabs>

    <v-window v-model="tab">
      <!-- Categories -->
      <v-window-item value="categories">
        <v-row>
          <v-col cols="12" md="5">
            <v-card class="pa-4">
              <v-card-title class="pa-0 mb-4 text-primary">
                {{ editingCategory ? 'Tahrirlash' : 'Yangi kategoriya' }}
              </v-card-title>
              <v-text-field v-model="catForm.name" label="Nomi (UZ)" class="mb-2" />
              <v-text-field v-model="catForm.name_ru" label="Nomi (RU)" class="mb-2" />
              <v-text-field v-model="catForm.name_en" label="Nomi (EN)" class="mb-2" />
              <v-text-field v-model.number="catForm.sort_order" label="Tartib raqami" type="number" class="mb-3" />
              <v-switch v-model="catForm.is_active" label="Faol" color="primary" class="mb-3" />
              <v-btn color="primary" block @click="saveCategory">Saqlash</v-btn>
              <v-btn v-if="editingCategory" variant="outlined" block class="mt-2" @click="cancelEditCategory">Bekor</v-btn>
            </v-card>
          </v-col>
          <v-col cols="12" md="7">
            <v-card>
              <v-data-table :headers="catHeaders" :items="categories" :loading="loadingCats" density="compact">
                <template #item.is_active="{ item }">
                  <v-chip :color="item.is_active ? 'success' : 'grey'" size="x-small" variant="tonal">
                    {{ item.is_active ? 'Faol' : 'Nofaol' }}
                  </v-chip>
                </template>
                <template #item.actions="{ item }">
                  <v-btn size="small" icon="mdi-pencil" variant="text" @click="editCategory(item)" />
                  <v-btn size="small" icon="mdi-delete" variant="text" color="error" @click="deleteCategory(item.id)" />
                </template>
              </v-data-table>
            </v-card>
          </v-col>
        </v-row>
      </v-window-item>

      <!-- Items -->
      <v-window-item value="items">
        <v-row>
          <v-col cols="12" md="5">
            <v-card class="pa-4">
              <v-card-title class="pa-0 mb-4 text-primary">
                {{ editingItem ? 'Tahrirlash' : 'Yangi taom' }}
              </v-card-title>
              <v-select v-model="itemForm.category_id" label="Kategoriya" :items="categoryOptions" item-title="name" item-value="id" class="mb-2" />
              <v-text-field v-model="itemForm.name" label="Nomi" class="mb-2" />
              <v-text-field v-model.number="itemForm.price" label="Narxi (so'm)" type="number" class="mb-2" />
              <v-select v-model="itemForm.unit" label="O'lchov" :items="['portion', 'kg', 'litr', 'dona']" class="mb-2" />
              <v-text-field v-model.number="itemForm.min_order" label="Min buyurtma" type="number" class="mb-2" />
              <v-text-field v-model.number="itemForm.sort_order" label="Tartib" type="number" class="mb-3" />
              <v-switch v-model="itemForm.is_active" label="Faol" color="primary" class="mb-2" />

              <!-- Image upload -->
              <v-img
                v-if="itemForm.currentImage && !itemImageFile"
                :src="`/storage/${itemForm.currentImage}`"
                height="120"
                cover
                rounded="lg"
                class="mb-2"
              />
              <v-file-input
                v-model="itemImageFile"
                label="Rasm (ixtiyoriy)"
                accept="image/*"
                prepend-icon="mdi-image"
                clearable
                class="mb-3"
              />

              <v-btn color="primary" block :loading="savingItem" @click="saveItem">Saqlash</v-btn>
              <v-btn v-if="editingItem" variant="outlined" block class="mt-2" @click="cancelEditItem">Bekor</v-btn>
            </v-card>
          </v-col>
          <v-col cols="12" md="7">
            <v-card>
              <v-data-table :headers="itemHeaders" :items="menuItems" :loading="loadingItems" density="compact">
                <template #item.image="{ item }">
                  <v-avatar v-if="item.image" size="36" rounded="sm" class="my-1">
                    <v-img :src="`/storage/${item.image}`" cover />
                  </v-avatar>
                  <v-icon v-else color="grey-lighten-1" size="28">mdi-image-off</v-icon>
                </template>
                <template #item.price="{ item }">{{ Number(item.price).toLocaleString() }} so'm</template>
                <template #item.is_active="{ item }">
                  <v-chip :color="item.is_active ? 'success' : 'grey'" size="x-small" variant="tonal">
                    {{ item.is_active ? 'Faol' : 'Nofaol' }}
                  </v-chip>
                </template>
                <template #item.actions="{ item }">
                  <v-btn size="small" icon="mdi-pencil" variant="text" @click="editItem(item)" />
                  <v-btn size="small" icon="mdi-delete" variant="text" color="error" @click="deleteItem(item.id)" />
                </template>
              </v-data-table>
            </v-card>
          </v-col>
        </v-row>
      </v-window-item>
    </v-window>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useNotificationStore } from '@/stores/notification'
import api from '@/plugins/axios'

const notification = useNotificationStore()
const tab = ref('categories')
const categories = ref([])
const menuItems = ref([])
const loadingCats = ref(true)
const loadingItems = ref(true)
const savingItem = ref(false)
const editingCategory = ref(null)
const editingItem = ref(null)
const itemImageFile = ref(null)

const catForm = ref({ name: '', name_ru: '', name_en: '', sort_order: 0, is_active: true })
const itemForm = ref({ category_id: null, name: '', price: 0, unit: 'portion', min_order: 1, sort_order: 0, is_active: true, currentImage: null })

const catHeaders = [
  { title: 'Nomi', key: 'name' },
  { title: 'Taomlar', key: 'items_count' },
  { title: 'Status', key: 'is_active' },
  { title: '', key: 'actions', sortable: false },
]

const itemHeaders = [
  { title: 'Rasm', key: 'image', sortable: false, width: '60px' },
  { title: 'Nomi', key: 'name' },
  { title: 'Kategoriya', key: 'category.name' },
  { title: 'Narxi', key: 'price' },
  { title: "O'lchov", key: 'unit' },
  { title: 'Status', key: 'is_active' },
  { title: '', key: 'actions', sortable: false },
]

const categoryOptions = computed(() => categories.value)

function editCategory(cat) {
  editingCategory.value = cat.id
  catForm.value = { name: cat.name, name_ru: cat.name_ru, name_en: cat.name_en, sort_order: cat.sort_order, is_active: cat.is_active }
}

function cancelEditCategory() {
  editingCategory.value = null
  catForm.value = { name: '', name_ru: '', name_en: '', sort_order: 0, is_active: true }
}

async function saveCategory() {
  try {
    if (editingCategory.value) {
      await api.put(`/admin/menu-categories/${editingCategory.value}`, catForm.value)
      notification.showSuccess('Kategoriya yangilandi!')
    } else {
      await api.post('/admin/menu-categories', catForm.value)
      notification.showSuccess("Kategoriya qo'shildi!")
    }
    cancelEditCategory()
    await fetchCategories()
  } catch {
    notification.showError('Xato yuz berdi')
  }
}

async function deleteCategory(id) {
  await api.delete(`/admin/menu-categories/${id}`)
  notification.showSuccess("Kategoriya o'chirildi!")
  await fetchCategories()
}

function editItem(item) {
  editingItem.value = item.id
  itemImageFile.value = null
  itemForm.value = {
    category_id: item.category_id,
    name: item.name,
    price: item.price,
    unit: item.unit,
    min_order: item.min_order,
    sort_order: item.sort_order,
    is_active: item.is_active,
    currentImage: item.image,
  }
}

function cancelEditItem() {
  editingItem.value = null
  itemImageFile.value = null
  itemForm.value = { category_id: null, name: '', price: 0, unit: 'portion', min_order: 1, sort_order: 0, is_active: true, currentImage: null }
}

async function saveItem() {
  savingItem.value = true
  try {
    const file = Array.isArray(itemImageFile.value) ? itemImageFile.value[0] : itemImageFile.value

    const formData = new FormData()
    formData.append('category_id', itemForm.value.category_id)
    formData.append('name', itemForm.value.name)
    formData.append('price', itemForm.value.price)
    formData.append('unit', itemForm.value.unit || 'portion')
    formData.append('min_order', itemForm.value.min_order || 1)
    formData.append('sort_order', itemForm.value.sort_order || 0)
    formData.append('is_active', itemForm.value.is_active ? '1' : '0')
    if (file) {
      formData.append('image', file)
    }

    if (editingItem.value) {
      formData.append('_method', 'PUT')
      await api.post(`/admin/menu-items/${editingItem.value}`, formData)
      notification.showSuccess('Taom yangilandi!')
    } else {
      await api.post('/admin/menu-items', formData)
      notification.showSuccess("Taom qo'shildi!")
    }
    cancelEditItem()
    await fetchItems()
  } catch {
    notification.showError('Xato yuz berdi')
  } finally {
    savingItem.value = false
  }
}

async function deleteItem(id) {
  await api.delete(`/admin/menu-items/${id}`)
  notification.showSuccess("Taom o'chirildi!")
  await fetchItems()
}

async function fetchCategories() {
  loadingCats.value = true
  const res = await api.get('/admin/menu-categories')
  categories.value = res.data.data
  loadingCats.value = false
}

async function fetchItems() {
  loadingItems.value = true
  const res = await api.get('/admin/menu-items')
  menuItems.value = res.data.data.data || res.data.data
  loadingItems.value = false
}

onMounted(async () => {
  await Promise.all([fetchCategories(), fetchItems()])
})
</script>
