import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Layouts
import AdminLayout from '@/layouts/AdminLayout.vue'

// Views
import Login from '@/views/Login.vue'
import Dashboard from '@/views/Dashboard.vue'
import TransferList from '@/views/TransferList.vue'
import TransferForm from '@/views/TransferForm.vue'
import StockCardList from '@/views/StockCardList.vue'
import SaleList from '@/views/SaleList.vue'

const routes: RouteRecordRaw[] = [
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: { title: 'Giriş', requiresGuest: true }
  },
  {
    path: '/',
    component: AdminLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'dashboard',
        component: Dashboard,
        meta: { title: 'Anasayfa' }
      },
      {
        path: '/transfers',
        name: 'transfers',
        component: TransferList,
        meta: { title: 'Sevk Listesi' }
      },
      {
        path: '/transfers/create',
        name: 'transfer.create',
        component: TransferForm,
        meta: { title: 'Yeni Sevk' }
      },
      {
        path: '/transfers/:id/edit',
        name: 'transfer.edit',
        component: TransferForm,
        meta: { title: 'Sevk Düzenle' }
      },
      {
        path: '/stockcards',
        name: 'stockcards',
        component: StockCardList,
        meta: { title: 'Stok Kartları' }
      },
      {
        path: '/sales',
        name: 'sales',
        component: SaleList,
        meta: { title: 'Satışlar' }
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guards
router.beforeEach(async (to, from, next) => {
  // Set page title
  if (to.meta.title) {
    document.title = `${to.meta.title} - PHP Portal`
  }
  
  const authStore = useAuthStore()
  
  // Check if route requires authentication
  if (to.meta.requiresAuth) {
    if (!authStore.isLoggedIn) {
      // Try to check auth from stored token
      const isAuthenticated = await authStore.checkAuth()
      if (!isAuthenticated) {
        next({ name: 'login' })
        return
      }
    }
  }
  
  // Check if route requires guest (not authenticated)
  if (to.meta.requiresGuest) {
    if (authStore.isLoggedIn) {
      next({ name: 'dashboard' })
      return
    }
  }
  
  next()
})

export default router
