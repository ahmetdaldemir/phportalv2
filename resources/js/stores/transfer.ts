import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { Transfer, TransferForm, TransferFilter } from '@/types/transfer'

export const useTransferStore = defineStore('transfer', () => {
  // State
  const transfers = ref<Transfer[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  // Getters
  const isLoading = computed(() => loading.value)
  const hasError = computed(() => error.value !== null)
  const errorMessage = computed(() => error.value)

  // Actions
  const fetchTransfers = async (filters?: TransferFilter) => {
    loading.value = true
    error.value = null
    
    try {
      // API call will be implemented
      
      // Mock data for now
      transfers.value = [
        {
          id: 1,
          number: 'TRF-001',
          type: 'phone',
          main_seller_id: 1,
          delivery_seller_id: 2,
          user_id: 1,
          is_status: 1,
          created_at: '2024-01-15T10:00:00Z',
          updated_at: '2024-01-15T10:00:00Z',
          user: { id: 1, name: 'John Doe', email: 'john@example.com', company_id: 1, seller_id: 1 },
          mainSeller: { id: 1, name: 'Ana Bayi', company_id: 1, is_status: 1 },
          deliverySeller: { id: 2, name: 'Alt Bayi', company_id: 1, is_status: 1 }
        },
        {
          id: 2,
          number: 'TRF-002',
          type: 'other',
          main_seller_id: 2,
          delivery_seller_id: 1,
          user_id: 2,
          is_status: 3,
          comfirm_id: 1,
          comfirm_date: '2024-01-16T14:30:00Z',
          created_at: '2024-01-16T09:00:00Z',
          updated_at: '2024-01-16T14:30:00Z',
          user: { id: 2, name: 'Jane Smith', email: 'jane@example.com', company_id: 1, seller_id: 2 },
          mainSeller: { id: 2, name: 'Alt Bayi', company_id: 1, is_status: 1 },
          deliverySeller: { id: 1, name: 'Ana Bayi', company_id: 1, is_status: 1 },
          confirmUser: { id: 1, name: 'John Doe', email: 'john@example.com', company_id: 1, seller_id: 1 }
        }
      ]
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Transfer listesi yüklenirken hata oluştu'
      console.error('Fetch transfers error:', err)
    } finally {
      loading.value = false
    }
  }

  const createTransfer = async (transferData: TransferForm) => {
    loading.value = true
    error.value = null
    
    try {
      // API call will be implemented
      
      // Mock creation
      const newTransfer: Transfer = {
        id: Date.now(),
        number: `TRF-${Date.now()}`,
        type: transferData.type,
        main_seller_id: transferData.main_seller_id,
        delivery_seller_id: transferData.delivery_seller_id,
        user_id: 1, // Current user ID
        is_status: 1,
        serial_list: transferData.serial_list,
        description: transferData.description,
        created_at: new Date().toISOString(),
        updated_at: new Date().toISOString()
      }
      
      transfers.value.unshift(newTransfer)
      return newTransfer
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Transfer oluşturulurken hata oluştu'
      console.error('Create transfer error:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateTransfer = async (id: number, transferData: TransferForm) => {
    loading.value = true
    error.value = null
    
    try {
      // API call will be implemented
      
      const index = transfers.value.findIndex(t => t.id === id)
      if (index !== -1) {
        transfers.value[index] = {
          ...transfers.value[index],
          ...transferData,
          updated_at: new Date().toISOString()
        }
      }
      
      return transfers.value[index]
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Transfer güncellenirken hata oluştu'
      console.error('Update transfer error:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateTransferStatus = async (id: number, status: number) => {
    loading.value = true
    error.value = null
    
    try {
      // API call will be implemented
      
      const index = transfers.value.findIndex(t => t.id === id)
      if (index !== -1) {
        transfers.value[index].is_status = status
        transfers.value[index].updated_at = new Date().toISOString()
        
        if (status === 3) {
          transfers.value[index].comfirm_date = new Date().toISOString()
          transfers.value[index].comfirm_id = 1 // Current user ID
        }
      }
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Transfer durumu güncellenirken hata oluştu'
      console.error('Update transfer status error:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteTransfer = async (id: number) => {
    loading.value = true
    error.value = null
    
    try {
      // API call will be implemented
      
      const index = transfers.value.findIndex(t => t.id === id)
      if (index !== -1) {
        transfers.value.splice(index, 1)
      }
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Transfer silinirken hata oluştu'
      console.error('Delete transfer error:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const getTransferById = (id: number) => {
    return transfers.value.find(t => t.id === id)
  }

  const clearError = () => {
    error.value = null
  }

  return {
    // State
    transfers,
    loading,
    error,
    
    // Getters
    isLoading,
    hasError,
    errorMessage,
    
    // Actions
    fetchTransfers,
    createTransfer,
    updateTransfer,
    updateTransferStatus,
    deleteTransfer,
    getTransferById,
    clearError
  }
})
