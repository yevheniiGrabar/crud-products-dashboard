import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useProductsStore = defineStore('products', () => {
    const products = ref([])
    const latestProducts = ref([])
    const stats = ref({})
    const loading = ref(false)
    const currentPage = ref(1)
    const totalPages = ref(1)
    const totalProducts = ref(0)

    const getProducts = async (page = 1) => {
        loading.value = true
        try {
            const response = await axios.get(`/api/products?page=${page}`)
            products.value = response.data.data
            currentPage.value = response.data.meta.current_page
            totalPages.value = response.data.meta.last_page
            totalProducts.value = response.data.meta.total
            return { success: true }
        } catch (error) {
            return {
                success: false,
                error: error.response?.data?.message || 'Error loading products'
            }
        } finally {
            loading.value = false
        }
    }

    const getLatestProducts = async () => {
        try {
            const response = await axios.get('/api/products/latest')
            latestProducts.value = response.data.data
            return { success: true }
        } catch (error) {
            return {
                success: false,
                error: error.response?.data?.message || 'Error loading latest products'
            }
        }
    }

    const getStats = async () => {
        try {
            const response = await axios.get('/api/products/stats')
            stats.value = response.data.data
            return { success: true }
        } catch (error) {
            return {
                success: false,
                error: error.response?.data?.message || 'Error loading statistics'
            }
        }
    }

    const createProduct = async (productData) => {
        loading.value = true
        try {
            const formData = new FormData()

            // Add all fields to FormData
            Object.keys(productData).forEach(key => {
                if (productData[key] !== null && productData[key] !== undefined) {
                    formData.append(key, productData[key])
                }
            })

            const response = await axios.post('/api/products', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })

            // Update the list of products
            await getProducts(currentPage.value)

            return { success: true, data: response.data.data }
        } catch (error) {
            return {
                success: false,
                error: error.response?.data?.message || 'Error creating product',
                errors: error.response?.data?.errors || {}
            }
        } finally {
            loading.value = false
        }
    }

    const updateProduct = async (id, productData) => {
        loading.value = true
        try {
            const formData = new FormData()

            // Add all fields to FormData
            Object.keys(productData).forEach(key => {
                if (productData[key] !== null && productData[key] !== undefined) {
                    formData.append(key, productData[key])
                }
            })

            const response = await axios.post(`/api/products/${id}`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })

            // Update the list of products
            await getProducts(currentPage.value)

            return { success: true, data: response.data.data }
        } catch (error) {
            return {
                success: false,
                error: error.response?.data?.message || 'Error updating product',
                errors: error.response?.data?.errors || {}
            }
        } finally {
            loading.value = false
        }
    }

    const deleteProduct = async (id) => {
        loading.value = true
        try {
            await axios.delete(`/api/products/${id}`)

            // Update the list of products
            await getProducts(currentPage.value)

            return { success: true }
        } catch (error) {
            return {
                success: false,
                error: error.response?.data?.message || 'Error deleting product'
            }
        } finally {
            loading.value = false
        }
    }

    return {
        products,
        latestProducts,
        stats,
        loading,
        currentPage,
        totalPages,
        totalProducts,
        getProducts,
        getLatestProducts,
        getStats,
        createProduct,
        updateProduct,
        deleteProduct
    }
})
