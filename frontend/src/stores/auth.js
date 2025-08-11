import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useAuthStore = defineStore('auth', () => {
    const user = ref(null)
    const token = ref(localStorage.getItem('token') || null)

    const isAuthenticated = computed(() => !!token.value)

    // Setup axios with token
    const setupAxiosAuth = (authToken) => {
        if (authToken) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${authToken}`
        } else {
            delete axios.defaults.headers.common['Authorization']
        }
    }

    // Initialization when loading
    if (token.value) {
        setupAxiosAuth(token.value)
    }

    const login = async (credentials) => {
        try {
            const response = await axios.post('/api/auth/login', credentials)
            const { user: userData, token: authToken } = response.data.data

            user.value = userData
            token.value = authToken
            localStorage.setItem('token', authToken)
            setupAxiosAuth(authToken)

            return { success: true }
        } catch (error) {
            return {
                success: false,
                error: error.response?.data?.message || 'Login error'
            }
        }
    }

    const register = async (userData) => {
        try {
            const response = await axios.post('/api/auth/register', userData)
            const { user: newUser, token: authToken } = response.data.data

            user.value = newUser
            token.value = authToken
            localStorage.setItem('token', authToken)
            setupAxiosAuth(authToken)

            return { success: true }
        } catch (error) {
            return {
                success: false,
                error: error.response?.data?.message || 'Registration error',
                errors: error.response?.data?.errors || {}
            }
        }
    }

    const logout = async () => {
        try {
            if (token.value) {
                await axios.post('/api/auth/logout')
            }
        } catch (error) {
            console.error('Logout error:', error)
        } finally {
            user.value = null
            token.value = null
            localStorage.removeItem('token')
            setupAxiosAuth(null)
        }
    }

    const fetchUser = async () => {
        try {
            const response = await axios.get('/api/auth/user')
            user.value = response.data.data.user
            return { success: true }
        } catch (error) {
            logout()
            return { success: false }
        }
    }

    return {
        user,
        token,
        isAuthenticated,
        login,
        register,
        logout,
        fetchUser
    }
})
