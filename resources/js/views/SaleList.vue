<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Satış /</span> Satış Listesi
    </h4>

    <div class="card card-vue">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Satış Listesi</h5>
        <router-link to="/sales/create" class="btn btn-vue">
          <i class="bx bx-plus me-1"></i>
          Yeni Satış
        </router-link>
      </div>
      
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-vue">
            <thead>
              <tr>
                <th>Satış No</th>
                <th>Müşteri</th>
                <th>Ürün</th>
                <th>Miktar</th>
                <th>Birim Fiyat</th>
                <th>Toplam</th>
                <th>Tarih</th>
                <th>Durum</th>
                <th>İşlemler</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="sale in sales" :key="sale.id">
                <td>{{ sale.number }}</td>
                <td>{{ sale.customer?.name }}</td>
                <td>{{ sale.product?.name }}</td>
                <td>{{ sale.quantity }}</td>
                <td>{{ formatPrice(sale.unit_price) }}</td>
                <td>{{ formatPrice(sale.total_price) }}</td>
                <td>{{ formatDate(sale.created_at) }}</td>
                <td>
                  <span :class="`badge badge-vue ${getSaleStatusClass(sale.status)}`">
                    {{ getSaleStatusText(sale.status) }}
                  </span>
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <router-link 
                      :to="`/sales/${sale.id}`"
                      class="btn btn-sm btn-outline-primary"
                      title="Görüntüle"
                    >
                      <i class="bx bx-show"></i>
                    </router-link>
                    <router-link 
                      :to="`/sales/${sale.id}/edit`"
                      class="btn btn-sm btn-outline-warning"
                      title="Düzenle"
                    >
                      <i class="bx bx-edit"></i>
                    </router-link>
                    <button 
                      @click="deleteSale(sale.id)"
                      class="btn btn-sm btn-outline-danger"
                      title="Sil"
                    >
                      <i class="bx bx-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

// Mock data
const sales = ref([
  {
    id: 1,
    number: 'SAT-001',
    customer: { id: 1, name: 'Ahmet Yılmaz' },
    product: { id: 1, name: 'iPhone 14 Pro' },
    quantity: 1,
    unit_price: 45000,
    total_price: 45000,
    status: 'completed',
    created_at: '2024-01-15T10:00:00Z'
  },
  {
    id: 2,
    number: 'SAT-002',
    customer: { id: 2, name: 'Fatma Demir' },
    product: { id: 2, name: 'Samsung Galaxy S23' },
    quantity: 2,
    unit_price: 35000,
    total_price: 70000,
    status: 'pending',
    created_at: '2024-01-16T14:30:00Z'
  },
  {
    id: 3,
    number: 'SAT-003',
    customer: { id: 3, name: 'Mehmet Kaya' },
    product: { id: 3, name: 'MacBook Pro' },
    quantity: 1,
    unit_price: 85000,
    total_price: 85000,
    status: 'cancelled',
    created_at: '2024-01-17T09:15:00Z'
  }
])

// Methods
const getSaleStatusClass = (status: string) => {
  switch (status) {
    case 'completed': return 'status-completed'
    case 'pending': return 'status-pending'
    case 'cancelled': return 'status-rejected'
    default: return 'status-pending'
  }
}

const getSaleStatusText = (status: string) => {
  switch (status) {
    case 'completed': return 'Tamamlandı'
    case 'pending': return 'Beklemede'
    case 'cancelled': return 'İptal Edildi'
    default: return 'Bilinmiyor'
  }
}

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('tr-TR', {
    style: 'currency',
    currency: 'TRY'
  }).format(price)
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('tr-TR')
}

const deleteSale = async (id: number) => {
  if (confirm('Bu satışı silmek istediğinizden emin misiniz?')) {
    // API call will be implemented
    sales.value = sales.value.filter(sale => sale.id !== id)
  }
}

// Lifecycle
onMounted(() => {
})
</script>
