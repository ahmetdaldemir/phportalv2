@extends('layouts.admin')

@section('content')
<div id="invoice-form-app" class="container-xxl flex-grow-1 container-p-y">
    <form @submit.prevent="submitForm" class="form-repeater source-item py-sm-3">
        <input type="hidden" v-model="form.id" />
        
            <div class="row invoice-add">
                <!-- Invoice Add-->
                <div class="col-lg-10 col-12 mb-lg-0 mb-4">
                    <div class="card invoice-preview-card">
                        <div class="card-body">
                            <div class="row p-sm-3 p-0">
                            <!-- Customer Selection -->
                                <div class="col-md-6 mb-md-0 mb-4">
                                    <div class="row mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="bx bx-user me-1"></i>Cari Seçiniz
                                    </label>
                                        <div class="col-md-9">
                                        <select 
                                            v-model="form.customer_id" 
                                            @change="onCustomerChange"
                                            class="form-select">
                                            <option value="1">Genel Cari</option>
                                            <option 
                                                v-for="customer in customers" 
                                                :key="customer.id"
                                                :value="customer.id"
                                                v-if="customer.type === 'account'">
                                                @{{ customer.fullname }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                        <button 
                                            @click="openCustomerModal" 
                                            class="btn btn-primary" 
                                            type="button">
                                            <i class="bx bx-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Invoice Details -->
                                <div class="col-md-6">
                                    <dl class="row mb-2">
                                        <dt class="col-sm-6 mb-2 mb-sm-0 text-md-end">
                                            <span class="h4 text-capitalize mb-0 text-nowrap">Invoice #</span>
                                        </dt>
                                        <dd class="col-sm-6 d-flex justify-content-md-end">
                                            <div class="w-px-150">
                                            <input 
                                                v-model="form.number" 
                                                type="text" 
                                                class="form-control"
                                                placeholder="Otomatik oluşturulacak">
                                            </div>
                                        </dd>
                                        <dt class="col-sm-6 mb-2 mb-sm-0 text-md-end">
                                            <span class="fw-normal">Fatura Tarihi:</span>
                                        </dt>
                                        <dd class="col-sm-6 d-flex justify-content-md-end">
                                            <div class="w-px-150">
                                            <input 
                                                v-model="form.create_date" 
                                                type="date" 
                                                class="form-control">
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        <hr class="mx-n4">
                                </div>
                                </div>
                                </div>

            <!-- Sidebar -->
            <div class="col-lg-2">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-money me-2"></i>Ödeme Türü
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Payment Type Selection -->
                        <div class="mb-3">
                            <label class="form-label">Ödeme Şekli</label>
                            <div class="form-check">
                                <input 
                                    v-model="form.payment_type" 
                                    class="form-check-input" 
                                    type="radio" 
                                    value="cash" 
                                    id="paymentCash">
                                <label class="form-check-label" for="paymentCash">
                                    <i class="bx bx-money me-1"></i>Nakit
                                </label>
                            </div>
                            <div class="form-check">
                                <input 
                                    v-model="form.payment_type" 
                                    class="form-check-input" 
                                    type="radio" 
                                    value="credit_card" 
                                    id="paymentCard">
                                <label class="form-check-label" for="paymentCard">
                                    <i class="bx bx-credit-card me-1"></i>Kredi Kartı
                                </label>
                                    </div>
                            <div class="form-check">
                                <input 
                                    v-model="form.payment_type" 
                                    class="form-check-input" 
                                    type="radio" 
                                    value="installment" 
                                    id="paymentInstallment">
                                <label class="form-check-label" for="paymentInstallment">
                                    <i class="bx bx-calendar me-1"></i>Taksit
                                </label>
                            </div>
                        </div>

                        <!-- Payment Amounts -->
                        <div class="mb-3" v-if="form.payment_type === 'cash'">
                            <label class="form-label">Nakit Tutar</label>
                            <input 
                                v-model.number="form.cash" 
                                type="number" 
                                step="0.01"
                                class="form-control">
                        </div>
                        
                        <div class="mb-3" v-if="form.payment_type === 'credit_card'">
                            <label class="form-label">Kredi Kartı Tutar</label>
                            <input 
                                v-model.number="form.credit_card" 
                                type="number" 
                                step="0.01"
                                class="form-control">
                        </div>
                        
                        <div class="mb-3" v-if="form.payment_type === 'installment'">
                            <label class="form-label">Taksit Tutar</label>
                            <input 
                                v-model.number="form.installment" 
                                type="number" 
                                step="0.01"
                                class="form-control">
                        </div>

                        <!-- Additional Options -->
                        <div class="mb-3">
                            <label class="form-label">Açıklama</label>
                            <textarea 
                                v-model="form.description" 
                                class="form-control" 
                                rows="3"
                                placeholder="Fatura açıklaması">
                            </textarea>
                        </div>
                    </div>
                </div>

                <!-- Summary Card -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-calculator me-2"></i>Özet
                        </h5>
                    </div>
                        <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Ara Toplam:</span>
                            <strong>@{{ formatCurrency(totals.subtotal) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>KDV:</span>
                            <strong>@{{ formatCurrency(totals.tax) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>İndirim:</span>
                            <strong>@{{ formatCurrency(totals.discount) }}</strong>
                    </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Toplam:</span>
                            <strong class="text-primary">@{{ formatCurrency(totals.total) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
                </div>

        <!-- Submit Button -->
        <div class="row mt-4">
            <div class="col-12">
                <button 
                    :disabled="submitting || !isFormValid" 
                    type="submit" 
                    class="btn btn-primary btn-lg w-100">
                    <span v-if="submitting" class="spinner-border spinner-border-sm me-2"></span>
                    <i v-else class="bx bx-save me-2"></i>
                    @{{ submitting ? 'Kaydediliyor...' : 'Faturayı Kaydet' }}
                </button>
            </div>
            </div>
        </form>

    <!-- Customer Information Display -->
    <div v-if="selectedCustomer" class="alert alert-info mt-3">
        <h6><i class="bx bx-info-circle me-1"></i>Seçili Müşteri Bilgileri</h6>
        <p class="mb-1"><strong>Ad:</strong> @{{ selectedCustomer.fullname }}</p>
        <p class="mb-1" v-if="selectedCustomer.phone1"><strong>Telefon:</strong> @{{ selectedCustomer.phone1 }}</p>
        <p class="mb-0" v-if="selectedCustomer.address"><strong>Adres:</strong> @{{ selectedCustomer.address }}</p>
    </div>
</div>

<!-- Include Customer Modal -->
@include('components.customermodal')

<script>
const { createApp } = Vue;

createApp({
    data() {
        return {
            form: {
                id: @json($invoices->id ?? null),
                customer_id: @json($invoices->customer_id ?? '1'),
                number: @json($invoices->number ?? ''),
                create_date: @json($invoices->create_date ?? date('Y-m-d')),
                payment_type: @json($invoices->payment_type ?? 'cash'),
                cash: @json($invoices->cash ?? 0),
                credit_card: @json($invoices->credit_card ?? 0),
                installment: @json($invoices->installment ?? 0),
                description: @json($invoices->description ?? ''),
                type: @json($invoices->type ?? '1'),
                is_status: @json($invoices->is_status ?? 1),
                total_price: @json($invoices->total_price ?? 0),
                tax_total: @json($invoices->tax_total ?? 0),
                discount_total: @json($invoices->discount_total ?? 0)
            },
            customers: @json($customers ?? []),
            submitting: false
        }
    },
    computed: {
        selectedCustomer() {
            return this.customers.find(c => c.id == this.form.customer_id);
        },
        totals() {
            const subtotal = this.form.total_price || 0;
            const tax = this.form.tax_total || 0;
            const discount = this.form.discount_total || 0;
            const total = subtotal + tax - discount;
            
            return {
                subtotal,
                tax,
                discount,
                total
            };
        },
        isFormValid() {
            return this.form.customer_id && this.form.create_date;
        }
    },
    methods: {
        async loadCustomers() {
            try {
                const response = await fetch('/api/customers?type=account');
                const data = await response.json();
                this.customers = data;
            } catch (error) {
                console.error('Error loading customers:', error);
            }
        },
        
        onCustomerChange() {
            // Customer specific logic
        },
        
        openCustomerModal() {
            const modal = new bootstrap.Modal(document.getElementById('editUser'));
            modal.show();
        },
        
        async submitForm() {
            if (!this.isFormValid) {
                alert('Lütfen gerekli alanları doldurunuz!');
                return;
            }
            
            this.submitting = true;
            
            try {
                const formData = new FormData();
                Object.keys(this.form).forEach(key => {
                    formData.append(key, this.form[key] || '');
                });
                
                const response = await fetch('{{ route("invoice.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    const result = await response.json();
                    alert('Fatura başarıyla kaydedildi!');
                    window.location.href = `/invoice?type=${this.form.type}`;
                } else {
                    throw new Error('Form gönderimi başarısız');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Kaydetme sırasında hata oluştu!');
            } finally {
                this.submitting = false;
            }
        },
        
        formatCurrency(amount) {
            return new Intl.NumberFormat('tr-TR', {
                style: 'currency',
                currency: 'TRY'
            }).format(amount || 0);
        }
    },
    
    mounted() {
        // Listen for customer save events
        window.addEventListener('customerSaved', (event) => {
            this.customers.push(event.detail);
            this.form.customer_id = event.detail.id;
        });
        
        // Load customers if not provided
        if (!this.customers.length) {
            this.loadCustomers();
        }
    }
}).mount('#invoice-form-app');
    </script>

<style scoped>
.invoice-preview-card {
    box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.12);
    border: 1px solid #d9dee3;
}

.form-check-label {
    font-weight: 500;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

.btn-lg {
    padding: 12px 24px;
    font-size: 1rem;
}
</style>
@endsection