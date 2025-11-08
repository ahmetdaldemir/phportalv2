import { create } from 'zustand'
import * as SecureStore from 'expo-secure-store'

const TOKEN_KEY = 'phportal_token'

type AuthState = {
  token: string | null
  user: {
    id: number
    name: string
    roles: string[]
  } | null
  isAuthenticated: boolean
  setCredentials: (token: string, user: AuthState['user']) => Promise<void>
  logout: () => Promise<void>
  bootstrap: () => Promise<void>
}

export const useAuthStore = create<AuthState>((set, get) => ({
  token: null,
  user: null,
  isAuthenticated: false,
  setCredentials: async (token, user) => {
    await SecureStore.setItemAsync(TOKEN_KEY, token)
    set({ token, user, isAuthenticated: true })
    if (typeof globalThis !== 'undefined') {
      ;(globalThis as any).currentUserRoles = user?.roles ?? []
    }
  },
  logout: async () => {
    await SecureStore.deleteItemAsync(TOKEN_KEY)
    set({ token: null, user: null, isAuthenticated: false })
    if (typeof globalThis !== 'undefined') {
      ;(globalThis as any).currentUserRoles = []
    }
  },
  bootstrap: async () => {
    const savedToken = await SecureStore.getItemAsync(TOKEN_KEY)
    if (savedToken && !get().token) {
      set({ token: savedToken, isAuthenticated: true })
    }
  }
}))

