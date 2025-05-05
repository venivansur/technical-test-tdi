<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import {
  ShoppingBagIcon,
  PlusIcon,
  PencilSquareIcon,
  TrashIcon,
  MagnifyingGlassIcon,
} from '@heroicons/vue/24/outline'
import { useProductStore } from '@/stores/productStore'
import ProductFormModal from '@/components/ProductFormModal.vue'
import Notification from '@/components/Notification.vue'
import Swal from 'sweetalert2'
const productStore = useProductStore()
const products = ref([])
const selectedProduct = ref(null)
const showForm = ref(false)
const searchQuery = ref('')
const stockFilter = ref('all')

const notification = ref({
  show: false,
  type: 'success',
  message: '',
})

const inStockCount = computed(() => products.value.filter((p) => p.stock > 10).length)
const lowStockCount = computed(
  () => products.value.filter((p) => p.stock <= 10 && p.stock > 0).length,
)
const outOfStockCount = computed(() => products.value.filter((p) => p.stock === 0).length)

const filteredProducts = computed(() => {
  return products.value.filter((product) => {
    const matchesSearch =
      product.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      product.id.toString().includes(searchQuery.value)

    switch (stockFilter.value) {
      case 'in-stock':
        return matchesSearch && product.stock > 10
      case 'low-stock':
        return matchesSearch && product.stock <= 10 && product.stock > 0
      case 'out-of-stock':
        return matchesSearch && product.stock === 0
      default:
        return matchesSearch
    }
  })
})

const fetchProducts = async () => {
  try {
    products.value = await productStore.fetchProducts()
  } catch (error) {
    console.error('Gagal memuat produk:', error)
    showNotification('Gagal memuat daftar produk', 'error')
  }
}

const showNotification = (message, type = 'success') => {
  notification.value.show = false
  nextTick(() => {
    notification.value = {
      show: true,
      type,
      message,
    }

    const timer = setTimeout(() => {
      notification.value.show = false
      clearTimeout(timer)
    }, 5000)
  })
}

const openForm = () => {
  selectedProduct.value = null
  showForm.value = true
}

const editProduct = (product) => {
  selectedProduct.value = { ...product }
  showForm.value = true
}

const deleteProduct = async (id) => {
  const { isConfirmed } = await Swal.fire({
    title: 'Hapus Produk?',
    text: 'Anda tidak dapat mengembalikan ini!',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus!',
  })

  if (!isConfirmed) return

  try {
    await productStore.deleteProduct(id)
    products.value = products.value.filter((p) => p.id !== id)
    Swal.fire('Terhapus!', 'Produk berhasil dihapus.', 'success')
  } catch (error) {
    Swal.fire('Gagal!', error.message, 'error')
  }
}

const handleSave = async (productData) => {
  try {
    if (productData.id) {
      const updated = await productStore.updateProduct(productData)
      const index = products.value.findIndex((p) => p.id === productData.id)
      if (index !== -1) {
        products.value.splice(index, 1, updated)
      }
      showNotification(`Produk "${updated.name}" berhasil diperbarui`)
    } else {
      const newProduct = await productStore.addProduct(productData)

      if (!products.value.some((p) => p.id === newProduct.id)) {
        products.value.push(newProduct)
      }
      showNotification(`Produk "${newProduct.name}" berhasil ditambahkan`)
    }
    showForm.value = false
  } catch (error) {
    console.error('Gagal menyimpan produk:', error)
    showNotification(`Gagal menyimpan produk: ${error.message}`, 'error')
  }
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(price)
}

const resetFilters = () => {
  searchQuery.value = ''
  stockFilter.value = 'all'
}

onMounted(() => {
  fetchProducts()
})
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Manajemen Produk</h1>
        <p class="text-gray-500 mt-1">Kelola inventaris produk Anda dengan efisien</p>
      </div>
      <button
        @click="openForm"
        class="bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white px-6 py-3 rounded-lg flex items-center gap-2 transition-all shadow-md hover:shadow-lg"
      >
        <PlusIcon class="w-5 h-5" />
        <span class="font-medium">Tambah Produk</span>
      </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
      <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <p class="text-sm text-gray-500">Total Produk</p>
        <p class="text-2xl font-bold text-gray-800">{{ products.length }}</p>
      </div>
      <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <p class="text-sm text-gray-500">Stok Tersedia</p>
        <p class="text-2xl font-bold text-green-600">{{ inStockCount }}</p>
      </div>
      <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <p class="text-sm text-gray-500">Stok Menipis</p>
        <p class="text-2xl font-bold text-yellow-600">{{ lowStockCount }}</p>
      </div>
      <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <p class="text-sm text-gray-500">Stok Habis</p>
        <p class="text-2xl font-bold text-red-600">{{ outOfStockCount }}</p>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100">
      <div class="flex flex-col md:flex-row gap-4">
        <div class="relative flex-grow">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
          </div>
          <input
            type="text"
            v-model="searchQuery"
            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
            placeholder="Cari produk..."
          />
        </div>
        <select
          v-model="stockFilter"
          class="border border-gray-300 rounded-lg bg-gray-50 px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
        >
          <option value="all">Semua Status Stok</option>
          <option value="in-stock">Stok Tersedia</option>
          <option value="low-stock">Stok Menipis</option>
          <option value="out-of-stock">Stok Habis</option>
        </select>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Produk
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Harga
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Stok
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Status
              </th>
              <th
                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Aksi
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="product in filteredProducts"
              :key="product.id"
              class="hover:bg-gray-50 transition-colors"
            >
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div
                    class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center"
                  >
                    <ShoppingBagIcon class="h-6 w-6 text-blue-600" />
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                    <div class="text-sm text-gray-500">ID: {{ product.id }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ formatPrice(product.price) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <span class="text-sm font-medium mr-2">{{ product.stock }}</span>
                  <div class="w-24 bg-gray-200 rounded-full h-2">
                    <div
                      class="bg-blue-600 h-2 rounded-full"
                      :style="{ width: `${Math.min(100, (product.stock / 50) * 100)}%` }"
                    ></div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="{
                    'bg-green-100 text-green-800': product.stock > 10,
                    'bg-yellow-100 text-yellow-800': product.stock <= 10 && product.stock > 0,
                    'bg-red-100 text-red-800': product.stock === 0,
                  }"
                >
                  {{ product.stock > 10 ? 'Tersedia' : product.stock > 0 ? 'Menipis' : 'Habis' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex justify-end gap-3">
                  <button
                    @click="editProduct(product)"
                    class="text-blue-600 hover:text-blue-900 flex items-center gap-1"
                  >
                    <PencilSquareIcon class="w-4 h-4" />
                    <span class="hidden md:inline">Edit</span>
                  </button>
                  <button
                    @click="deleteProduct(product.id)"
                    class="text-red-600 hover:text-red-900 flex items-center gap-1"
                  >
                    <TrashIcon class="w-4 h-4" />
                    <span class="hidden md:inline">Hapus</span>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="filteredProducts.length === 0" class="text-center py-16 bg-gray-50 rounded-b-xl">
        <ShoppingBagIcon class="mx-auto h-16 w-16 text-gray-300" />
        <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak ada produk ditemukan</h3>
        <p class="mt-1 text-gray-500">
          {{
            searchQuery || stockFilter !== 'all'
              ? 'Coba sesuaikan pencarian atau filter Anda'
              : 'Mulai dengan menambahkan produk pertama Anda'
          }}
        </p>
        <div class="mt-6">
          <button
            @click="resetFilters"
            v-if="searchQuery || stockFilter !== 'all'"
            class="text-blue-600 hover:text-blue-800 text-sm font-medium"
          >
            Reset filter
          </button>
          <button
            @click="openForm"
            v-else
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none"
          >
            <PlusIcon class="-ml-1 mr-2 h-5 w-5" />
            Tambah Produk
          </button>
        </div>
      </div>
    </div>

    <ProductFormModal
      :isOpen="showForm"
      :product="selectedProduct"
      @close="showForm = false"
      @save="handleSave"
    />
    <Notification
      v-if="notification.show"
      :type="notification.type"
      :message="notification.message"
      @close="notification.show = false"
    />
  </div>
</template>
