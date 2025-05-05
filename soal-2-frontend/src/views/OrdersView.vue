<script setup>
import { computed, ref, onMounted } from 'vue'
import { useOrderStore } from '@/stores/orderStore'
import OrderDetail from '@/components/OrderDetail.vue'
import { ShoppingBagIcon, ArrowPathIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline'

const orderStore = useOrderStore()
const selectedOrder = ref(null)
const searchQuery = ref('')
const dateFilter = ref('all')
const statusFilter = ref('all')

const orderStats = computed(() => ({
  total: orderStore.orders.length,
  today: orderStore.orders.filter((o) => isToday(new Date(o.order_date))).length,
  thisMonth: orderStore.orders.filter((o) => isThisMonth(new Date(o.order_date))).length,
  totalRevenue: orderStore.orders.reduce((sum, o) => sum + (o.total_price || 0), 0),
}))

const selectOrder = async (orderId) => {
  await orderStore.selectOrder(orderId)
  selectedOrder.value = orderStore.selectedOrder
}

onMounted(async () => {
  await orderStore.fetchOrders()
})
const filteredOrders = computed(() => {
  return orderStore.formattedOrders.filter((order) => {
    const matchesSearch =
      searchQuery.value === '' ||
      order.customer_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      order.id.toString().includes(searchQuery.value)

    const orderDate = new Date(order.order_date)
    let matchesDate = true
    if (dateFilter.value === 'today') {
      matchesDate = isToday(orderDate)
    } else if (dateFilter.value === 'week') {
      matchesDate = isThisWeek(orderDate)
    } else if (dateFilter.value === 'month') {
      matchesDate = isThisMonth(orderDate)
    }

    let matchesStatus = statusFilter.value === 'all' || order.status === statusFilter.value

    return matchesSearch && matchesDate && matchesStatus
  })
})

const refreshOrders = async () => {
  try {
    await orderStore.fetchOrders()
    selectedOrder.value = null
  } catch (error) {
    console.error('Error refreshing orders:', error)
  }
}

const resetFilters = () => {
  searchQuery.value = ''
  dateFilter.value = 'all'
  statusFilter.value = 'all'
}

const formatPrice = (price) => {
  try {
    const num = Number(price)
    if (isNaN(num)) return 'Rp0'

    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
    }).format(num)
  } catch {
    return 'Rp0'
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  })
}

const formatTime = (dateString) => {
  return new Date(dateString).toLocaleTimeString('id-ID', {
    hour: '2-digit',
    minute: '2-digit',
  })
}

const getStatusText = (status) => {
  const statusMap = {
    pending: 'Pending',
    completed: 'Selesai',
    cancelled: 'Dibatalkan',
  }
  return statusMap[status] || status
}

const isToday = (date) => {
  const today = new Date()
  return (
    date.getDate() === today.getDate() &&
    date.getMonth() === today.getMonth() &&
    date.getFullYear() === today.getFullYear()
  )
}

const isThisWeek = (date) => {
  const today = new Date()
  const firstDayOfWeek = new Date(today.setDate(today.getDate() - today.getDay()))
  const lastDayOfWeek = new Date(firstDayOfWeek)
  lastDayOfWeek.setDate(lastDayOfWeek.getDate() + 6)
  return date >= firstDayOfWeek && date <= lastDayOfWeek
}

const isThisMonth = (date) => {
  const today = new Date()
  return date.getMonth() === today.getMonth() && date.getFullYear() === today.getFullYear()
}

onMounted(async () => {
  await refreshOrders()
})
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Daftar Pesanan</h1>
        <p class="text-gray-500 mt-1">Kelola semua pesanan pelanggan</p>
      </div>
      <div class="flex gap-3">
        <button
          @click="refreshOrders"
          class="bg-white text-gray-700 hover:bg-gray-50 border border-gray-300 px-4 py-2 rounded-lg flex items-center gap-2 transition-colors"
        >
          <ArrowPathIcon class="w-5 h-5" />
          <span>Refresh</span>
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <p class="text-sm text-gray-500">Total Pesanan</p>
        <p class="text-2xl font-bold text-gray-800">{{ orderStats.total }}</p>
      </div>
      <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <p class="text-sm text-gray-500">Hari Ini</p>
        <p class="text-2xl font-bold text-blue-600">{{ orderStats.today }}</p>
      </div>
      <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <p class="text-sm text-gray-500">Bulan Ini</p>
        <p class="text-2xl font-bold text-green-600">{{ orderStats.thisMonth }}</p>
      </div>
      <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <p class="text-sm text-gray-500">Total Pendapatan</p>
        <p class="text-2xl font-bold text-purple-600">{{ formatPrice(orderStats.totalRevenue) }}</p>
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
            placeholder="Cari pesanan..."
          />
        </div>
        <div class="flex gap-2">
          <select
            v-model="dateFilter"
            class="border border-gray-300 rounded-lg bg-gray-50 px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="all">Semua Tanggal</option>
            <option value="today">Hari Ini</option>
            <option value="week">Minggu Ini</option>
            <option value="month">Bulan Ini</option>
          </select>
        </div>
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
                ID Pesanan
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Pelanggan
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Tanggal
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Items
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Total
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="order in filteredOrders"
              :key="order.id"
              @click="selectOrder(order.id)"
              class="hover:bg-gray-50 cursor-pointer transition-colors"
              :class="{ 'bg-blue-50': selectedOrder?.id === order.id }"
            >
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-blue-600">#{{ order.id }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ order.customer_name }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ formatDate(order.order_date) }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ order.total_items }} item(s)
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ formatPrice(order.total_price) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap"></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="filteredOrders.length === 0" class="text-center py-16 bg-gray-50 rounded-b-xl">
        <ShoppingBagIcon class="mx-auto h-16 w-16 text-gray-300" />
        <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak ada pesanan ditemukan</h3>
        <p class="mt-1 text-gray-500">
          {{
            searchQuery || dateFilter !== 'all' || statusFilter !== 'all'
              ? 'Coba sesuaikan pencarian atau filter Anda'
              : 'Belum ada pesanan yang tercatat'
          }}
        </p>
        <div class="mt-6">
          <button
            @click="resetFilters"
            v-if="searchQuery || dateFilter !== 'all' || statusFilter !== 'all'"
            class="text-blue-600 hover:text-blue-800 text-sm font-medium"
          >
            Reset filter
          </button>
        </div>
      </div>
    </div>

    <transition
      enter-active-class="transition ease-out duration-300"
      enter-from-class="transform opacity-0 -translate-y-4"
      enter-to-class="transform opacity-100 translate-y-0"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="transform opacity-100 translate-y-0"
      leave-to-class="transform opacity-0 -translate-y-4"
    >
      <OrderDetail
        v-if="selectedOrder"
        :order="selectedOrder"
        class="mt-8"
        @close="selectedOrder = null"
        @status-updated="refreshOrders"
      />
    </transition>
  </div>
</template>
