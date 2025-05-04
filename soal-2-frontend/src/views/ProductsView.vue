<template>
  <div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold text-gray-800">Manajemen Produk</h1>
      <button 
        @click="openForm"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors"
      >
        <PlusIcon class="w-5 h-5" />
        Tambah Produk
      </button>
    </div>

    <!-- Tabel Produk -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr 
            v-for="product in products" 
            :key="product.id"
            class="hover:bg-gray-50 transition-colors"
          >
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ product.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatPrice(product.price) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <span :class="{
                'text-green-600': product.stock > 10,
                'text-yellow-600': product.stock <= 10 && product.stock > 0,
                'text-red-600': product.stock === 0
              }">
                {{ product.stock }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm flex gap-2">
              <button 
                @click="editProduct(product)"
                class="text-indigo-600 hover:text-indigo-900"
              >
                <PencilSquareIcon class="w-5 h-5" />
              </button>
              <button 
                @click="deleteProduct(product.id)"
                class="text-red-600 hover:text-red-900"
              >
                <TrashIcon class="w-5 h-5" />
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Empty State -->
    <div v-if="products.length === 0" class="text-center py-12">
      <ShoppingBagIcon class="mx-auto h-12 w-12 text-gray-400" />
      <h3 class="mt-2 text-lg font-medium text-gray-900">Belum ada produk</h3>
      <p class="mt-1 text-gray-500">Tambahkan produk pertama Anda</p>
    </div>

    <!-- Form Modal -->
    <ProductFormModal 
      :isOpen="showForm"
      :product="selectedProduct"
      @close="showForm = false"
      @save="handleSave"
    />
  </div>
</template>

<script setup>
import { ShoppingBagIcon, PlusIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline'
import { ref, onMounted } from 'vue'
import { useProductStore } from '@/stores/productStore'
import ProductFormModal from '@/components/ProductFormModal.vue'

const productStore = useProductStore()
const products = ref([])
const selectedProduct = ref(null)
const showForm = ref(false)
// Fetch data saat komponen dimuat
onMounted(async () => {
  products.value = await productStore.fetchProducts()
})

const editProduct = (product) => {
  selectedProduct.value = { ...product }
  showForm.value = true
}

const deleteProduct = async (id) => {
  if (confirm('Hapus produk ini?')) {
    await productStore.deleteProduct(id)
    products.value = products.value.filter(p => p.id !== id)
  }
}

const handleSave = async (productData) => {
  if (productData.id) {
    // Update produk
    const updated = await productStore.updateProduct(productData)
    const index = products.value.findIndex(p => p.id === productData.id)
    products.value.splice(index, 1, updated)
  } else {
    // Tambah produk baru
    const newProduct = await productStore.addProduct(productData)
    products.value.push(newProduct)
  }
  showForm.value = false
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID', { 
    style: 'currency', 
    currency: 'IDR' 
  }).format(price)
}

const openForm = () => {
  selectedProduct.value = null
  showForm.value = true
}
</script>



