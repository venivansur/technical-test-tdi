<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-2xl shadow-lg p-6 w-full max-w-md">
      <h3 class="text-xl font-semibold mb-4">{{ form.id ? 'Edit' : 'Tambah' }} Produk</h3>

      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
          <input
            v-model="form.name"
            type="text"
            required
            minlength="3"
            @blur="touched.name = true"
            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:ring-blue-300"
          />
          <p v-if="touched.name && form.name.length < 3" class="text-red-500 text-sm mt-1">
            Nama minimal 3 karakter
          </p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
          <input
            v-model.number="form.price"
            type="number"
            min="0.01"
            step="0.01"
            required
            @blur="touched.price = true"
            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:ring-blue-300"
          />
          <p v-if="touched.price && form.price <= 0" class="text-red-500 text-sm mt-1">
            Harga harus lebih dari 0
          </p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
          <input
            v-model.number="form.stock"
            type="number"
            min="0"
            required
            @blur="touched.stock = true"
            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:ring-blue-300"
          />
          <p v-if="touched.stock && form.stock < 0" class="text-red-500 text-sm mt-1">
            Stok tidak boleh negatif
          </p>
        </div>

        <div class="flex justify-end gap-2 pt-4">
          <button
            type="button"
            @click="$emit('close')"
            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-md"
          >
            Batal
          </button>
          <button
            type="submit"
            :disabled="!isFormValid"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md disabled:opacity-50"
          >
            Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  isOpen: Boolean,
  product: Object,
  product: {
    type: Object,
    default: () => ({ id: null, name: '', price: 0, stock: 0 }),
  },
})

const emit = defineEmits(['close', 'save'])

const form = ref({ ...props.product })

const touched = ref({
  name: false,
  price: false,
  stock: false,
})

watch(
  () => props.product,
  (newVal) => {
    form.value = { ...newVal }
    touched.value = { name: false, price: false, stock: false }
  },
)

const isFormValid = computed(() => {
  return (
    form.value.name && form.value.name.length >= 3 && form.value.price > 0 && form.value.stock >= 0
  )
})

const handleSubmit = () => {
  if (!isFormValid.value) return
  emit('save', form.value)
}
</script>
