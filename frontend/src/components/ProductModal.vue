<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="close"></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <form @submit.prevent="handleSubmit">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                  {{ isEditing ? 'Edit product' : 'Add product' }}
                </h3>
                
                <div class="space-y-4">
                  <!-- Name -->
                  <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input
                      id="name"
                      v-model="form.name"
                      type="text"
                      required
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      :class="{ 'border-red-500': errors.name }"
                    />
                    <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name[0] }}</p>
                  </div>

                  <!-- SKU -->
                  <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
                    <input
                      id="sku"
                      v-model="form.sku"
                      type="text"
                      required
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      :class="{ 'border-red-500': errors.sku }"
                    />
                    <p v-if="errors.sku" class="mt-1 text-sm text-red-600">{{ errors.sku[0] }}</p>
                  </div>

                  <!-- Price -->
                  <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                    <input
                      id="price"
                      v-model="form.price"
                      type="number"
                      step="0.01"
                      min="0"
                      required
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      :class="{ 'border-red-500': errors.price }"
                    />
                    <p v-if="errors.price" class="mt-1 text-sm text-red-600">{{ errors.price[0] }}</p>
                  </div>

                  <!-- Quantity -->
                  <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                    <input
                      id="quantity"
                      v-model="form.quantity"
                      type="number"
                      min="0"
                      required
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      :class="{ 'border-red-500': errors.quantity }"
                    />
                    <p v-if="errors.quantity" class="mt-1 text-sm text-red-600">{{ errors.quantity[0] }}</p>
                  </div>

                  <!-- Image -->
                  <div>
                    <label for="image" class="block text-sm font-medium text-gray-700">Image</label>
                    <input
                      id="image"
                      ref="imageInput"
                      type="file"
                      accept="image/*"
                      @change="handleImageChange"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      :class="{ 'border-red-500': errors.image }"
                    />
                    <p v-if="errors.image" class="mt-1 text-sm text-red-600">{{ errors.image[0] }}</p>
                    
                    <!-- Preview of the image -->
                    <div v-if="imagePreview" class="mt-2">
                      <img
                        :src="imagePreview"
                        alt="Preview"
                        class="h-20 w-20 object-cover rounded-lg"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="submit"
              :disabled="loading"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
            >
              <span v-if="loading" class="mr-2">
                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
              </span>
              {{ loading ? 'Saving...' : (isEditing ? 'Update' : 'Create') }}
            </button>
            <button
              type="button"
              @click="close"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'
import { useProductsStore } from '@/stores/products'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  product: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'saved'])

const productsStore = useProductsStore()

const loading = ref(false)
const errors = reactive({})
const imagePreview = ref(null)

const form = reactive({
  name: '',
  sku: '',
  price: '',
  quantity: '',
  image: null
})

const isEditing = computed(() => !!props.product)

// Fill the form when editing
watch(() => props.product, (product) => {
  if (product) {
    form.name = product.name || ''
    form.sku = product.sku || ''
    form.price = product.price || ''
    form.quantity = product.quantity || ''
    form.image = null
    imagePreview.value = product.image || null
  } else {
    resetForm()
  }
}, { immediate: true })

const resetForm = () => {
  form.name = ''
  form.sku = ''
  form.price = ''
  form.quantity = ''
  form.image = null
  imagePreview.value = null
  Object.keys(errors).forEach(key => errors[key] = null)
}

const handleImageChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    form.image = file
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const handleSubmit = async () => {
  loading.value = true
  Object.keys(errors).forEach(key => errors[key] = null)

  try {
    let result
    if (isEditing.value) {
      result = await productsStore.updateProduct(props.product.id, form)
    } else {
      result = await productsStore.createProduct(form)
    }

    if (result.success) {
      emit('saved')
    } else {
      if (result.errors) {
        Object.assign(errors, result.errors)
      }
    }
  } catch (err) {
    console.error('Error saving product:', err)
  } finally {
    loading.value = false
  }
}

const close = () => {
  resetForm()
  emit('close')
}
</script>
