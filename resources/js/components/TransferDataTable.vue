<template>
  <div class="transfer-datatable">
    <!-- Header with Stats -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="card bg-primary text-white">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <h6 class="card-title">Toplam Sevk</h6>
                <h3 class="mb-0">{{ stats.total || 0 }}</h3>
              </div>
              <div class="align-self-center">
                <i class="bx bx-transfer fs-1"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card bg-warning text-white">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <h6 class="card-title">Bekleyen</h6>
                <h3 class="mb-0">{{ stats.pending || 0 }}</h3>
              </div>
              <div class="align-self-center">
                <i class="bx bx-time fs-1"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card bg-success text-white">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <h6 class="card-title">Onaylanan</h6>
                <h3 class="mb-0">{{ stats.approved || 0 }}</h3>
              </div>
              <div class="align-self-center">
                <i class="bx bx-check-circle fs-1"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card bg-danger text-white">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <h6 class="card-title">Reddedilen</h6>
                <h3 class="mb-0">{{ stats.rejected || 0 }}</h3>
              </div>
              <div class="align-self-center">
                <i class="bx bx-x-circle fs-1"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-2">
            <label class="form-label">Durum</label>
            <select v-model="filters.status" class="form-select" @change="loadData">
              <option value="">Tümü</option>
              <option value="1">Bekliyor</option>
              <option value="2">Ön Onay</option>
              <option value="3">Onaylandı</option>
              <option value="4">Reddedildi</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Bayi</label>
            <select v-model="filters.seller_id" class="form-select" @change="loadData">
              <option value="">Tümü</option>
              <option v-for="seller in sellers" :key="seller.id" :value="seller.id">
                {{ seller.name }}
              </option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Seri No</label>
            <input v-model="filters.serialNumber" type="text" class="form-control" 
                   placeholder="Seri numarası..." @keyup.enter="loadData">
          </div>
          <div class="col-md-2">
            <label class="form-label">Tarih Başlangıç</label>
            <input v-model="filters.date_from" type="date" class="form-control" @change="loadData">
          </div>
          <div class="col-md-2">
            <label class="form-label">Tarih Bitiş</label>
            <input v-model="filters.date_to" type="date" class="form-control" @change="loadData">
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button @click="loadData" class="btn btn-primary me-2">
              <i class="bx bx-search"></i> Ara
            </button>
            <button @click="clearFilters" class="btn btn-outline-secondary">
              <i class="bx bx-refresh"></i> Temizle
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- DataTable -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Sevk Listesi</h5>
        <div class="d-flex gap-2">
          <button @click="exportData" class="btn btn-outline-success btn-sm">
            <i class="bx bx-export"></i> Dışa Aktar
          </button>
          <button @click="loadData" class="btn btn-outline-primary btn-sm">
            <i class="bx bx-refresh"></i> Yenile
          </button>
        </div>
      </div>
      <div class="card-body">
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Yükleniyor...</span>
          </div>
          <p class="mt-2">Veriler yükleniyor...</p>
        </div>

        <!-- Data Table -->
        <div v-else>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th>Sevk No</th>
                  <th>Tip</th>
                  <th>Gönderici</th>
                  <th>Alıcı</th>
                  <th>Oluşturma</th>
                  <th>Durum</th>
                  <th>İşlemler</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="transfer in transfers.data" :key="transfer.id" 
                    :class="getRowClass(transfer.is_status)">
                  <td>
                    <a href="#" @click.prevent="showDetails(transfer)" class="fw-bold text-primary">
                      {{ transfer.number || '#' + transfer.id }}
                    </a>
                  </td>
                  <td>
                    <span class="badge" :class="transfer.type === 'phone' ? 'bg-info' : 'bg-secondary'">
                      {{ transfer.type === 'phone' ? 'TELEFON' : 'DİĞER' }}
                    </span>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-2">
                        <span class="avatar-initial rounded-circle bg-primary">
                          {{ getInitials(transfer.main_seller?.name) }}
                        </span>
                      </div>
                      <span>{{ transfer.main_seller?.name || 'N/A' }}</span>
                    </div>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-2">
                        <span class="avatar-initial rounded-circle bg-success">
                          {{ getInitials(transfer.delivery_seller?.name) }}
                        </span>
                      </div>
                      <span>{{ transfer.delivery_seller?.name || 'N/A' }}</span>
                    </div>
                  </td>
                  <td>
                    <div class="d-flex flex-column">
                      <small class="text-muted">{{ formatDate(transfer.created_at) }}</small>
                      <small class="text-muted">{{ formatTime(transfer.created_at) }}</small>
                    </div>
                  </td>
                  <td>
                    <span class="badge" :class="getStatusClass(transfer.is_status)">
                      {{ getStatusText(transfer.is_status) }}
                    </span>
                  </td>
                  <td>
                    <div class="btn-group btn-group-sm">
                      <button @click="showDetails(transfer)" class="btn btn-outline-primary" 
                              title="Detayları Görüntüle">
                        <i class="bx bx-show"></i>
                      </button>
                      <button v-if="canEdit(transfer)" @click="editTransfer(transfer)" 
                              class="btn btn-outline-warning" title="Düzenle">
                        <i class="bx bx-edit"></i>
                      </button>
                      <button v-if="canApprove(transfer)" @click="approveTransfer(transfer)" 
                              class="btn btn-outline-success" title="Onayla">
                        <i class="bx bx-check"></i>
                      </button>
                      <button v-if="canReject(transfer)" @click="rejectTransfer(transfer)" 
                              class="btn btn-outline-danger" title="Reddet">
                        <i class="bx bx-x"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="transfers.data && transfers.data.length > 0" class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
              Toplam {{ transfers.total }} kayıttan {{ transfers.from }}-{{ transfers.to }} arası gösteriliyor
            </div>
            <nav>
              <ul class="pagination pagination-sm mb-0">
                <li v-for="link in transfers.links" :key="link.label" 
                    :class="['page-item', { active: link.active, disabled: !link.url }]">
                  <a v-if="link.url" @click.prevent="loadData(link.url)" 
                     class="page-link" v-html="link.label"></a>
                  <span v-else class="page-link" v-html="link.label"></span>
                </li>
              </ul>
            </nav>
          </div>

          <!-- Empty State -->
          <div v-if="!loading && (!transfers.data || transfers.data.length === 0)" 
               class="text-center py-5">
            <i class="bx bx-transfer fs-1 text-muted"></i>
            <h5 class="mt-3 text-muted">Henüz sevk bulunmuyor</h5>
            <p class="text-muted">Yeni bir sevk oluşturmak için yukarıdaki butonu kullanın.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Transfer Details Modal -->
    <div class="modal fade" id="transferDetailsModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Sevk Detayları</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body" v-if="selectedTransfer">
            <div class="row">
                             <div class="col-md-6">
                 <h6>Genel Bilgiler</h6>
                 <table class="table table-sm">
                   <tbody>
                     <tr>
                       <td><strong>Sevk No:</strong></td>
                       <td>{{ selectedTransfer.number || '#' + selectedTransfer.id }}</td>
                     </tr>
                     <tr>
                       <td><strong>Tip:</strong></td>
                       <td>{{ selectedTransfer.type === 'phone' ? 'Telefon' : 'Diğer' }}</td>
                     </tr>
                     <tr>
                       <td><strong>Durum:</strong></td>
                       <td>
                         <span class="badge" :class="getStatusClass(selectedTransfer.is_status)">
                           {{ getStatusText(selectedTransfer.is_status) }}
                         </span>
                       </td>
                     </tr>
                     <tr>
                       <td><strong>Oluşturma:</strong></td>
                       <td>{{ formatDate(selectedTransfer.created_at) }}</td>
                     </tr>
                   </tbody>
                 </table>
               </div>
               <div class="col-md-6">
                 <h6>Bayi Bilgileri</h6>
                 <table class="table table-sm">
                   <tbody>
                     <tr>
                       <td><strong>Gönderici:</strong></td>
                       <td>{{ selectedTransfer.main_seller?.name }}</td>
                     </tr>
                     <tr>
                       <td><strong>Alıcı:</strong></td>
                       <td>{{ selectedTransfer.delivery_seller?.name }}</td>
                     </tr>
                     <tr>
                       <td><strong>Gönderen:</strong></td>
                       <td>{{ selectedTransfer.user?.name }}</td>
                     </tr>
                     <tr v-if="selectedTransfer.comfirm_id">
                       <td><strong>Onaylayan:</strong></td>
                       <td>{{ selectedTransfer.confirmed_by?.name }}</td>
                     </tr>
                   </tbody>
                 </table>
               </div>
            </div>
            
            <div v-if="selectedTransfer.serial_list && selectedTransfer.serial_list.length > 0">
              <h6 class="mt-3">Seri Numaraları</h6>
              <div class="row">
                <div v-for="serial in selectedTransfer.serial_list" :key="serial" 
                     class="col-md-3 mb-2">
                  <span class="badge bg-light text-dark">{{ serial }}</span>
                </div>
              </div>
            </div>

            <div v-if="selectedTransfer.description" class="mt-3">
              <h6>Açıklama</h6>
              <p class="text-muted">{{ selectedTransfer.description }}</p>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
            <button v-if="canEdit(selectedTransfer)" @click="editTransfer(selectedTransfer)" 
                    class="btn btn-warning">Düzenle</button>
            <button v-if="canApprove(selectedTransfer)" @click="approveTransfer(selectedTransfer)" 
                    class="btn btn-success">Onayla</button>
            <button v-if="canReject(selectedTransfer)" @click="rejectTransfer(selectedTransfer)" 
                    class="btn btn-danger">Reddet</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'TransferDataTable',
  props: {
    apiUrl: {
      type: String,
      required: true
    },
    sellers: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      transfers: {
        data: [],
        total: 0,
        from: 0,
        to: 0,
        links: []
      },
      stats: {
        total: 0,
        pending: 0,
        approved: 0,
        rejected: 0
      },
      filters: {
        status: '',
        seller_id: '',
        serialNumber: '',
        date_from: '',
        date_to: ''
      },
      loading: false,
      selectedTransfer: null
    }
  },
  mounted() {
    this.loadData()
    this.loadStats()
  },
  methods: {
    async loadData(url = null) {
      this.loading = true
      try {
        const params = { ...this.filters }
        const response = await axios.get(url || this.apiUrl, { params })
        this.transfers = response.data
      } catch (error) {
        console.error('Veri yüklenirken hata:', error)
        this.$toast?.error('Veriler yüklenirken bir hata oluştu')
      } finally {
        this.loading = false
      }
    },

    async loadStats() {
      try {
        const response = await axios.get(`${this.apiUrl}/stats`)
        this.stats = response.data
      } catch (error) {
        console.error('İstatistikler yüklenirken hata:', error)
      }
    },

    clearFilters() {
      this.filters = {
        status: '',
        seller_id: '',
        serialNumber: '',
        date_from: '',
        date_to: ''
      }
      this.loadData()
    },

    showDetails(transfer) {
      this.selectedTransfer = transfer
      const modal = new bootstrap.Modal(document.getElementById('transferDetailsModal'))
      modal.show()
    },

    editTransfer(transfer) {
      window.location.href = `/transfer/edit?id=${transfer.id}`
    },

    async approveTransfer(transfer) {
      if (!confirm('Bu sevki onaylamak istediğinizden emin misiniz?')) return
      
      try {
        await axios.get(`/transfer/update?id=${transfer.id}&is_status=3`)
        this.$toast?.success('Sevk başarıyla onaylandı')
        this.loadData()
        this.loadStats()
      } catch (error) {
        this.$toast?.error('Onaylama işlemi başarısız')
      }
    },

    async rejectTransfer(transfer) {
      if (!confirm('Bu sevki reddetmek istediğinizden emin misiniz?')) return
      
      try {
        await axios.get(`/transfer/update?id=${transfer.id}&is_status=4`)
        this.$toast?.success('Sevk başarıyla reddedildi')
        this.loadData()
        this.loadStats()
      } catch (error) {
        this.$toast?.error('Reddetme işlemi başarısız')
      }
    },

    exportData() {
      const params = new URLSearchParams(this.filters)
      window.open(`${this.apiUrl}/export?${params.toString()}`, '_blank')
    },

    getStatusClass(status) {
      const classes = {
        1: 'bg-warning',
        2: 'bg-info',
        3: 'bg-success',
        4: 'bg-danger'
      }
      return classes[status] || 'bg-secondary'
    },

    getStatusText(status) {
      const texts = {
        1: 'Bekliyor',
        2: 'Ön Onay',
        3: 'Onaylandı',
        4: 'Reddedildi'
      }
      return texts[status] || 'Bilinmiyor'
    },

    getRowClass(status) {
      const classes = {
        1: 'table-warning',
        2: 'table-info',
        3: 'table-success',
        4: 'table-danger'
      }
      return classes[status] || ''
    },

    getInitials(name) {
      if (!name) return '?'
      return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2)
    },

    formatDate(date) {
      return new Date(date).toLocaleDateString('tr-TR')
    },

    formatTime(date) {
      return new Date(date).toLocaleTimeString('tr-TR', { 
        hour: '2-digit', 
        minute: '2-digit' 
      })
    },

    canEdit(transfer) {
      return transfer && transfer.is_status === 1
    },

    canApprove(transfer) {
      return transfer && (transfer.is_status === 1 || transfer.is_status === 2)
    },

    canReject(transfer) {
      return transfer && (transfer.is_status === 1 || transfer.is_status === 2)
    }
  }
}
</script>

<style scoped>
.transfer-datatable .avatar {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  font-weight: bold;
  font-size: 12px;
}

.transfer-datatable .table th {
  border-top: none;
  font-weight: 600;
  color: #6c757d;
}

.transfer-datatable .table td {
  vertical-align: middle;
}

.transfer-datatable .btn-group .btn {
  padding: 0.25rem 0.5rem;
}

.transfer-datatable .badge {
  font-size: 0.75rem;
  padding: 0.375rem 0.75rem;
}

.transfer-datatable .card {
  border: none;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.transfer-datatable .card-header {
  background-color: #f8f9fa;
  border-bottom: 1px solid #dee2e6;
}

.transfer-datatable .form-control,
.transfer-datatable .form-select {
  border-radius: 0.375rem;
  border: 1px solid #dee2e6;
}

.transfer-datatable .form-control:focus,
.transfer-datatable .form-select:focus {
  border-color: #86b7fe;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.transfer-datatable .pagination .page-link {
  border-radius: 0.375rem;
  margin: 0 0.125rem;
}

.transfer-datatable .modal-content {
  border-radius: 0.5rem;
  border: none;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.transfer-datatable .table-hover tbody tr:hover {
  background-color: rgba(0, 0, 0, 0.02);
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .transfer-datatable .btn-group {
    flex-direction: column;
  }
  
  .transfer-datatable .btn-group .btn {
    margin-bottom: 0.25rem;
  }
  
  .transfer-datatable .table-responsive {
    font-size: 0.875rem;
  }
}
</style>
