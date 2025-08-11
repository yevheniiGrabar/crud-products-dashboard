import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', () => {
    const user = ref(null)
    const token = ref(localStorage.getItem('token') || null)

    const isAuthenticated = computed(() => !!token.value)

    const login = async (credentials) => {
        try {
            const response = await api.post('/auth/login', credentials)
            const { user: userData, token: authToken } = response.data.data

            user.value = userData
            token.value = authToken
            localStorage.setItem('token', authToken)

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
            const response = await api.post('/auth/register', userData)
            const { user: newUser, token: authToken } = response.data.data

            user.value = newUser
            token.value = authToken
            localStorage.setItem('token', authToken)

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
                await api.post('/auth/logout')
            }
        } catch (error) {
            console.error('Logout error:', error)
        } finally {
            user.value = null
            token.value = null
            localStorage.removeItem('token')
        }
    }

    const fetchUser = async () => {
        try {
            const response = await api.get('/auth/user')
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
