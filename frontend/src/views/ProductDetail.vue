<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="px-4 py-6 sm:px-0">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Product Details</h1>
            <p class="mt-2 text-sm text-gray-600">
              View and manage product information
            </p>
          </div>
          <router-link
            to="/products"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
          >
            Back to Products
          </router-link>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="flex justify-center py-8">
        <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>

      <!-- Error -->
      <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-md p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800">Error loading product</h3>
            <div class="mt-2 text-sm text-red-700">
              {{ error }}
            </div>
          </div>
        </div>
      </div>

      <!-- Product Details -->
      <div v-else-if="product" class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Product Image -->
            <div>
              <div v-if="product.image" class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg">
                <img :src="product.image" :alt="product.name" class="h-full w-full object-cover object-center">
              </div>
              <div v-else class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg bg-gray-200 flex items-center justify-center">
                <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
            </div>

            <!-- Product Info -->
            <div class="space-y-6">
              <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ product.name }}</h2>
                <p class="mt-1 text-sm text-gray-500">SKU: {{ product.sku }}</p>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <dt class="text-sm font-medium text-gray-500">Price</dt>
                  <dd class="mt-1 text-lg font-semibold text-gray-900">{{ formatPrice(product.price) }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Quantity</dt>
                  <dd class="mt-1 text-lg font-semibold text-gray-900">{{ product.quantity }}</dd>
                </div>
              </div>

              <div>
                <dt class="text-sm font-medium text-gray-500">Created</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ formatDate(product.created_at) }}</dd>
              </div>

              <div>
                <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ formatDate(product.updated_at) }}</dd>
              </div>

              <!-- Actions -->
              <div class="flex space-x-3">
                <button
                  @click="editProduct"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                >
                  Edit Product
                </button>
                <button
                  @click="deleteProduct"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700"
                >
                  Delete Product
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useProductsStore } from '@/stores/products'

const route = useRoute()
const router = useRouter()
const productsStore = useProductsStore()

const product = ref(null)
const loading = ref(true)
const error = ref(null)

const formatPrice = (price) => {
  if (!price) return '$0'
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 0
  }).format(price)
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const loadProduct = async () => {
  try {
    loading.value = true
    error.value = null
    
    const result = await productsStore.getProductById(route.params.id)
    if (result.success) {
      product.value = result.data
    } else {
      error.value = result.error || 'Product not found'
    }
  } catch (err) {
    error.value = 'Error loading product'
  } finally {
    loading.value = false
  }
}

const editProduct = () => {
  router.push(`/products?edit=${product.value.id}`)
}

const deleteProduct = async () => {
  if (confirm('Are you sure you want to delete this product?')) {
    try {
      const result = await productsStore.deleteProduct(product.value.id)
      if (result.success) {
        router.push('/products')
      } else {
        alert('Error deleting product: ' + result.error)
      }
    } catch (err) {
      alert('Error deleting product')
    }
  }
}

onMounted(() => {
  loadProduct()
})
</script>
