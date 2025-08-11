import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

export const useProductsStore = defineStore('products', () => {
    const products = ref([])
    const latestProducts = ref([])
    const stats = ref({})
    const loading = ref(false)
    const currentPage = ref(1)
    const totalPages = ref(1)
    const totalProducts = ref(0)
    const perPage = ref(10)
    const from = ref(0)
    const to = ref(0)

    const getProducts = async (page = 1, itemsPerPage = null) => {
        loading.value = true
        try {
            const pageSize = itemsPerPage || perPage.value
            // Add timestamp to prevent caching
            const response = await api.get(`/products?page=${page}&per_page=${pageSize}&_t=${Date.now()}`)
            products.value = response.data.data
            currentPage.value = response.data.meta.current_page
            totalPages.value = response.data.meta.last_page
            totalProducts.value = response.data.meta.total
            perPage.value = response.data.meta.per_page
            from.value = response.data.meta.from
            to.value = response.data.meta.to
        } catch (error) {
            console.error('Error loading products:', error)
        } finally {
            loading.value = false
        }
    }

    const getLatestProducts = async () => {
        try {
            const response = await api.get('/products/latest')
            latestProducts.value = response.data.data
        } catch (error) {
            console.error('Error loading latest products:', error)
        }
    }

    const getStats = async () => {
        try {
            const response = await api.get('/products/stats')
            stats.value = response.data.data
        } catch (error) {
            console.error('Error loading stats:', error)
        }
    }

    const getProductById = async (id) => {
        try {
            const response = await api.get(`/products/${id}`)
            return { success: true, data: response.data.data }
        } catch (error) {
            return {
                success: false,
                error: error.response?.data?.message || 'Error loading product'
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

            const response = await api.post('/products', formData, {
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
            // For update, send as JSON instead of FormData to avoid Laravel issues with PUT + FormData
            const response = await api.put(`/products/${id}`, productData, {
                headers: {
                    'Content-Type': 'application/json'
                }
            })

            // Force refresh all data to ensure UI is updated
            await Promise.all([
                getProducts(currentPage.value),
                getLatestProducts(),
                getStats()
            ])

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
        try {
            await api.delete(`/products/${id}`)
            // Update the list of products
            await getProducts(currentPage.value)
            return { success: true }
        } catch (error) {
            return {
                success: false,
                error: error.response?.data?.message || 'Error deleting product'
            }
        }
    }

    const setPerPage = (newPerPage) => {
        perPage.value = newPerPage
    }

    return {
        products,
        latestProducts,
        stats,
        loading,
        currentPage,
        totalPages,
        totalProducts,
        perPage,
        from,
        to,
        getProducts,
        getLatestProducts,
        getStats,
        getProductById,
        createProduct,
        updateProduct,
        deleteProduct,
        setPerPage
    }
})
