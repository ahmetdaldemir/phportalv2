<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Dashboard</h4>
    
    <div class="row">
      <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card card-vue">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <h5 class="card-title">Toplam Sevk</h5>
                <h3 class="text-primary">{{ stats.totalTransfers }}</h3>
              </div>
              <div class="avatar avatar-md bg-primary rounded">
                <i class="bx bx-transfer fs-4 text-white"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card card-vue">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <h5 class="card-title">Bekleyen Sevk</h5>
                <h3 class="text-warning">{{ stats.pendingTransfers }}</h3>
              </div>
              <div class="avatar avatar-md bg-warning rounded">
                <i class="bx bx-time fs-4 text-white"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card card-vue">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <h5 class="card-title">Toplam Satış</h5>
                <h3 class="text-success">{{ stats.totalSales }}</h3>
              </div>
              <div class="avatar avatar-md bg-success rounded">
                <i class="bx bx-shopping-bag fs-4 text-white"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card card-vue">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <h5 class="card-title">Stok Kartları</h5>
                <h3 class="text-info">{{ stats.totalStockCards }}</h3>
              </div>
              <div class="avatar avatar-md bg-info rounded">
                <i class="bx bx-package fs-4 text-white"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-8 col-12 mb-4">
        <div class="card card-vue">
          <div class="card-header">
            <h5 class="card-title">Son Sevkler</h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-vue">
                <thead>
                  <tr>
                    <th>Numara</th>
                    <th>Tip</th>
                    <th>Durum</th>
                    <th>Tarih</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="transfer in recentTransfers" :key="transfer.id">
                    <td>
                      <router-link :to="`/transfers/${transfer.id}`">
                        {{ transfer.number }}
                      </router-link>
                    </td>
                    <td>{{ transfer.type === 'phone' ? 'TELEFON' : 'DİĞER' }}</td>
                    <td>
                      <span :class="`badge badge-vue status-${getStatusClass(transfer.is_status)}`">
                        {{ TRANSFER_STATUS[transfer.is_status] }}
                      </span>
                    </td>
                    <td>{{ formatDate(transfer.created_at) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-lg-4 col-12 mb-4">
        <div class="card card-vue">
          <div class="card-header">
            <h5 class="card-title">Hızlı İşlemler</h5>
          </div>
          <div class="card-body">
            <div class="d-grid gap-2">
              <router-link to="/transfers/create" class="btn btn-vue">
                <i class="bx bx-plus me-2"></i>
                Yeni Sevk Oluştur
              </router-link>
              <router-link to="/stockcards" class="btn btn-outline-primary">
                <i class="bx bx-package me-2"></i>
                Stok Kartları
              </router-link>
              <router-link to="/sales" class="btn btn-outline-success">
                <i class="bx bx-shopping-bag me-2"></i>
                Satış İşlemleri
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/utils/api'

const router = useRouter()

const stats = ref({
  totalTransfers: 0,
  pendingTransfers: 0,
  totalSales: 0,
  totalStockCards: 0
})

const recentTransfers = ref([])

const TRANSFER_STATUS = {
  0: 'Bekliyor',
  1: 'Onaylandı',
  2: 'Reddedildi',
  3: 'Tamamlandı'
}

const getStatusClass = (status: number) => {
  switch (status) {
    case 0: return 'warning'
    case 1: return 'success'
    case 2: return 'danger'
    case 3: return 'info'
    default: return 'secondary'
  }
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('tr-TR')
}

const loadDashboardData = async () => {
  try {
    // Load stats
    const statsResponse = await api.get('/dashboard/stats')
    stats.value = statsResponse.data

    // Load recent transfers
    const transfersResponse = await api.get('/dashboard/recent-transfers')
    recentTransfers.value = transfersResponse.data
  } catch (error) {
    console.error('Error loading dashboard data:', error)
  }
}

onMounted(() => {
  loadDashboardData()
})
</script>
