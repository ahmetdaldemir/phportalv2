import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { User } from '@/types/user'
import api from '@/utils/api'

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref<User | null>(null)
  const isAuthenticated = ref(false)
  const loading = ref(false)

  // Getters
  const currentUser = computed(() => user.value)
  const isLoggedIn = computed(() => isAuthenticated.value)
  const isLoading = computed(() => loading.value)

  // Actions
  const login = async (credentials: { email: string; password: string }) => {
    loading.value = true
    try {
      const response = await api.post('/login', credentials)
      const { user: userData, token, permissions, roles } = response.data
      
      // Store token and user data
      localStorage.setItem('auth_token', token)
      localStorage.setItem('user', JSON.stringify(userData))
      localStorage.setItem('permissions', JSON.stringify(permissions))
      localStorage.setItem('roles', JSON.stringify(roles))
      
      // Update state
      user.value = userData
      isAuthenticated.value = true
      
      return response.data
    } catch (error) {
      console.error('Login error:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  const logout = async () => {
    try {
      await api.post('/logout')
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      // Clear local storage and state
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
      localStorage.removeItem('permissions')
      localStorage.removeItem('roles')
      
      user.value = null
      isAuthenticated.value = false
    }
  }

  const checkAuth = async () => {
    try {
      const token = localStorage.getItem('auth_token')
      if (!token) {
        return false
      }
      
      const response = await api.get('/user')
      user.value = response.data
      isAuthenticated.value = true
      return true
    } catch (error) {
      console.error('Auth check error:', error)
      // Clear invalid data
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
      user.value = null
      isAuthenticated.value = false
      return false
    }
  }

  const initializeAuth = () => {
    const storedUser = localStorage.getItem('user')
    const token = localStorage.getItem('auth_token')
    
    if (storedUser && token) {
      user.value = JSON.parse(storedUser)
      isAuthenticated.value = true
    }
  }

  return {
    // State
    user,
    isAuthenticated,
    loading,
    
    // Getters
    currentUser,
    isLoggedIn,
    isLoading,
    
    // Actions
    login,
    logout,
    checkAuth,
    initializeAuth
  }
})
