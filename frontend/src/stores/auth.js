import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/plugins/axios'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(JSON.parse(localStorage.getItem('user') || 'null'))
  const token = ref(localStorage.getItem('token') || null)

  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.role === 'admin')
  const isClient = computed(() => user.value?.role === 'client')
  const isApproved = computed(() => user.value?.is_active === true)
  const hasContract = computed(() => user.value?.contract_agreed === true)

  async function login(credentials) {
    const response = await api.post('/auth/login', credentials)
    const { token: newToken, user: newUser } = response.data.data
    token.value = newToken
    user.value = newUser
    localStorage.setItem('token', newToken)
    localStorage.setItem('user', JSON.stringify(newUser))
    return response.data
  }

  async function register(data) {
    const response = await api.post('/auth/register', data)
    return response.data
  }

  async function logout() {
    try {
      await api.post('/auth/logout')
    } catch {}
    token.value = null
    user.value = null
    localStorage.removeItem('token')
    localStorage.removeItem('user')
  }

  async function fetchUser() {
    if (!token.value) return
    try {
      const response = await api.get('/auth/me')
      user.value = response.data.data
      localStorage.setItem('user', JSON.stringify(user.value))
    } catch {
      await logout()
    }
  }

  function updateUser(userData) {
    user.value = { ...user.value, ...userData }
    localStorage.setItem('user', JSON.stringify(user.value))
  }

  return {
    user, token,
    isAuthenticated, isAdmin, isClient, isApproved, hasContract,
    login, register, logout, fetchUser, updateUser
  }
})
