<script setup>
import { RouterLink, RouterView } from 'vue-router'
import Navbar from '@/components/Navbar.vue'
import Notification from '@/components/Notification.vue'
import { ref } from 'vue'

const notification = ref({
  show: false,
  message: '',
  type: 'success',
})
</script>

<template>
  <header>
    <div class="min-h-screen bg-gray-50">
      <Navbar />
      <main class="max-w-6xl mx-auto py-6 sm:px-6 lg:px-8">
        <router-view />
      </main>
      <Notification v-if="notification.show" />
    </div>
  </header>
</template>
