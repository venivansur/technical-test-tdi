<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Daftar Pesanan</h1>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left">Order ID</th>
            <th class="px-6 py-3 text-left">Customer</th>
            <th class="px-6 py-3 text-left">Tanggal</th>
            <th class="px-6 py-3 text-left">Total Items</th>
            <th class="px-6 py-3 text-left">Total Harga</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr
            v-for="order in formattedOrders"
            :key="order.id"
            @click="selectOrder(order.id)"
            class="hover:bg-gray-50 cursor-pointer transition-colors"
            :class="{ 'bg-blue-50': selectedOrder?.id === order.id }"
          >
            <td class="px-6 py-4">{{ order.id }}</td>
            <td class="px-6 py-4">{{ order.customer_name }}</td>
            <td class="px-6 py-4">{{ formatDate(order.order_date) }}</td>
            <td class="px-6 py-4">{{ order.total_items }}</td>
            <td class="px-6 py-4">{{ formatPrice(order.total_price) }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <OrderDetail v-if="selectedOrder" :order="selectedOrder" class="mt-8 animate-fade-in" />
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useOrderStore } from '@/stores/orderStore'
import OrderDetail from '@/components/OrderDetail.vue'
import { storeToRefs } from 'pinia'
const orderStore = useOrderStore()
const selectedOrder = ref(null)
const selectOrder = async (orderId) => {
  await orderStore.selectOrder(orderId)
  selectedOrder.value = orderStore.selectedOrder
}

onMounted(async () => {
  await orderStore.fetchOrders()
})

const formattedOrders = computed(() => orderStore.formattedOrders)

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
  }).format(price)
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('id-ID')
}
</script>
