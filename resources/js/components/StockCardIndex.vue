<template>
  <div class="stockcard-index">
    <!-- Header -->
    <div class="page-header">
      <h4 class="page-title">
        <span class="text-muted">Stok Kartları /</span> Stok Kart Listesi
      </h4>
      <div class="header-actions">
        <button @click="showCreateModal = true" class="btn btn-primary">
          <i class="bx bx-plus"></i> Yeni Stok Kartı Ekle
        </button>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Kategori</label>
            <select v-model="filters.category_id" @change="loadStockCards" class="form-select">
              <option value="">Tüm Kategoriler</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.path }}
              </option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Marka</label>
            <select v-model="filters.brand_id" @change="loadStockCards" class="form-select">
              <option value="">Tüm Markalar</option>
              <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                {{ brand.name }}
              </option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Şube</label>
            <select v-model="filters.seller_id" @change="loadStockCards" class="form-select">
              <option value="">Tüm Şubeler</option>
              <option v-for="seller in sellers" :key="seller.id" :value="seller.id">
                {{ seller.name }}
              </option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Renk</label>
            <select v-model="filters.color_id" @change="loadStockCards" class="form-select">
              <option value="">Tüm Renkler</option>
              <option v-for="color in colors" :key="color.id" :value="color.id">
                {{ color.name }}
              </option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Stock Cards Table -->
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Stok Adı</th>
                <th>Kategori</th>
                <th>İşlemler</th>
              </tr>
            </thead>
            <tbody>
              <template v-for="stockcard in stockcards" :key="stockcard.id">
                <!-- Main Row -->
                <tr @click="toggleExpanded(stockcard.id)" class="accordion-toggle cursor-pointer">
                  <td>
                    <i :class="expandedItems.includes(stockcard.id) ? 'bx bx-up-arrow' : 'bx bx-down-arrow'"></i>
                    {{ stockcard.stock_name }}
                  </td>
                  <td>{{ stockcard.category_sperator_name }}{{ stockcard.category_name }}</td>
                  <td>
                    <div class="btn-group btn-group-sm">
                      <button @click.stop="editStockCard(stockcard)" class="btn btn-outline-primary">
                        <i class="bx bx-edit"></i>
                      </button>
                      <button @click.stop="showMovements(stockcard)" class="btn btn-outline-info">
                        <i class="bx bx-list-ul"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                
                <!-- Expanded Details -->
                <tr v-if="expandedItems.includes(stockcard.id)">
                  <td colspan="3" class="p-0">
                    <div class="accordion-body">
                      <table class="table table-bordered table-sm mb-0">
                        <thead class="table-light">
                          <tr>
                            <th>Ürün Adı</th>
                            <th>Adet</th>
                            <th>Kategori</th>
                            <th>Marka</th>
                            <th>Model</th>
                            <th>Alış F.</th>
                            <th>Destek. F.</th>
                            <th>Satış F.</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="stockData in stockcard.stockData" :key="stockData.id">
                            <td><strong>{{ stockData.name }}</strong></td>
                            <td><strong>{{ stockData.quantity }}</strong></td>
                            <td><strong>{{ stockData.category_sperator_name }}{{ stockData.category || 'Belirtilmedi' }}</strong></td>
                            <td><strong>{{ stockData.brand }}</strong></td>
                            <td>
                              <span v-for="version in stockData.version" :key="version" class="d-block">
                                {{ version }}
                              </span>
                            </td>
                            <td v-if="hasRole(['Depo Sorumlusu', 'super-admin'])">
                              {{ formatPrice(stockData.cost_price) }}
                            </td>
                            <td v-if="hasRole(['Depo Sorumlusu', 'super-admin'])">
                              {{ formatPrice(stockData.base_cost_price) }}
                            </td>
                            <td>{{ formatPrice(stockData.sale_price) }}</td>
                            <td>
                              <div class="form-check form-switch">
                                <input 
                                  class="form-check-input" 
                                  type="checkbox"
                                  :checked="stockData.is_status == 1"
                                  @change="updateStatus(stockData.id, stockData.is_status == 1 ? 0 : 1)"
                                />
                              </div>
                            </td>
                            <td>
                              <div class="btn-group btn-group-sm">
                                <button 
                                  v-if="hasRole(['Depo Sorumlusu', 'super-admin'])"
                                  @click="createInvoice(stockData.id)" 
                                  class="btn btn-danger" 
                                  title="Fatura Ekle"
                                >
                                  <i class="bx bx-list-plus"></i>
                                </button>
                                <button 
                                  v-if="hasRole(['Depo Sorumlusu', 'super-admin'])"
                                  @click="editStockCard(stockData)" 
                                  class="btn btn-primary" 
                                  title="Düzenle"
                                >
                                  <i class="bx bx-edit-alt"></i>
                                </button>
                                                                 <button 
                                   v-if="hasRole(['Depo Sorumlusu', 'super-admin'])"
                                   @click="openPriceModal(stockData)" 
                                   class="btn btn-success" 
                                   title="Fiyat Değiştir"
                                 >
                                  <i class="bx bxs-dollar-circle"></i>
                                </button>
                                <button 
                                  v-if="hasRole(['super-admin'])"
                                  @click="deleteStockCard(stockData.id)" 
                                  class="btn btn-danger" 
                                  title="Sil"
                                >
                                  <i class="bx bxs-trash"></i>
                                </button>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div class="card mt-4" v-if="pagination">
      <div class="card-body">
        <nav aria-label="Page navigation">
          <ul class="pagination justify-content-center mb-0">
            <li class="page-item" :class="{ disabled: !pagination.prev_page_url }">
              <a class="page-link" href="#" @click.prevent="loadPage(pagination.current_page - 1)">
                Önceki
              </a>
            </li>
            <li 
              v-for="page in pagination.last_page" 
              :key="page"
              class="page-item"
              :class="{ active: page === pagination.current_page }"
            >
              <a class="page-link" href="#" @click.prevent="loadPage(page)">{{ page }}</a>
            </li>
            <li class="page-item" :class="{ disabled: !pagination.next_page_url }">
              <a class="page-link" href="#" @click.prevent="loadPage(pagination.current_page + 1)">
                Sonraki
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" :class="{ show: showDeleteModal }" :style="{ display: showDeleteModal ? 'block' : 'none' }" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Silmek için not girmelisiniz</h5>
            <button type="button" class="btn-close" @click="showDeleteModal = false"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Not</label>
              <input 
                type="text" 
                v-model="deleteNote" 
                class="form-control" 
                required
                placeholder="Silme nedeni..."
              />
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" @click="showDeleteModal = false">
              Kapat
            </button>
            <button type="button" class="btn btn-danger" @click="confirmDelete" :disabled="!deleteNote">
              Sil
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Price Update Modal -->
    <div class="modal fade" :class="{ show: showPriceModal }" :style="{ display: showPriceModal ? 'block' : 'none' }" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Fiyat Değişiklik İşlemi</h5>
            <button type="button" class="btn-close" @click="showPriceModal = false"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Satış Fiyatı</label>
              <input 
                type="number" 
                v-model="priceForm.sale_price" 
                class="form-control" 
                step="0.01"
                min="0"
              />
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" @click="showPriceModal = false">
              Kapat
            </button>
            <button type="button" class="btn btn-primary" @click="updatePrice">
              Fiyat Değiştir
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Transfer Modal -->
    <div class="modal fade" :class="{ show: showTransferModal }" :style="{ display: showTransferModal ? 'block' : 'none' }" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Sevk İşlemi</h5>
            <button type="button" class="btn-close" @click="showTransferModal = false"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Serial Number</label>
              <input 
                type="text" 
                v-model="transferForm.serial_number" 
                class="form-control" 
                placeholder="Seri Numarası"
              />
            </div>
            <div class="mb-3">
              <label class="form-label">Şube</label>
              <select v-model="transferForm.seller_id" class="form-control">
                <option v-for="seller in sellers" :key="seller.id" :value="seller.id">
                  {{ seller.name }}
                </option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Neden</label>
              <select v-model="transferForm.reason_id" class="form-control">
                <option v-for="reason in reasons" :key="reason.id" :value="reason.id">
                  {{ reason.name }}
                </option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" @click="showTransferModal = false">
              Kapat
            </button>
            <button type="button" class="btn btn-primary" @click="createTransfer">
              Sevk İşlemi Başlat
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading Overlay -->
    <div v-if="loading" class="loading-overlay">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Yükleniyor...</span>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted, computed } from 'vue'
import axios from 'axios'

export default {
  name: 'StockCardIndex',
  setup() {
    // Reactive data
    const loading = ref(false)
    const stockcards = ref([])
    const categories = ref([])
    const brands = ref([])
    const sellers = ref([])
    const colors = ref([])
    const reasons = ref([])
    const pagination = ref(null)
    const expandedItems = ref([])
    
    // Modal states
    const showDeleteModal = ref(false)
    const showPriceModal = ref(false)
    const showTransferModal = ref(false)
    const showCreateModal = ref(false)
    
    // Form data
    const filters = reactive({
      category_id: '',
      brand_id: '',
      seller_id: '',
      color_id: '',
      serialNumber: '',
      page: 1
    })
    
    const deleteNote = ref('')
    const selectedStockCardId = ref(null)
    
    const priceForm = reactive({
      stock_card_id: null,
      sale_price: ''
    })
    
    const transferForm = reactive({
      stock_card_id: null,
      serial_number: '',
      seller_id: '',
      reason_id: ''
    })

    // Methods
    const loadStockCards = async () => {
      loading.value = true
      try {
        const response = await axios.get('/api/stockcards', { params: filters })
        stockcards.value = response.data.data || response.data
        pagination.value = response.data.pagination || null
      } catch (error) {
        console.error('Error loading stock cards:', error)
        showToast('Stok kartları yüklenirken hata oluştu', 'error')
      } finally {
        loading.value = false
      }
    }

    const loadCategories = async () => {
      try {
        const response = await axios.get('/api/categories')
        categories.value = response.data
      } catch (error) {
        console.error('Error loading categories:', error)
      }
    }

    const loadBrands = async () => {
      try {
        const response = await axios.get('/api/brands')
        brands.value = response.data
      } catch (error) {
        console.error('Error loading brands:', error)
      }
    }

    const loadSellers = async () => {
      try {
        const response = await axios.get('/api/sellers')
        sellers.value = response.data
      } catch (error) {
        console.error('Error loading sellers:', error)
      }
    }

    const loadColors = async () => {
      try {
        const response = await axios.get('/api/colors')
        colors.value = response.data
      } catch (error) {
        console.error('Error loading colors:', error)
      }
    }

    const loadReasons = async () => {
      try {
        const response = await axios.get('/api/reasons')
        reasons.value = response.data
      } catch (error) {
        console.error('Error loading reasons:', error)
      }
    }

    const toggleExpanded = (id) => {
      const index = expandedItems.value.indexOf(id)
      if (index > -1) {
        expandedItems.value.splice(index, 1)
      } else {
        expandedItems.value.push(id)
      }
    }

    const updateStatus = async (id, status) => {
      try {
        await axios.post(`/api/stockcards/${id}/status`, { is_status: status })
        showToast('Durum güncellendi', 'success')
        loadStockCards()
      } catch (error) {
        console.error('Error updating status:', error)
        showToast('Durum güncellenirken hata oluştu', 'error')
      }
    }

    const deleteStockCard = (id) => {
      selectedStockCardId.value = id
      showDeleteModal.value = true
    }

    const confirmDelete = async () => {
      if (!deleteNote.value) {
        showToast('Silme notu gereklidir', 'error')
        return
      }

      try {
        await axios.post(`/api/stockcards/${selectedStockCardId.value}/delete`, {
          note: deleteNote.value
        })
        showToast('Stok kartı başarıyla silindi', 'success')
        showDeleteModal.value = false
        deleteNote.value = ''
        selectedStockCardId.value = null
        loadStockCards()
      } catch (error) {
        console.error('Error deleting stock card:', error)
        showToast('Silme işlemi başarısız', 'error')
      }
    }

    const openPriceModal = (stockData) => {
      priceForm.stock_card_id = stockData.id
      priceForm.sale_price = stockData.sale_price
      showPriceModal.value = true
    }

    const updatePrice = async () => {
      try {
        await axios.post('/api/stockcards/price-update', priceForm)
        showToast('Fiyat güncellendi', 'success')
        showPriceModal.value = false
        loadStockCards()
      } catch (error) {
        console.error('Error updating price:', error)
        showToast('Fiyat güncellenirken hata oluştu', 'error')
      }
    }

    const createTransfer = async () => {
      try {
        await axios.post('/api/stockcards/transfer', transferForm)
        showToast('Transfer işlemi başlatıldı', 'success')
        showTransferModal.value = false
        loadStockCards()
      } catch (error) {
        console.error('Error creating transfer:', error)
        showToast('Transfer işlemi başarısız', 'error')
      }
    }

    const editStockCard = (stockData) => {
      window.location.href = `/stockcard/edit/${stockData.id}`
    }

    const createInvoice = (stockCardId) => {
      window.location.href = `/invoice/create/${stockCardId}`
    }

    const showMovements = (stockCard) => {
      window.location.href = `/stockcard/movements/${stockCard.id}`
    }

    const loadPage = (page) => {
      if (page >= 1 && page <= pagination.value.last_page) {
        filters.page = page
        loadStockCards()
      }
    }

    const formatPrice = (price) => {
      return new Intl.NumberFormat('tr-TR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).format(price)
    }

    const hasRole = (roles) => {
      // Bu fonksiyon kullanıcının rollerini kontrol eder
      // Backend'den gelen user roles bilgisini kullanır
      return true // Geçici olarak true döndürüyoruz
    }

    const showToast = (message, type = 'info') => {
      // Toast notification gösterimi
      if (window.Swal) {
        Swal.fire({
          icon: type,
          title: message,
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000
        })
      } else {
        alert(message)
      }
    }

    // Lifecycle
    onMounted(() => {
      loadStockCards()
      loadCategories()
      loadBrands()
      loadSellers()
      loadColors()
      loadReasons()
    })

    return {
      // Data
      loading,
      stockcards,
      categories,
      brands,
      sellers,
      colors,
      reasons,
      pagination,
      expandedItems,
      filters,
      
      // Modal states
      showDeleteModal,
      showPriceModal,
      showTransferModal,
      showCreateModal,
      
      // Form data
      deleteNote,
      selectedStockCardId,
      priceForm,
      transferForm,
      
             // Methods
       loadStockCards,
       toggleExpanded,
       updateStatus,
       deleteStockCard,
       confirmDelete,
       openPriceModal,
       updatePrice,
       createTransfer,
       editStockCard,
       createInvoice,
       showMovements,
       loadPage,
       formatPrice,
       hasRole
    }
  }
}
</script>

<style scoped>
.stockcard-index {
  padding: 1.5rem;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.page-title {
  margin: 0;
  font-weight: 600;
}

.cursor-pointer {
  cursor: pointer;
}

.cursor-pointer:hover {
  background-color: rgba(0, 0, 0, 0.05);
}

.accordion-body {
  background-color: #f8f9fa;
  border-top: 1px solid #dee2e6;
}

.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255, 255, 255, 0.8);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.modal.show {
  background-color: rgba(0, 0, 0, 0.5);
}

.btn-group-sm .btn {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}

.table-sm th,
.table-sm td {
  padding: 0.5rem;
  font-size: 0.875rem;
}

.form-check-input:checked {
  background-color: #0d6efd;
  border-color: #0d6efd;
}

.pagination {
  margin-bottom: 0;
}

@media (max-width: 768px) {
  .page-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .header-actions {
    text-align: center;
  }
  
  .table-responsive {
    font-size: 0.875rem;
  }
}
</style>
