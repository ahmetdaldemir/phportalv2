export interface User {
  id: number
  name: string
  email: string
  company_id: number
  seller_id?: number
  company?: {
    id: number
    name: string
    email: string
  }
  seller?: {
    id: number
    name: string
  }
  created_at?: string
  updated_at?: string
}

export interface Company {
  id: number
  name: string
  address?: string
  phone?: string
  email?: string
  tax_number?: string
  tax_office?: string
  country_code?: string
}

export interface Seller {
  id: number
  name: string
  company_id: number
  user_id?: number
  is_status: number
  can_see_stock?: boolean
  can_see_cost_price?: boolean
  can_see_base_cost_price?: boolean
  can_see_sale_price?: boolean
}
