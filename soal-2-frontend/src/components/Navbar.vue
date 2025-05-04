<template>
  <nav class="bg-white shadow-sm">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex-shrink-0 flex items-center">
          <router-link to="/" class="text-gray-900 font-bold text-lg"> MyStore </router-link>
        </div>

        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
          <router-link
            v-for="link in links"
            :key="link.to"
            :to="link.to"
            class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
            active-class="border-blue-500 text-gray-900"
          >
            {{ link.text }}
          </router-link>
        </div>

        <div class="-mr-2 flex items-center sm:hidden">
          <button
            @click="isOpen = !isOpen"
            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none"
          >
            <svg
              class="h-6 w-6"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
              />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <div v-show="isOpen" class="sm:hidden">
      <div class="pt-2 pb-3 space-y-1">
        <router-link
          v-for="link in links"
          :key="link.to"
          :to="link.to"
          @click="isOpen = false"
          class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
          active-class="bg-blue-50 border-blue-500 text-blue-700"
        >
          {{ link.text }}
        </router-link>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref } from 'vue'

const isOpen = ref(false)

const links = [
  { to: '/', text: 'Produk' },
  { to: '/orders', text: 'Pesanan' },
  { to: '/create-order', text: 'Buat Pesanan' },
]
</script>
