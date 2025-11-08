<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Sevkler /</span> Sevk listesi
    </h4>

    <div class="card">
      <!-- Filter Form -->
      <div class="card-body">
        <form @submit.prevent="searchTransfers" class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Stok</label>
            <input 
              v-model="filters.stockName" 
              type="text" 
              class="form-control"
            >
          </div>
          <div class="col-md-2">
            <label class="form-label">Marka</label>
            <select v-model="filters.brand" class="form-select" @change="getVersions">
              <option value="">Tümü</option>
              <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                {{ brand.name }}
              </option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Model</label>
            <select v-model="filters.version" class="form-select">
              <option value="">Tümü</option>
              <option v-for="version in versions" :key="version.id" :value="version.id">
                {{ version.name }}
              </option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Kategori</label>
            <select v-model="filters.category" class="form-select">
              <option value="">Tümü</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.path }}
              </option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Renk</label>
            <select v-model="filters.color" class="form-select">
              <option value="">Tümü</option>
              <option v-for="color in colors" :key="color.id" :value="color.id">
                {{ color.name }}
              </option>
            </select>
          </div>
          <div class="col-md-1">
            <label class="form-label">Şube</label>
            <select v-model="filters.seller" class="form-select">
              <option value="all">Tümü</option>
              <option v-for="seller in sellers" :key="seller.id" :value="seller.id">
                {{ seller.name }}
              </option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Seri Numarası</label>
            <input 
              v-model="filters.serialNumber" 
              type="text" 
              class="form-control"
            >
          </div>
          <div class="col-12 mt-4">
            <button type="submit" class="btn btn-sm btn-outline-primary">Ara</button>
            <router-link to="/transfers/create" class="btn btn-sm btn-primary ms-2">
              Yeni Sevk Ekle
            </router-link>
          </div>
        </form>
      </div>

      <!-- Tabs -->
      <div class="nav-align-top mb-4">
        <ul class="nav nav-tabs" role="tablist">
          <li class="nav-item">
            <button 
              type="button" 
              class="nav-link" 
              :class="{ active: activeTab === 'incoming' }"
              @click="activeTab = 'incoming'"
            >
              Gelen Sevkler
            </button>
          </li>
          <li class="nav-item">
            <button 
              type="button" 
              class="nav-link" 
              :class="{ active: activeTab === 'outgoing' }"
              @click="activeTab = 'outgoing'"
            >
              Yapılan Sevkler
            </button>
          </li>
        </ul>

        <div class="tab-content">
          <!-- Incoming Transfers -->
          <div v-if="activeTab === 'incoming'" class="tab-pane fade show active">
            <div class="table-responsive text-nowrap">
              <table class="table">
                <thead>
                  <tr>
                    <th>Numara</th>
                    <th>Tip</th>
                    <th>Gönderici Bayi</th>
                    <th>Oluşturma Zamanı</th>
                    <th>Alıcı Bayi</th>
                    <th>Gönderen</th>
                    <th>Teslim Alan</th>
                    <th>Durum</th>
                    <th>Teslim Tarihi</th>
                    <th>İşlemler</th>
                  </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                  <tr v-for="transfer in incomingTransfers" :key="transfer.id">
                    <td>
                      <router-link :to="`/transfers/${transfer.id}`">
                        {{ transfer.number }}
                      </router-link>
                    </td>
                    <td>{{ transfer.type === 'phone' ? 'TELEFON' : 'DİĞER' }}</td>
                    <td>{{ transfer.mainSeller?.name }}</td>
                    <td>{{ formatDate(transfer.created_at) }}</td>
                    <td>{{ transfer.deliverySeller?.name }}</td>
                    <td>{{ transfer.user?.name }}</td>
                    <td>{{ transfer.confirmUser?.name || '-' }}</td>
                    <td>
                      <span :class="`badge bg-label-${TRANSFER_STATUS_COLOR[transfer.is_status]}`">
                        {{ TRANSFER_STATUS[transfer.is_status] }}
                      </span>
                    </td>
                    <td>{{ transfer.comfirm_date || '-' }}</td>
                    <td>
                      <div class="btn-group" role="group">
                        <button 
                          v-if="canApprove(transfer)"
                          @click="approveTransfer(transfer.id)"
                          class="btn btn-sm btn-success"
                          title="Onayla"
                        >
                          <i class="bx bx-check"></i>
                        </button>
                        <button 
                          v-if="canReject(transfer)"
                          @click="rejectTransfer(transfer.id)"
                          class="btn btn-sm btn-danger"
                          title="Reddet"
                        >
                          <i class="bx bx-x"></i>
                        </button>
                        <router-link 
                          :to="`/transfers/${transfer.id}/edit`"
                          class="btn btn-sm btn-primary"
                          title="Düzenle"
                        >
                          <i class="bx bx-edit"></i>
                        </router-link>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Outgoing Transfers -->
          <div v-if="activeTab === 'outgoing'" class="tab-pane fade show active">
            <div class="table-responsive text-nowrap">
              <table class="table">
                <thead>
                  <tr>
                    <th>Numara</th>
                    <th>Tip</th>
                    <th>Gönderici Bayi</th>
                    <th>Oluşturma Zamanı</th>
                    <th>Alıcı Bayi</th>
                    <th>Gönderen</th>
                    <th>Teslim Alan</th>
                    <th>Durum</th>
                    <th>Teslim Tarihi</th>
                    <th>İşlemler</th>
                  </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                  <tr v-for="transfer in outgoingTransfers" :key="transfer.id">
                    <td>
                      <router-link :to="`/transfers/${transfer.id}`">
                        {{ transfer.number }}
                      </router-link>
                    </td>
                    <td>{{ transfer.type === 'phone' ? 'TELEFON' : 'DİĞER' }}</td>
                    <td>{{ transfer.mainSeller?.name }}</td>
                    <td>{{ formatDate(transfer.created_at) }}</td>
                    <td>{{ transfer.deliverySeller?.name }}</td>
                    <td>{{ transfer.user?.name }}</td>
                    <td>{{ transfer.confirmUser?.name || '-' }}</td>
                    <td>
                      <span :class="`badge bg-label-${TRANSFER_STATUS_COLOR[transfer.is_status]}`">
                        {{ TRANSFER_STATUS[transfer.is_status] }}
                      </span>
                    </td>
                    <td>{{ transfer.comfirm_date || '-' }}</td>
                    <td>
                      <router-link 
                        :to="`/transfers/${transfer.id}/edit`"
                        class="btn btn-sm btn-primary"
                        title="Düzenle"
                      >
                        <i class="bx bx-edit"></i>
                      </router-link>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useTransferStore } from '@/stores/transfer'
import type { Transfer, TransferFilter } from '@/types/transfer'
import { TRANSFER_STATUS, TRANSFER_STATUS_COLOR } from '@/types/transfer'

// Store
const transferStore = useTransferStore()

// Reactive data
const activeTab = ref<'incoming' | 'outgoing'>('incoming')
const filters = ref<TransferFilter>({})

// Mock data (will be replaced with API calls)
const brands = ref([])
const versions = ref([])
const categories = ref([])
const colors = ref([])
const sellers = ref([])

// Computed
const incomingTransfers = computed(() => transferStore.transfers.filter(t => t.is_status <= 3))
const outgoingTransfers = computed(() => transferStore.transfers.filter(t => t.is_status > 3))

// Methods
const searchTransfers = async () => {
  await transferStore.fetchTransfers(filters.value)
}

const getVersions = async () => {
  if (filters.value.brand) {
    // API call to get versions for selected brand
  }
}

const approveTransfer = async (id: number) => {
  if (confirm('Onaylamak istediğinizden emin misiniz?')) {
    await transferStore.updateTransferStatus(id, 3)
  }
}

const rejectTransfer = async (id: number) => {
  if (confirm('Reddetmek istediğinizden emin misiniz?')) {
    await transferStore.updateTransferStatus(id, 4)
  }
}

const canApprove = (transfer: Transfer) => {
  return transfer.is_status === 2 && transfer.delivery_seller_id === 1
}

const canReject = (transfer: Transfer) => {
  return transfer.is_status === 1 || transfer.is_status === 2
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('tr-TR')
}

// Lifecycle
onMounted(async () => {
  await transferStore.fetchTransfers()
  // Load filter data
  // brands.value = await api.getBrands()
  // categories.value = await api.getCategories()
  // colors.value = await api.getColors()
  // sellers.value = await api.getSellers()
})
</script>
