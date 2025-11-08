import React, { createContext, useContext } from 'react'
import axios, { AxiosInstance } from 'axios'
import { useAuthStore } from '@store/useAuthStore'

const API_BASE_URL = process.env.EXPO_PUBLIC_API_URL ?? 'https://your-phportal-domain.test'

const apiClient = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json'
  },
  withCredentials: true
})

type ApiContextValue = AxiosInstance

const ApiContext = createContext<ApiContextValue>(apiClient)

export const ApiProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const token = useAuthStore(state => state.token)

  React.useEffect(() => {
    if (token) {
      apiClient.defaults.headers.common.Authorization = `Bearer ${token}`
    } else {
      delete apiClient.defaults.headers.common.Authorization
    }
  }, [token])

  return <ApiContext.Provider value={apiClient}>{children}</ApiContext.Provider>
}

export const useApi = () => useContext(ApiContext)

