<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useOrderStore } from '@/stores/orderStore'
import { useProductStore } from '@/stores/productStore'
import Notification from '@/components/Notification.vue'
import {
  ArrowLeftIcon,
  PlusIcon,
  TrashIcon,
  CheckIcon,
  ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline'

const router = useRouter()
const orderStore = useOrderStore()
const productStore = useProductStore()

const productsLoading = ref(true)
const loading = ref(false)
const form = ref({
  customer_name: '',
  order_date: new Date().toISOString().split('T')[0],
  items: [],
})
const newItem = ref({
  product_id: null,
  quantity: 1,
})
const notification = ref({
  show: false,
  type: 'success',
  message: '',
})
const selectedProductStock = ref(0)

const availableProducts = computed(() => {
  if (!productStore.products || !Array.isArray(productStore.products)) {
    return []
  }

  return productStore.products.filter((product) => {
    if (!product || !product.id) return false
    const stock = Number(product.stock)
    const alreadyAdded = form.value.items.some((item) => item.product_id === product.id)
    return !isNaN(stock) && stock > 0 && !alreadyAdded
  })
})

const canAddItem = computed(() => {
  return (
    newItem.value.product_id &&
    newItem.value.quantity > 0 &&
    newItem.value.quantity <= selectedProductStock.value
  )
})

const totalOrder = computed(() => {
  return form.value.items.reduce((total, item) => {
    return total + getProductPrice(item.product_id) * item.quantity
  }, 0)
})

const updateAvailableStock = () => {
  const product = productStore.products?.find((p) => p?.id === newItem.value.product_id)
  selectedProductStock.value = product?.stock || 0

  if (newItem.value.quantity > selectedProductStock.value) {
    newItem.value.quantity = 1
  }
}

const addItem = () => {
  if (!canAddItem.value) return

  form.value.items.push({
    product_id: newItem.value.product_id,
    quantity: newItem.value.quantity,
    price: getProductPrice(newItem.value.product_id),
  })

  newItem.value = { product_id: null, quantity: 0 }
  selectedProductStock.value = 0
}

const removeItem = (index) => {
  form.value.items.splice(index, 1)
}

const getProductName = (productId) => {
  return productStore.products.find((p) => p.id === productId)?.name || 'Produk tidak ditemukan'
}

const getProductPrice = (productId) => {
  return productStore.products.find((p) => p.id === productId)?.price || 0
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(price)
}

const showNotification = (message, type = 'success') => {
  notification.value = { show: true, message, type }
}

const resetForm = () => {
  form.value = {
    customer_name: '',
    order_date: new Date().toISOString().split('T')[0],
    items: [],
  }
  newItem.value = { product_id: null, quantity: 1 }
  selectedProductStock.value = 0
}

const handleSubmit = async () => {
  try {
    loading.value = true

    if (!form.value.customer_name?.trim()) {
      showNotification('Nama customer harus diisi', 'error')
      return
    }

    if (form.value.items.length === 0) {
      showNotification('Minimal tambahkan satu item pesanan', 'error')
      return
    }

    await orderStore.createOrder({
      customer_name: form.value.customer_name,
      order_date: form.value.order_date,
      items: form.value.items,
      total: totalOrder.value,
    })

    showNotification('Pesanan berhasil dibuat!', 'success')

    setTimeout(() => {
      router.push('/orders')
    }, 1500)
  } catch (error) {
    console.error('Error creating order:', error)
    showNotification(`Gagal membuat pesanan: ${error.message}`, 'error')
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  try {
    productsLoading.value = true
    await productStore.fetchProducts()
  } catch (error) {
    console.error('Error loading products:', error)
    showNotification('Gagal memuat daftar produk', 'error')
  } finally {
    productsLoading.value = false
  }
})
</script>

<template>
  <div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="flex justify-between items-center mb-8">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Buat Pesanan Baru</h1>
        <p class="text-gray-500 mt-1">Isi formulir untuk membuat pesanan baru</p>
      </div>
      <router-link to="/orders" class="flex items-center text-blue-600 hover:text-blue-800">
        <ArrowLeftIcon class="w-5 h-5 mr-1" />
        Kembali ke Daftar Pesanan
      </router-link>
    </div>

    <LoadingSpinner v-if="loading" full-page />

    <
    <form
      @submit.prevent="handleSubmit"
      class="space-y-6 bg-white rounded-xl shadow-sm p-6 border border-gray-100"
    >
      <div class="space-y-4">
        <h2 class="text-lg font-medium text-gray-800 border-b pb-2">Informasi Pelanggan</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Customer*</label>
            <input
              v-model="form.customer_name"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
              placeholder="Nama lengkap pelanggan"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pesanan*</label>
            <input
              v-model="form.order_date"
              type="date"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
        </div>
      </div>

      <div class="space-y-4">
        <h2 class="text-lg font-medium text-gray-800 border-b pb-2">Item Pesanan</h2>

        <div v-if="productsLoading" class="text-center py-6">
          <LoadingSpinner />
          <p class="mt-2 text-gray-500">Memuat daftar produk...</p>
        </div>

        <div v-else class="bg-gray-50 p-4 rounded-lg">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Produk*</label>
              <select
                v-model="newItem.product_id"
                @change="updateAvailableStock"
                :disabled="availableProducts.length === 0"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="" disabled selected>-- Pilih Produk --</option>
                <option
                  v-for="product in availableProducts"
                  :key="product.id"
                  :value="product.id"
                  class="flex justify-between"
                >
                  {{ product.name }} (Stok: {{ product.stock }})
                </option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah*</label>
              <div class="flex">
                <input
                  v-model.number="newItem.quantity"
                  type="number"
                  min="1"
                  :max="selectedProductStock"
                  class="w-full px-3 py-2 border border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500"
                />
                <span class="inline-flex items-center px-3 bg-gray-200 text-gray-700 rounded-r-md">
                  /{{ selectedProductStock }}
                </span>
              </div>
            </div>
          </div>

          <div class="flex justify-end">
            <button
              type="button"
              @click="addItem"
              :disabled="!canAddItem"
              class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              <PlusIcon class="w-5 h-5 mr-1" />
              Tambah Item
            </button>
          </div>

          <div
            v-if="availableProducts.length === 0"
            class="mt-4 p-3 bg-yellow-50 text-yellow-700 rounded-md"
          >
            <p v-if="productStore.products.length === 0" class="text-sm">
              <ExclamationTriangleIcon class="inline w-5 h-5 mr-1" />
              Belum ada produk terdaftar. Silakan tambahkan produk terlebih dahulu.
            </p>
            <p v-else class="text-sm">
              <ExclamationTriangleIcon class="inline w-5 h-5 mr-1" />
              Tidak ada produk dengan stok tersedia.
            </p>
          </div>
        </div>

        <div v-if="form.items.length > 0" class="border rounded-lg overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th
                  class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Produk
                </th>
                <th
                  class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Harga
                </th>
                <th
                  class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Jumlah
                </th>
                <th
                  class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Subtotal
                </th>
                <th
                  class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                ></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr
                v-for="(item, index) in form.items"
                :key="index"
                class="hover:bg-gray-50 transition-colors"
              >
                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ getProductName(item.product_id) }}
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                  {{ formatPrice(getProductPrice(item.product_id)) }}
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                  {{ item.quantity }}
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ formatPrice(getProductPrice(item.product_id) * item.quantity) }}
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                  <button
                    type="button"
                    @click="removeItem(index)"
                    class="text-red-600 hover:text-red-900"
                  >
                    <TrashIcon class="w-5 h-5" />
                  </button>
                </td>
              </tr>
            </tbody>
            <tfoot class="bg-gray-50">
              <tr>
                <td colspan="3" class="px-4 py-3 text-right text-sm font-medium text-gray-500">
                  Total Pesanan
                </td>
                <td class="px-4 py-3 text-sm font-bold text-gray-900">
                  {{ formatPrice(totalOrder) }}
                </td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      <div class="flex justify-end pt-4 border-t border-gray-200">
        <button
          type="button"
          @click="resetForm"
          class="mr-4 px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
        >
          Reset Form
        </button>
        <button
          type="submit"
          :disabled="form.items.length === 0 || loading"
          class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center"
        >
          <CheckIcon class="w-5 h-5 mr-1" v-if="!loading" />

          Buat Pesanan
        </button>
      </div>
    </form>

    <Notification
      v-if="notification.show"
      :type="notification.type"
      :message="notification.message"
      @close="notification.show = false"
      class="fixed bottom-4 right-4 z-50"
    />
  </div>
</template>
