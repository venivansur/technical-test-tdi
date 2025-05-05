<script setup>
import { ref, computed, watch } from 'vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import Notification from './Notification.vue'

const props = defineProps({
  isOpen: Boolean,
  product: {
    type: Object,
    default: () => ({
      id: null,
      name: '',
      price: 0,
      stock: 0,
    }),
  },
})

const emit = defineEmits(['close', 'save'])

const form = ref({ ...props.product })
const touched = ref({
  name: false,
  price: false,
  stock: false,
})

const showNotification = ref(false)
const notificationType = ref('success')
const notificationMessage = ref('')

watch(
  () => props.product,
  (newProduct) => {
    form.value = { ...newProduct }
    touched.value = { name: false, price: false, stock: false }
  },
  { deep: true },
)

const isFormValid = computed(() => {
  return form.value.name?.trim().length >= 3 && form.value.price > 0 && form.value.stock >= 0
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(price)
}

const handleNumberInput = (field, value) => {
  form.value[field] = value === '' ? '' : Number(value)
}

const handleClose = () => {
  emit('close')
}

const handleSubmit = async () => {
  touched.value = {
    name: true,
    price: true,
    stock: true,
  }

  if (!isFormValid.value) return

  try {
    const productData = {
      ...form.value,
      price: Number(form.value.price),
      stock: Number(form.value.stock),
    }

    if (!isSubmitting.value) {
      isSubmitting.value = true

      await emit('save', productData)

      showNotification.value = true
      notificationType.value = 'success'
      notificationMessage.value = `Produk "${productData.name}" berhasil ${
        productData.id ? 'diperbarui' : 'ditambahkan'
      }`

      setTimeout(() => {
        showNotification.value = false
        isSubmitting.value = false
        emit('close')
      }, 1500)
    }
  } catch (error) {
    showNotification.value = true
    notificationType.value = 'error'
    notificationMessage.value = `Gagal menyimpan produk: ${error.message}`
    isSubmitting.value = false

    setTimeout(() => {
      showNotification.value = false
    }, 5000)
  }
}

const isSubmitting = ref(false)
</script>

<template>
  <transition
    enter-active-class="transition ease-out duration-200"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition ease-in duration-150"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
      <div
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        aria-hidden="true"
        @click="handleClose"
      ></div>

      <div class="flex items-center justify-center min-h-screen p-4 sm:p-0">
        <div
          class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-lg"
        >
          <div class="bg-white px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-medium text-gray-900">
                {{ form.id ? 'Edit Produk' : 'Tambah Produk Baru' }}
              </h3>
              <button
                type="button"
                @click="handleClose"
                class="text-gray-400 hover:text-gray-500 focus:outline-none"
              >
                <span class="sr-only">Close</span>
                <XMarkIcon class="h-6 w-6" />
              </button>
            </div>
            <p v-if="form.id" class="mt-1 text-sm text-gray-500">ID Produk: {{ form.id }}</p>
          </div>

          <div class="bg-white px-6 py-4">
            <form @submit.prevent="handleSubmit" class="space-y-4">
              <div>
                <label for="product-name" class="block text-sm font-medium text-gray-700">
                  Nama Produk*
                </label>
                <div class="mt-1 relative rounded-md shadow-sm">
                  <input
                    id="product-name"
                    v-model.trim="form.name"
                    type="text"
                    required
                    minlength="3"
                    @blur="touched.name = true"
                    :class="[
                      'block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none sm:text-sm',
                      touched.name && form.name.length < 3
                        ? 'border-red-300 focus:ring-red-500 focus:border-red-500'
                        : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500',
                    ]"
                    placeholder="Nama produk"
                  />
                </div>
                <p v-if="touched.name && form.name.length < 3" class="mt-1 text-sm text-red-600">
                  Nama produk minimal 3 karakter
                </p>
              </div>

              <div>
                <label for="product-price" class="block text-sm font-medium text-gray-700">
                  Harga (IDR)*
                </label>
                <div class="mt-1 relative rounded-md shadow-sm">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 sm:text-sm">Rp</span>
                  </div>
                  <input
                    id="product-price"
                    v-model.number="form.price"
                    @input="handleNumberInput('price', $event.target.value)"
                    type="number"
                    min="1"
                    step="100"
                    required
                    @blur="touched.price = true"
                    :class="[
                      'block w-full pl-12 pr-3 py-2 border rounded-md shadow-sm focus:outline-none sm:text-sm',
                      touched.price && form.price <= 0
                        ? 'border-red-300 focus:ring-red-500 focus:border-red-500'
                        : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500',
                    ]"
                    placeholder="0"
                  />
                </div>
                <p v-if="touched.price && form.price <= 0" class="mt-1 text-sm text-red-600">
                  Harga harus lebih dari 0
                </p>
                <p v-else class="mt-1 text-sm text-gray-500">
                  Harga: {{ formatPrice(form.price) }}
                </p>
              </div>

              <div>
                <label for="product-stock" class="block text-sm font-medium text-gray-700">
                  Stok*
                </label>
                <div class="mt-1 relative rounded-md shadow-sm">
                  <input
                    id="product-stock"
                    v-model.number="form.stock"
                    type="number"
                    min="0"
                    required
                    @blur="touched.stock = true"
                    :class="[
                      'block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none sm:text-sm',
                      touched.stock && form.stock < 0
                        ? 'border-red-300 focus:ring-red-500 focus:border-red-500'
                        : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500',
                    ]"
                    placeholder="Jumlah stok"
                  />
                </div>
                <p v-if="touched.stock && form.stock < 0" class="mt-1 text-sm text-red-600">
                  Stok tidak boleh negatif
                </p>
              </div>
            </form>
          </div>

          <div class="bg-gray-50 px-6 py-4 flex justify-end border-t border-gray-200">
            <button
              type="button"
              @click="handleClose"
              class="mr-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Batal
            </button>
            <button
              type="submit"
              @click="handleSubmit"
              :disabled="!isFormValid"
              :class="[
                'inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white focus:outline-none focus:ring-2 focus:ring-offset-2',
                isFormValid
                  ? 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500'
                  : 'bg-blue-400 cursor-not-allowed',
              ]"
            >
              Simpan
            </button>
          </div>
        </div>
      </div>
    </div>
  </transition>

  <Notification
    v-if="showNotification"
    :type="notificationType"
    :message="notificationMessage"
    @close="showNotification = false"
  />
</template>
