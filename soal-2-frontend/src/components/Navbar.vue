<script setup>
import { ref } from 'vue'

import { Bars3Icon, XMarkIcon } from '@heroicons/vue/24/outline'

const isOpen = ref(false)

const links = [
  { to: '/', text: 'Produk' },
  { to: '/orders', text: 'Pesanan' },
  { to: '/create-order', text: 'Buat Pesanan' },
]
</script>

<template>
  <nav class="bg-white shadow-sm border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex-shrink-0 flex items-center">
          <router-link
            to="/"
            class="text-xl font-bold bg-gradient-to-r from-blue-600 to-blue-500 bg-clip-text text-transparent"
          >
            MyStoreID
          </router-link>
        </div>

        <div class="hidden sm:ml-6 sm:flex sm:items-center sm:space-x-4">
          <router-link
            v-for="link in links"
            :key="link.to"
            :to="link.to"
            class="px-3 py-2 rounded-md text-sm font-medium transition-colors"
            :class="{
              'bg-blue-100 text-blue-700': $route.path === link.to,
              'text-gray-500 hover:text-gray-700 hover:bg-gray-50': $route.path !== link.to,
            }"
          >
            {{ link.text }}
          </router-link>
        </div>

        <div class="-mr-2 flex items-center sm:hidden">
          <button
            @click="isOpen = !isOpen"
            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none"
          >
            <span class="sr-only">Open main menu</span>
            <Bars3Icon v-if="!isOpen" class="h-6 w-6" />
            <XMarkIcon v-else class="h-6 w-6" />
          </button>
        </div>
      </div>
    </div>

    <transition
      enter-active-class="transition ease-out duration-100"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <div v-show="isOpen" class="sm:hidden absolute w-full z-10 bg-white shadow-md">
        <div class="pt-2 pb-3 space-y-1">
          <router-link
            v-for="link in links"
            :key="link.to"
            :to="link.to"
            @click="isOpen = false"
            class="block px-3 py-2 text-base font-medium"
            :class="{
              'bg-blue-50 text-blue-700': $route.path === link.to,
              'text-gray-500 hover:text-gray-700 hover:bg-gray-50': $route.path !== link.to,
            }"
          >
            {{ link.text }}
          </router-link>
        </div>
      </div>
    </transition>
  </nav>
</template>
