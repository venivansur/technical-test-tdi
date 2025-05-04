<template>
    <div 
      class="fixed bottom-4 right-4 p-4 rounded shadow-lg text-white"
      :class="{
        'bg-green-500': type === 'success',
        'bg-red-500': type === 'error'
      }"
    >
      {{ message }}
      <button @click="$emit('close')" class="ml-4">✕</button>
    </div>
  </template>
  
  <script setup>
  defineProps({
    type: {
      type: String,
      default: 'success'
    },
    message: String
  })
  </script>