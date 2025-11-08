<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Stok /</span> Stok Kartları
    </h4>

    <div class="card card-vue">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Stok Kartları Listesi</h5>
        <router-link to="/stockcards/create" class="btn btn-vue">
          <i class="bx bx-plus me-1"></i>
          Yeni Stok Kartı
        </router-link>
      </div>
      
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-vue">
            <thead>
              <tr>
                <th>Kod</th>
                <th>Ürün Adı</th>
                <th>Kategori</th>
                <th>Marka</th>
                <th>Model</th>
                <th>Renk</th>
                <th>Stok</th>
                <th>Fiyat</th>
                <th>İşlemler</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="stockCard in stockCards" :key="stockCard.id">
                <td>{{ stockCard.code }}</td>
                <td>{{ stockCard.name }}</td>
                <td>{{ stockCard.category?.name }}</td>
                <td>{{ stockCard.brand?.name }}</td>
                <td>{{ stockCard.version?.name }}</td>
                <td>{{ stockCard.color?.name }}</td>
                <td>
                  <span :class="`badge badge-vue ${getStockClass(stockCard.quantity)}`">
                    {{ stockCard.quantity }}
                  </span>
                </td>
                <td>{{ formatPrice(stockCard.price) }}</td>
                <td>
                  <div class="btn-group" role="group">
                    <router-link 
                      :to="`/stockcards/${stockCard.id}`"
                      class="btn btn-sm btn-outline-primary"
                      title="Görüntüle"
                    >
                      <i class="bx bx-show"></i>
                    </router-link>
                    <router-link 
                      :to="`/stockcards/${stockCard.id}/edit`"
                      class="btn btn-sm btn-outline-warning"
                      title="Düzenle"
                    >
                      <i class="bx bx-edit"></i>
                    </router-link>
                    <button 
                      @click="deleteStockCard(stockCard.id)"
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
const stockCards = ref([
  {
    id: 1,
    code: 'STK-001',
    name: 'iPhone 14 Pro',
    category: { id: 1, name: 'Telefon' },
    brand: { id: 1, name: 'Apple' },
    version: { id: 1, name: '14 Pro' },
    color: { id: 1, name: 'Siyah' },
    quantity: 15,
    price: 45000
  },
  {
    id: 2,
    code: 'STK-002',
    name: 'Samsung Galaxy S23',
    category: { id: 1, name: 'Telefon' },
    brand: { id: 2, name: 'Samsung' },
    version: { id: 2, name: 'S23' },
    color: { id: 2, name: 'Beyaz' },
    quantity: 8,
    price: 35000
  },
  {
    id: 3,
    code: 'STK-003',
    name: 'MacBook Pro',
    category: { id: 2, name: 'Bilgisayar' },
    brand: { id: 1, name: 'Apple' },
    version: { id: 3, name: 'M2 Pro' },
    color: { id: 3, name: 'Gümüş' },
    quantity: 3,
    price: 85000
  }
])

// Methods
const getStockClass = (quantity: number) => {
  if (quantity === 0) return 'status-rejected'
  if (quantity < 5) return 'status-pending'
  return 'status-completed'
}

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('tr-TR', {
    style: 'currency',
    currency: 'TRY'
  }).format(price)
}

const deleteStockCard = async (id: number) => {
  if (confirm('Bu stok kartını silmek istediğinizden emin misiniz?')) {
    // API call will be implemented
    stockCards.value = stockCards.value.filter(card => card.id !== id)
  }
}

// Lifecycle
onMounted(() => {
})
</script>
