<template>
    <div class="bg-white p-6 rounded-lg shadow">
      <h2 class="text-xl font-semibold mb-4">
        Detail Pesanan #{{ order.id }}
      </h2>
      
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left">Produk</th>
            <th class="px-6 py-3 text-left">Qty</th>
            <th class="px-6 py-3 text-left">Harga</th>
            <th class="px-6 py-3 text-left">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <tr 
            v-for="item in order.items" 
            :key="item.id"
            class="hover:bg-gray-50"
          >
            <td class="px-6 py-4">{{ item.product?.name || 'Produk dihapus' }}</td>
            <td class="px-6 py-4">{{ item.quantity }}</td>
            <td class="px-6 py-4">{{ formatPrice(item.price) }}</td>
            <td class="px-6 py-4 font-medium">
              {{ formatPrice(item.price * item.quantity) }}
            </td>
          </tr>
        </tbody>
      </table>
  
      <div class="mt-4 flex justify-end">
        <div class="text-right">
          <p class="text-lg">
            <span class="font-semibold">Total:</span> 
            {{ formatPrice(order.total_price) }}
          </p>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  defineProps({
    order: {
      type: Object,
      required: true
    }
  })
  
  const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR'
    }).format(price)
  }
  </script>
  
  <style scoped>
  .animate-fade-in {
    animation: fadeIn 0.3s ease-out;
  }
  
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }
  </style>