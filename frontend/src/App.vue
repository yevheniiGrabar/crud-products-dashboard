<script setup>
import { onMounted } from 'vue'
import Navigation from '@/components/Navigation.vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

onMounted(async () => {
  if (authStore.isAuthenticated) {
    await authStore.fetchUser()
  }
})
</script>

<template>
  <div id="app">
    <Navigation />
    <router-view />
  </div>
</template>

<style scoped></style>
