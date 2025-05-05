<script setup>
import { ShoppingBagIcon, UserIcon } from '@heroicons/vue/24/outline'

defineProps({
  order: {
    type: Object,
    required: true,
  },
})

const formatPrice = (price) => {
  const num = Number(price)
  if (isNaN(num) || typeof price === 'undefined' || price === null) {
    return 'Rp0'
  }
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(num)
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
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
</script>

<template>
  <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800">Detail Pesanan #{{ order.id }}</h2>
        <div class="text-sm text-gray-500">
          {{ formatDate(order.order_date) }}
        </div>
      </div>
      <div class="mt-1 flex items-center text-sm text-gray-500">
        <UserIcon class="h-4 w-4 mr-1" />
        <span>{{ order.customer_name }}</span>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Produk
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Qty
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Harga
            </th>
            <th
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Subtotal
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="item in order.items" :key="item.id" class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div
                  class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-md flex items-center justify-center"
                >
                  <ShoppingBagIcon class="h-6 w-6 text-blue-600" />
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">
                    {{ item.product?.name || 'Produk dihapus' }}
                  </div>
                  <div v-if="item.product" class="text-sm text-gray-500">
                    SKU: {{ item.product.sku || 'N/A' }}
                  </div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ item.quantity }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatPrice(item.price) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
              {{ formatPrice(item.price * item.quantity) }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
      <div class="flex justify-between">
        <div class="text-sm text-gray-500">
          Status:
          <span
            :class="{
              'text-yellow-600': order.status === 'pending',
              'text-green-600': order.status === 'completed',
              'text-red-600': order.status === 'cancelled',
            }"
            class="font-medium"
          >
            {{ getStatusText(order.status) }}
          </span>
        </div>
        <div class="text-right">
          <div class="text-sm text-gray-500 mb-1">
            Total Items: {{ order.items.reduce((sum, item) => sum + item.quantity, 0) }}
          </div>
          <div class="text-lg font-semibold text-gray-800">
            Total:
            {{
              formatPrice(order.items.reduce((sum, item) => sum + item.price * item.quantity, 0))
            }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
