<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Sevkler /</span> 
      {{ isEdit ? 'Sevk Düzenle' : 'Yeni Sevk' }}
    </h4>

    <div class="card card-vue">
      <div class="card-body">
        <form @submit.prevent="submitForm" class="form-vue">
          <div class="row">
            <!-- Sender Seller -->
            <div class="col-md-6 mb-3">
              <label class="form-label">Gönderici Bayi</label>
              <select v-model="form.main_seller_id" class="form-select" required>
                <option value="">Bayi Seçiniz</option>
                <option v-for="seller in sellers" :key="seller.id" :value="seller.id">
                  {{ seller.name }}
                </option>
              </select>
            </div>

            <!-- Receiver Seller -->
            <div class="col-md-6 mb-3">
              <label class="form-label">Alıcı Bayi</label>
              <select v-model="form.delivery_seller_id" class="form-select" required>
                <option value="">Bayi Seçiniz</option>
                <option v-for="seller in sellers" :key="seller.id" :value="seller.id">
                  {{ seller.name }}
                </option>
              </select>
            </div>

            <!-- Transfer Type -->
            <div class="col-md-6 mb-3">
              <label class="form-label">Sevk Tipi</label>
              <select v-model="form.type" class="form-select" required>
                <option value="phone">Telefon</option>
                <option value="other">Diğer</option>
              </select>
            </div>

            <!-- Serial Numbers -->
            <div class="col-12 mb-3">
              <label class="form-label">Seri Numaraları</label>
              <div class="serial-inputs">
                <div v-for="(serial, index) in form.serial_list" :key="index" class="input-group mb-2">
                  <input 
                    v-model="form.serial_list[index]" 
                    type="text" 
                    class="form-control" 
                    :placeholder="`Seri No ${index + 1}`"
                    required
                  >
                  <button 
                    type="button" 
                    class="btn btn-outline-danger" 
                    @click="removeSerial(index)"
                    v-if="form.serial_list.length > 1"
                  >
                    <i class="bx bx-trash"></i>
                  </button>
                </div>
              </div>
              <button type="button" class="btn btn-outline-primary btn-sm" @click="addSerial">
                <i class="bx bx-plus"></i> Seri No Ekle
              </button>
            </div>

            <!-- Description -->
            <div class="col-12 mb-3">
              <label class="form-label">Açıklama</label>
              <textarea 
                v-model="form.description" 
                class="form-control" 
                rows="3"
                placeholder="Sevk açıklaması..."
              ></textarea>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="d-flex justify-content-end gap-2">
            <router-link to="/transfers" class="btn btn-secondary">
              <i class="bx bx-arrow-back me-1"></i>
              İptal
            </router-link>
            <button type="submit" class="btn btn-vue" :disabled="loading">
              <i v-if="loading" class="bx bx-loader-alt bx-spin me-1"></i>
              <i v-else class="bx bx-save me-1"></i>
              {{ isEdit ? 'Güncelle' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useTransferStore } from '@/stores/transfer'
import type { TransferForm } from '@/types/transfer'

const route = useRoute()
const router = useRouter()
const transferStore = useTransferStore()

// Reactive data
const loading = ref(false)
const form = ref<TransferForm>({
  type: 'phone',
  main_seller_id: 0,
  delivery_seller_id: 0,
  serial_list: [''],
  description: ''
})

// Mock data
const sellers = ref([
  { id: 1, name: 'Ana Bayi' },
  { id: 2, name: 'Alt Bayi 1' },
  { id: 3, name: 'Alt Bayi 2' }
])

// Computed
const isEdit = computed(() => route.params.id !== undefined)

// Methods
const addSerial = () => {
  form.value.serial_list.push('')
}

const removeSerial = (index: number) => {
  form.value.serial_list.splice(index, 1)
}

const submitForm = async () => {
  loading.value = true
  
  try {
    // Filter out empty serial numbers
    const filteredSerials = form.value.serial_list.filter(serial => serial.trim() !== '')
    
    if (filteredSerials.length === 0) {
      alert('En az bir seri numarası girmelisiniz')
      return
    }

    const formData = {
      ...form.value,
      serial_list: filteredSerials
    }

    if (isEdit.value) {
      await transferStore.updateTransfer(Number(route.params.id), formData)
    } else {
      await transferStore.createTransfer(formData)
    }

    router.push('/transfers')
  } catch (error) {
    console.error('Form submission error:', error)
    alert('Bir hata oluştu')
  } finally {
    loading.value = false
  }
}

const loadTransfer = async () => {
  if (isEdit.value) {
    const transfer = transferStore.getTransferById(Number(route.params.id))
    if (transfer) {
      form.value = {
        type: transfer.type,
        main_seller_id: transfer.main_seller_id,
        delivery_seller_id: transfer.delivery_seller_id,
        serial_list: transfer.serial_list || [''],
        description: transfer.description || ''
      }
    }
  }
}

// Lifecycle
onMounted(async () => {
  await transferStore.fetchTransfers()
  await loadTransfer()
})
</script>

<style scoped>
.serial-inputs {
  max-height: 200px;
  overflow-y: auto;
}

.input-group {
  display: flex;
  align-items: center;
}

.input-group .form-control {
  flex: 1;
}
</style>
