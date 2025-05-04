<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Buat Pesanan Baru</h1>

    <LoadingSpinner v-if="loading" />

    <form @submit.prevent="handleSubmit" class="space-y-6">
      <div>
        <label class="block mb-2">Nama Customer</label>
        <input
          v-model="form.customer_name"
          type="text"
          required
          class="w-full p-2 border rounded"
        />
      </div>

      <div>
        <label class="block mb-2">Tanggal Pesanan</label>
        <input v-model="form.order_date" type="date" required class="w-full p-2 border rounded" />
      </div>

      <div class="border p-4 rounded-lg">
        <h3 class="font-medium mb-4">Tambah Item Pesanan</h3>

        <div v-if="productsLoading" class="text-center py-4">
          <LoadingSpinner />
          <p>Memuat daftar produk...</p>
        </div>

        <div v-else>
          <div class="flex gap-4">
            <div class="flex-1">
              <label class="block mb-2">Produk</label>
              <select
                v-model="newItem.product_id"
                class="w-full p-2 border rounded"
                @change="updateAvailableStock"
                :disabled="availableProducts.length === 0"
              >
                <option value="" disabled selected>Pilih Produk</option>
                <option v-for="product in availableProducts" :key="product.id" :value="product.id">
                  {{ product.name }} (Stok: {{ product.stock }})
                </option>
              </select>

              <div v-if="availableProducts.length === 0" class="mt-2 text-red-500">
                <p v-if="productStore.products.length === 0">Belum ada produk terdaftar</p>
                <p v-else>Tidak ada produk dengan stok tersedia</p>
              </div>
            </div>
          </div>
          <div>
            <label class="block mb-2">Jumlah</label>
            <input
              v-model.number="newItem.quantity"
              type="number"
              min="1"
              :max="selectedProductStock"
              class="w-full p-2 border rounded"
            />
            <small v-if="newItem.quantity > selectedProductStock" class="text-red-500">
              Jumlah melebihi stok tersedia
            </small>
          </div>

          <div class="self-end">
            <button
              type="button"
              @click="addItem"
              :disabled="!canAddItem"
              class="bg-blue-500 text-white px-4 py-2 rounded disabled:opacity-50"
            >
              Tambah Item
            </button>
          </div>
        </div>
      </div>

      <div v-if="form.items.length > 0" class="space-y-4">
        <h3 class="font-medium">Item Pesanan</h3>
        <table class="w-full border-collapse">
          <thead>
            <tr class="bg-gray-100">
              <th class="p-2 text-left">Produk</th>
              <th class="p-2 text-left">Harga</th>
              <th class="p-2 text-left">Jumlah</th>
              <th class="p-2 text-left">Subtotal</th>
              <th class="p-2 text-left">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in form.items" :key="index" class="border-b">
              <td class="p-2">{{ getProductName(item.product_id) }}</td>
              <td class="p-2">{{ formatPrice(getProductPrice(item.product_id)) }}</td>
              <td class="p-2">{{ item.quantity }}</td>
              <td class="p-2">
                {{ formatPrice(getProductPrice(item.product_id) * item.quantity) }}
              </td>
              <td class="p-2">
                <button type="button" @click="removeItem(index)" class="text-red-500">Hapus</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex justify-between items-center">
        <div class="text-lg font-medium">Total: {{ formatPrice(totalOrder) }}</div>
        <button
          type="submit"
          :disabled="form.items.length === 0"
          class="bg-green-500 text-white px-6 py-2 rounded disabled:opacity-50"
        >
          Buat Pesanan
        </button>
      </div>
    </form>

    <Notification
      v-if="notification.show"
      :type="notification.type"
      :message="notification.message"
      @close="notification.show = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useOrderStore } from '@/stores/orderStore'
import { useProductStore } from '@/stores/productStore'
import LoadingSpinner from '@/components/LoadingSpinner.vue'
import Notification from '@/components/Notification.vue'

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
    return !isNaN(stock) && stock > 0
  })
})

const canAddItem = computed(
  () =>
    newItem.value.product_id &&
    newItem.value.quantity > 0 &&
    newItem.value.quantity <= selectedProductStock.value,
)
const totalOrder = computed(() =>
  form.value.items.reduce((total, item) => {
    return total + getProductPrice(item.product_id) * item.quantity
  }, 0),
)

const updateAvailableStock = () => {
  const product = productStore.products?.find((p) => p?.id === newItem.value.product_id)
  selectedProductStock.value = product?.stock || 0
}
const addItem = () => {
  if (!canAddItem.value) return

  form.value.items.push({
    product_id: newItem.value.product_id,
    quantity: newItem.value.quantity,
    price: getProductPrice(newItem.value.product_id),
  })

  newItem.value = { product_id: null, quantity: 1 }
}

const removeItem = (index) => {
  form.value.items.splice(index, 1)
}

const getProductName = (productId) => {
  return productStore.products.find((p) => p.id === productId)?.name || 'Produk dihapus'
}

const getProductPrice = (productId) => {
  return productStore.products.find((p) => p.id === productId)?.price || 0
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
  }).format(price)
}

const showNotification = (message, type = 'success') => {
  notification.value = { show: true, message, type }
}

const handleSubmit = async () => {
  try {
    loading.value = true

    if (!form.value.customer_name?.trim() || form.value.items.length === 0) {
      showNotification('Nama customer dan item pesanan harus diisi', 'error')
      return
    }

    await orderStore.createOrder({
      customer_name: form.value.customer_name,
      order_date: form.value.order_date,
      items: form.value.items,
      total: totalOrder.value,
    })

    showNotification('Pesanan berhasil dibuat!')

    setTimeout(() => {
      router.push('/orders')
    }, 1500)

    form.value = {
      customer_name: '',
      order_date: new Date().toISOString().split('T')[0],
      items: [],
    }
  } catch (error) {
    showNotification('Gagal membuat pesanan: ' + error.message, 'error')
    console.error('Error:', error)
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  try {
    productsLoading.value = true
    await productStore.fetchProducts()
  } catch (error) {
    console.error('Gagal memuat produk:', error)
    showNotification('Gagal memuat daftar produk', 'error')
  } finally {
    productsLoading.value = false
  }
})
</script>
