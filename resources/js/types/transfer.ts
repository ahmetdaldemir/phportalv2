export interface Transfer {
  id: number
  number: string
  type: 'phone' | 'other'
  main_seller_id: number
  delivery_seller_id: number
  user_id: number
  comfirm_id?: number
  comfirm_date?: string
  is_status: number
  serial_list?: string[]
  description?: string
  created_at: string
  updated_at: string
  
  // Relations
  user?: User
  mainSeller?: Seller
  deliverySeller?: Seller
  confirmUser?: User
}

export interface TransferForm {
  id?: number
  type: 'phone' | 'other'
  main_seller_id: number
  delivery_seller_id: number
  serial_list: string[]
  description?: string
}

export interface TransferFilter {
  stockName?: string
  brand?: number
  version?: number
  category?: number
  color?: number
  seller?: number | 'all'
  serialNumber?: string
}

export const TRANSFER_STATUS = {
  1: 'Beklemede',
  2: 'Ön Onay',
  3: 'Onaylandı',
  4: 'Reddedildi',
  5: 'Tamamlandı'
} as const

export const TRANSFER_STATUS_COLOR = {
  1: 'warning',
  2: 'info',
  3: 'success',
  4: 'danger',
  5: 'primary'
} as const
