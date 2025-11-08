<div class="modal fade" id="editUser" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-simple modal-edit-user">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body" id="customer-modal-app">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3>
                        <i class="bx bx-user-plus me-2"></i>
                        <span v-if="customer.id">Müşteri Düzenle</span>
                        <span v-else>Yeni Müşteri Ekle</span>
                    </h3>
                </div>
                <form @submit.prevent="saveCustomer" id="customerForm">
                    <input type="hidden" v-model="customer.id"/>
                    <div class="row">
                        <!-- İsim -->
                        <div class="mb-3 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-user me-1"></i>İsim *
                            </label>
                            <input
                                v-model="customer.firstname"
                                class="form-control"
                                type="text"
                                required
                                placeholder="İsim giriniz"
                            />
                        </div>
                        
                        <!-- Soyisim -->
                        <div class="mb-3 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-user me-1"></i>Soyisim *
                            </label>
                            <input
                                v-model="customer.lastname"
                                class="form-control"
                                type="text"
                                required
                                placeholder="Soyisim giriniz"
                            />
                        </div>
                        
                        <!-- Telefon -->
                        <div class="mb-3 col-md-12">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-phone me-1"></i>Telefon *
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bx bx-phone"></i> +90
                                </span>
                                <input
                                    v-model="customer.phone1"
                                    type="tel"
                                    class="form-control"
                                    placeholder="5xx xxx xx xx"
                                    required
                                />
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="mb-3 col-md-12">
                            <label class="form-label fw-semibold">
                                <i class="bx bx-envelope me-1"></i>E-mail
                            </label>
                            <input
                                v-model="customer.email"
                                type="email"
                                class="form-control"
                                placeholder="ornek@email.com"
                            />
                        </div>

                        <!-- Tip (Hidden by default, can be shown if needed) -->
                        <input type="hidden" v-model="customer.type" value="customer"/>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button 
                            type="button" 
                            class="btn btn-outline-secondary" 
                            data-bs-dismiss="modal">
                            <i class="bx bx-x me-1"></i>İptal
                        </button>
                        <button 
                            type="submit" 
                            class="btn btn-primary"
                            :disabled="saving">
                            <span v-if="saving">
                                <span class="spinner-border spinner-border-sm me-2"></span>
                                Kaydediliyor...
                            </span>
                            <span v-else>
                                <i class="bx bx-save me-1"></i>
                                Kaydet
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Customer Modal Vue App
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Vue === 'undefined') {
        console.error('Vue.js is not loaded. Customer modal will not work.');
        return;
    }

    const { createApp } = Vue;

    window.CustomerModalApp = createApp({
        data() {
            return {
                customer: {
                    id: null,
                    type: 'customer',
                    firstname: '',
                    lastname: '',
                    phone1: '',
                    email: ''
                },
                saving: false
            }
        },
        computed: {
            fullName() {
                return `${this.customer.firstname} ${this.customer.lastname}`.trim();
            }
        },
        methods: {
            async saveCustomer() {
                if (!this.customer.firstname || !this.customer.lastname || !this.customer.phone1) {
                    alert('Lütfen zorunlu alanları doldurunuz! (İsim, Soyisim, Telefon)');
                    return;
                }
                
                this.saving = true;
                
                try {
                    // Prepare data
                    const customerData = {
                        ...this.customer,
                        fullname: this.fullName,
                        firstname: this.customer.firstname,
                        lastname: this.customer.lastname
                    };
                    
                    const response = await fetch('/custom_customerstore', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                        },
                        body: JSON.stringify(customerData)
                    });
                    
                    const result = await response.json();
                    
                    // Check if duplicate found (409 Conflict)
                    if (response.status === 409 && result.warning) {
                        const existingCustomer = result.existing_customer;
                        const useExisting = confirm(
                            `⚠️ ${result.message}\n\n` +
                            `Mevcut Müşteri Bilgileri:\n` +
                            `Ad Soyad: ${existingCustomer.fullname}\n` +
                            `Telefon: ${existingCustomer.phone1}\n` +
                            `Tip: ${existingCustomer.type}\n\n` +
                            `Bu müşteriyi kullanmak ister misiniz?`
                        );
                        
                        if (useExisting) {
                            // Emit event with existing customer
                            window.dispatchEvent(new CustomEvent('customerSaved', {
                                detail: existingCustomer
                            }));
                            
                            // Close modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('editUser'));
                            modal.hide();
                            
                            // Reset form
                            this.resetForm();
                            
                            if (window.Swal) {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Mevcut Müşteri Seçildi',
                                    text: existingCustomer.fullname + ' müşterisi seçildi.',
                                    timer: 2000
                                });
                            }
                        }
                        return;
                    }
                    
                    if (response.ok && result.success) {
                        // Emit event for parent components
                        window.dispatchEvent(new CustomEvent('customerSaved', {
                            detail: result.customer || result
                        }));
                        
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editUser'));
                        modal.hide();
                        
                        // Reset form
                        this.resetForm();
                        
                        // Show success message
                        if (window.Swal) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Başarılı!',
                                text: result.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else if (window.$toast) {
                            window.$toast.success(result.message);
                        } else {
                            alert(result.message);
                        }
                    } else {
                        throw new Error(result.message || 'Kaydetme işlemi başarısız');
                    }
                } catch (error) {
                    console.error('Error saving customer:', error);
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: error.message || 'Müşteri kaydedilirken hata oluştu!'
                        });
                    } else if (window.$toast) {
                        window.$toast.error(error.message || 'Müşteri kaydedilirken hata oluştu!');
                    } else {
                        alert(error.message || 'Müşteri kaydedilirken hata oluştu!');
                    }
                } finally {
                    this.saving = false;
                }
            },
            
            resetForm() {
                this.customer = {
                    id: null,
                    type: 'customer',
                    firstname: '',
                    lastname: '',
                    phone1: '',
                    email: ''
                };
            },
            
            editCustomer(customerData) {
                this.customer = { ...customerData };
            }
        },
        
        mounted() {
            // Listen for edit customer events
            window.addEventListener('editCustomer', (event) => {
                this.editCustomer(event.detail);
            });
        }
    }).mount('#customer-modal-app');
});
</script>

@section('custom-css')
<style>
.modal-simple .btn-close {
    position: absolute;
    top: -2rem;
    right: -2rem;
    background-color: #fff;
    border-radius: 0.5rem;
    opacity: 1;
    padding: 0.635rem;
    box-shadow: 0 0.125rem 0.25rem rgb(161 172 184 / 40%);
    transition: all .23s ease .1s;
}

.modal-simple .modal-content {
    border-radius: 1rem;
}

.form-label.fw-semibold {
    font-weight: 600;
    color: #566a7f;
    margin-bottom: 0.5rem;
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #d0d5dd;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

.modal-body h3 {
    color: #566a7f;
    font-weight: 600;
}

.form-control:focus {
    border-color: #696cff;
    box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.1);
}

.btn-primary {
    background-color: #696cff;
    border-color: #696cff;
}

.btn-primary:hover:not(:disabled) {
    background-color: #5f61e6;
    border-color: #5f61e6;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(105, 108, 255, 0.3);
}

.btn-outline-secondary:hover {
    transform: translateY(-1px);
}

.form-control, .input-group-text {
    border-radius: 0.375rem;
}
</style>
@endsection