@extends('layouts.admin')

@section('custom-css')
    <link rel="stylesheet" href="{{asset('assets/css/list-page-base.css')}}">
@endsection

@section('content')
    <div id="users-app" class="container-xxl flex-grow-1 container-p-y">
        <!-- Professional Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1">
                        <i class="bx bx-user me-2"></i>Kullanıcı Yönetimi
                    </h4>
                    <p class="mb-0 opacity-75">Sistem kullanıcılarını yönetin</p>
                </div>
                
                <a href="{{route('user.create')}}" class="btn btn-light btn-lg shadow-sm">
                    <i class="bx bx-plus me-2"></i>Yeni Kullanıcı Ekle
                </a>
            </div>
        </div>

        <!-- Professional Filter Section with Base CSS -->
        <div class="professional-card mb-4">
            <div class="professional-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-filter me-2"></i>Arama Filtreleri
                    </h5>
                </div>
            </div>
            
            <div class="card-body">
                <form @submit.prevent="searchUsers" class="compact-filter-form">
                    <div class="row g-2">
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="compact-filter-group">
                                <label class="compact-label">
                                    <i class="bx bx-search"></i>Arama
                                </label>
                                <input type="text" 
                                       v-model="searchForm.name" 
                                       class="form-control compact-input" 
                                       placeholder="İsim, email veya şube ara...">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-6">
                            <div class="compact-filter-group">
                                <label class="compact-label">
                                    <i class="bx bx-check-circle"></i>Durum
                                </label>
                                <select v-model="searchForm.status" 
                                        class="form-select compact-select">
                                    <option value="">Tümü</option>
                                    <option value="1">Aktif</option>
                                    <option value="0">Pasif</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6">
                            <div class="compact-filter-group">
                                <label class="compact-label">
                                    <i class="bx bx-building"></i>Şube
                                </label>
                                <select v-model="searchForm.seller" 
                                        class="form-select compact-select">
                                    <option value="">Tüm Şubeler</option>
                                    <option v-for="seller in sellers" 
                                            :key="seller.id" 
                                            :value="seller.id">
                                        @{{ seller.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-12 col-sm-12">
                            <div class="compact-filter-group">
                                <label class="compact-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary compact-btn">
                                        <i class="bx bx-search me-1"></i>Ara
                                    </button>
                                    <button type="button" @click="resetSearch" class="btn btn-outline-secondary compact-btn">
                                        <i class="bx bx-refresh me-1"></i>Sıfırla
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Professional Data Table -->
        <div class="professional-card">
            <div class="professional-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-table me-2"></i>Kullanıcı Listesi
                    </h5>
                    <div class="d-flex gap-2">
                        <span class="badge bg-primary">@{{ filteredUsers.length }} Kullanıcı</span>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover professional-table">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 40px;">
                                <input type="checkbox" 
                                       v-model="selectAll" 
                                       @change="toggleSelectAll"
                                       class="form-check-input">
                            </th>
                            <th style="width: 200px;">Kullanıcı</th>
                            <th style="width: 180px;">Email</th>
                            <th style="width: 120px;">Şube</th>
                            <th style="width: 100px;">Yetki</th>
                            <th class="text-center" style="width: 80px;">Status</th>
                            <th class="text-center" style="width: 80px;">Pozisyon</th>
                            <th class="text-center" style="width: 80px;">Personel</th>
                            <th class="text-center" style="width: 80px;">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in filteredUsers" :key="user.id" 
                            :class="{'table-secondary': user.company_id != 1}">
                            <td class="text-center">
                                <input type="checkbox" 
                                       v-model="selectedUsers" 
                                       :value="user.id"
                                       class="form-check-input">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xs me-2">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            @{{ user.name.charAt(0).toUpperCase() }}
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 text-truncate" 
                                            style="max-width: 120px; cursor: pointer;" 
                                            :title="user.name"
                                            @click="openPasswordModal(user)"
                                            class="text-primary">
                                            @{{ user.name }}
                                        </h6>
                                        <small class="text-muted">ID: @{{ user.id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-info text-truncate d-inline-block" 
                                      style="max-width: 150px;" 
                                      :title="user.email">
                                    @{{ user.email }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-label-secondary text-truncate d-inline-block" 
                                      style="max-width: 100px;" 
                                      :title="user.seller?.name || 'SYSTEM'">
                                    @{{ user.seller?.name || 'SYSTEM' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-label-warning text-truncate d-inline-block" 
                                      style="max-width: 80px;" 
                                      :title="user.roles?.map(role => role.name).join(', ') || 'Rol Yok'">
                                    @{{ user.roles?.map(role => role.name).join(', ') || 'Rol Yok' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           :checked="user.is_status == 1"
                                           @change="updateUserStatus(user.id, user.is_status == 1 ? 0 : 1)"
                                           :disabled="loading.status">
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           :checked="user.position == 1"
                                           @change="updateUserField(user.id, user.position == 1 ? 0 : 1, 'position')"
                                           :disabled="loading.field">
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           :checked="user.personel == 1"
                                           @change="updateUserField(user.id, user.personel == 1 ? 0 : 1, 'personel')"
                                           :disabled="loading.field">
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                            data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" 
                                           :href="`{{route('user.edit', ['id' => ''])}}${user.id}`">
                                            <i class="bx bx-edit-alt me-1"></i> Düzenle
                                        </a>
                                        <a class="dropdown-item text-danger" 
                                           href="#" 
                                           @click.prevent="deleteUser(user.id)">
                                            <i class="bx bx-trash me-1"></i> Sil
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Professional Footer -->
            <div class="professional-footer" v-if="pagination">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Toplam @{{ pagination.total }} kayıttan @{{ pagination.from }}-@{{ pagination.to }} arası gösteriliyor
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item" :class="{disabled: pagination.current_page === 1}">
                                <a class="page-link" href="#" @click.prevent="loadPage(pagination.current_page - 1)">
                                    <i class="bx bx-chevron-left"></i>
                                </a>
                            </li>
                            <li v-for="page in visiblePages" :key="page" 
                                class="page-item" 
                                :class="{active: page === pagination.current_page}">
                                <a class="page-link" href="#" @click.prevent="loadPage(page)">
                                    @{{ page }}
                                </a>
                            </li>
                            <li class="page-item" :class="{disabled: pagination.current_page === pagination.last_page}">
                                <a class="page-link" href="#" @click.prevent="loadPage(pagination.current_page + 1)">
                                    <i class="bx bx-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
 

    <!-- Password Change Modal -->
    <div class="modal fade" id="passwordModal" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title">
                        <i class="bx bx-key me-2"></i>Şifre Değiştir
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="changePassword">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bx bx-user me-1"></i>Kullanıcı
                            </label>
                            <input type="text" 
                                   :value="passwordForm.userName" 
                                   class="form-control" 
                                   readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bx bx-envelope me-1"></i>Email
                            </label>
                            <input type="email" 
                                   :value="passwordForm.userEmail" 
                                   class="form-control" 
                                   readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bx bx-lock me-1"></i>Yeni Şifre
                            </label>
                            <input type="password" 
                                   v-model="passwordForm.newPassword" 
                                   class="form-control" 
                                   placeholder="Yeni şifre giriniz..."
                                   required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bx bx-lock-alt me-1"></i>Şifre Tekrar
                            </label>
                            <input type="password" 
                                   v-model="passwordForm.confirmPassword" 
                                   class="form-control" 
                                   placeholder="Şifreyi tekrar giriniz..."
                                   required>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bx bx-info-circle me-2"></i>
                            <strong>Bilgi:</strong> Şifre en az 8 karakter olmalıdır.
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>İptal
                    </button>
                    <button type="button" 
                            @click="changePassword"
                            class="btn btn-primary"
                            :disabled="loading.password">
                        <i class="bx bx-save me-1"></i>
                        <span v-if="loading.password">Kaydediliyor...</span>
                        <span v-else>Şifre Değiştir</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
    <!-- Vue.js CDN -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    
    <script>
        const { createApp } = Vue;

        const app = createApp({
            data() {
                return {
                    // Users data
                    users: @json($users),
                    sellers: [],
                    
                    // Search and filter
                    searchForm: {
                        name: '',
                        status: '',
                        seller: ''
                    },
                    
                    // Selection
                    selectedUsers: [],
                    selectAll: false,
                    
                    // Loading states
                    loading: {
                        status: false,
                        field: false,
                        users: false,
                        password: false
                    },
                    
                    // Pagination
                    pagination: null,
                    
                    // Password change form
                    passwordForm: {
                        userId: null,
                        userName: '',
                        userEmail: '',
                        newPassword: '',
                        confirmPassword: ''
                    }
                }
            },
            
            computed: {
                filteredUsers() {
                    let filtered = this.users;
                    
                    if (this.searchForm.name) {
                        const searchTerm = this.searchForm.name.toLowerCase();
                        filtered = filtered.filter(user => 
                            user.name.toLowerCase().includes(searchTerm) ||
                            user.email.toLowerCase().includes(searchTerm) ||
                            (user.seller?.name && user.seller.name.toLowerCase().includes(searchTerm))
                        );
                    }
                    
                    if (this.searchForm.status !== '') {
                        filtered = filtered.filter(user => user.is_status == this.searchForm.status);
                    }
                    
                    if (this.searchForm.seller) {
                        filtered = filtered.filter(user => user.seller_id == this.searchForm.seller);
                    }
                    
                    return filtered;
                },
                
                visiblePages() {
                    if (!this.pagination) return [];
                    
                    const current = this.pagination.current_page;
                    const last = this.pagination.last_page;
                    const pages = [];
                    
                    for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
                        pages.push(i);
                    }
                    
                    return pages;
                }
            },
            
            methods: {
                // Format date
                formatDate(date) {
                    return new Date(date).toLocaleDateString('tr-TR');
                },
                
                // Search functions
                searchUsers() {
                    // Real-time search - no API call needed since we have all data
                },
                
                resetSearch() {
                    this.searchForm = {
                        name: '',
                        status: '',
                        seller: ''
                    };
                },
                
                // Selection functions
                toggleSelectAll() {
                    if (this.selectAll) {
                        this.selectedUsers = this.filteredUsers.map(user => user.id);
                    } else {
                        this.selectedUsers = [];
                    }
                },
                
                // User actions
                async updateUserStatus(userId, newStatus) {
                    this.loading.status = true;
                    
                    try {
                        const response = await axios.post(`/user/update`, {
                            id: userId,
                            is_status: newStatus,
                            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        });
                        
                        // Update local data
                        const user = this.users.find(u => u.id === userId);
                        if (user) {
                            user.is_status = newStatus;
                        }
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Başarılı',
                            text: 'Kullanıcı durumu güncellendi',
                            customClass: {
                                confirmButton: "btn btn-success"
                            }
                        });
                        
                    } catch (error) {
                        console.error('Status update error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata',
                            text: 'Durum güncellenirken bir hata oluştu',
                            customClass: {
                                confirmButton: "btn btn-danger"
                            }
                        });
                    } finally {
                        this.loading.status = false;
                    }
                },
                
                async updateUserField(userId, newValue, field) {
                    this.loading.field = true;
                    
                    try {
                        const response = await axios.post(`/user/fieldUpdate`, {
                            id: userId,
                            [field]: newValue,
                            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        });
                        
                        // Update local data
                        const user = this.users.find(u => u.id === userId);
                        if (user) {
                            user[field] = newValue;
                        }
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Başarılı',
                            text: 'Kullanıcı bilgisi güncellendi',
                            customClass: {
                                confirmButton: "btn btn-success"
                            }
                        });
                        
                    } catch (error) {
                        console.error('Field update error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata',
                            text: 'Bilgi güncellenirken bir hata oluştu',
                            customClass: {
                                confirmButton: "btn btn-danger"
                            }
                        });
                    } finally {
                        this.loading.field = false;
                    }
                },
                
                async deleteUser(userId) {
                    const result = await Swal.fire({
                        title: 'Emin misiniz?',
                        text: 'Bu kullanıcıyı silmek istediğinizden emin misiniz?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Evet, Sil!',
                        cancelButtonText: 'İptal'
                    });
                    
                    if (result.isConfirmed) {
                        try {
                            const response = await axios.get(`/user/delete?id=${userId}`);
                            
                            // Remove from local data
                            this.users = this.users.filter(u => u.id !== userId);
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Silindi!',
                                text: 'Kullanıcı başarıyla silindi',
                                customClass: {
                                    confirmButton: "btn btn-success"
                                }
                            });
                            
                        } catch (error) {
                            console.error('Delete error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Hata',
                                text: 'Kullanıcı silinirken bir hata oluştu',
                                customClass: {
                                    confirmButton: "btn btn-danger"
                                }
                            });
                        }
                    }
                },
                
                // Load sellers
                async loadSellers() {
                    try {
                        const response = await axios.get('/api/common/sellers');
                        this.sellers = response.data;
                    } catch (error) {
                        console.error('Error loading sellers:', error);
                    }
                },
                
                // Pagination
                loadPage(page) {
                    if (page < 1 || page > this.pagination.last_page) return;
                    // Implement pagination if needed
                },
                
                // Password change functions
                openPasswordModal(user) {
                    console.log('Opening password modal for user:', user);
                    
                    // Use Vue.set or direct assignment for reactivity
                    this.passwordForm.userId = user.id;
                    this.passwordForm.userName = user.name;
                    this.passwordForm.userEmail = user.email;
                    this.passwordForm.newPassword = '';
                    this.passwordForm.confirmPassword = '';
                    
                    console.log('Password form set to:', this.passwordForm);
                    
                    // Wait for Vue to update the DOM
                    this.$nextTick(() => {
                        const modal = new bootstrap.Modal(document.getElementById('passwordModal'));
                        modal.show();
                    });
                },
                
                async changePassword() {
                    // Validation
                    if (!this.passwordForm.newPassword) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata',
                            text: 'Yeni şifre giriniz',
                            customClass: {
                                confirmButton: "btn btn-danger"
                            }
                        });
                        return;
                    }
                    
                    if (this.passwordForm.newPassword.length < 8) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata',
                            text: 'Şifre en az 8 karakter olmalıdır',
                            customClass: {
                                confirmButton: "btn btn-danger"
                            }
                        });
                        return;
                    }
                    
                    if (this.passwordForm.newPassword !== this.passwordForm.confirmPassword) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata',
                            text: 'Şifreler eşleşmiyor',
                            customClass: {
                                confirmButton: "btn btn-danger"
                            }
                        });
                        return;
                    }
                    
                    this.loading.password = true;
                    
                    try {
                        const response = await axios.post('/user/change-password', {
                            user_id: this.passwordForm.userId,
                            new_password: this.passwordForm.newPassword,
                            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        });
                        
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('passwordModal'));
                        modal.hide();
                        
                        // Reset form
                        this.passwordForm = {
                            userId: null,
                            userName: '',
                            userEmail: '',
                            newPassword: '',
                            confirmPassword: ''
                        };
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Başarılı',
                            text: 'Şifre başarıyla değiştirildi',
                            customClass: {
                                confirmButton: "btn btn-success"
                            }
                        });
                        
                    } catch (error) {
                        console.error('Password change error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata',
                            text: error.response?.data?.message || 'Şifre değiştirilirken bir hata oluştu',
                            customClass: {
                                confirmButton: "btn btn-danger"
                            }
                        });
                    } finally {
                        this.loading.password = false;
                    }
                }
            },
            
            mounted() {
                this.loadSellers();
            }
        }).mount('#users-app');
    </script>
@endsection
