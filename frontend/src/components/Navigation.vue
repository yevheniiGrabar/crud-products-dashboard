<template>
  <nav class="bg-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex">
          <div class="flex-shrink-0 flex items-center">
            <router-link to="/" class="text-xl font-bold text-gray-800">
              CRUD Dashboard
            </router-link>
          </div>
          
          <!-- Навигационные ссылки для десктопа -->
          <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
            <router-link
              v-if="isAuthenticated"
              to="/dashboard"
              class="text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium hover:border-gray-300"
              active-class="border-indigo-500 text-gray-900"
            >
              Dashboard
            </router-link>
            <router-link
              v-if="isAuthenticated"
              to="/products"
              class="text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium hover:border-gray-300"
              active-class="border-indigo-500 text-gray-900"
            >
              Products
            </router-link>
          </div>
        </div>

        <!-- Right part of navigation -->
        <div class="hidden sm:ml-6 sm:flex sm:items-center">
          <div v-if="isAuthenticated" class="flex items-center space-x-4">
            <span class="text-sm text-gray-700">
              Hello, {{ user?.name }}
            </span>
            <button
              @click="handleLogout"
              class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium"
            >
              Logout
            </button>
          </div>
          <div v-else class="flex items-center space-x-4">
            <router-link
              to="/login"
              class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
            >
              Login
            </router-link>
            <router-link
              to="/register"
              class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium"
            >
              Registration
            </router-link>
          </div>
        </div>

        <!-- Mobile menu -->
        <div class="flex items-center sm:hidden">
          <button
            @click="mobileMenuOpen = !mobileMenuOpen"
            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
          >
            <svg
              class="h-6 w-6"
              :class="{ 'hidden': mobileMenuOpen, 'block': !mobileMenuOpen }"
              stroke="currentColor"
              fill="none"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
              />
            </svg>
            <svg
              class="h-6 w-6"
              :class="{ 'block': mobileMenuOpen, 'hidden': !mobileMenuOpen }"
              stroke="currentColor"
              fill="none"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile menu -->
    <div class="sm:hidden" :class="{ 'block': mobileMenuOpen, 'hidden': !mobileMenuOpen }">
      <div class="pt-2 pb-3 space-y-1">
        <router-link
          v-if="isAuthenticated"
          to="/dashboard"
          class="text-gray-700 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium"
          active-class="bg-indigo-50 border-indigo-500 text-indigo-700"
        >
          Dashboard
        </router-link>
        <router-link
          v-if="isAuthenticated"
          to="/products"
          class="text-gray-700 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium"
          active-class="bg-indigo-50 border-indigo-500 text-indigo-700"
        >
          Products
        </router-link>
      </div>
      
      <div class="pt-4 pb-3 border-t border-gray-200">
        <div v-if="isAuthenticated" class="space-y-1">
          <div class="px-3 py-2 text-sm text-gray-700">
            Hello, {{ user?.name }}
          </div>
          <button
            @click="handleLogout"
            class="w-full text-left text-gray-700 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium"
          >
            Logout
          </button>
        </div>
        <div v-else class="space-y-1">
          <router-link
            to="/login"
            class="text-gray-700 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium"
          >
            Login
          </router-link>
          <router-link
            to="/register"
            class="text-gray-700 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium"
          >
            Registration
          </router-link>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const mobileMenuOpen = ref(false)

const isAuthenticated = computed(() => authStore.isAuthenticated)
const user = computed(() => authStore.user)

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>
