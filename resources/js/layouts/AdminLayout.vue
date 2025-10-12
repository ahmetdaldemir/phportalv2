<template>
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <!-- Sidebar -->
      <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
        <div class="app-brand demo">
          <router-link to="/" class="app-brand-link">
            <span class="app-brand-logo demo">
              <Logo />
            </span>
          </router-link>
          
          <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
          </a>
        </div>

        <div class="menu-inner-shadow"></div>

        <ul class="menu-inner py-1" style="overflow: auto;">
          <!-- Dashboard -->
          <li class="menu-item" :class="{ active: route.name === 'dashboard' }">
            <router-link to="/" class="menu-link">
              <i class="menu-icon tf-icons bx bx-home-circle"></i>
              <div data-i18n="Analytics">Anasayfa</div>
            </router-link>
          </li>

          <!-- Categories -->
          <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Kategoriler</span>
          </li>
          <li class="menu-item">
            <router-link to="/phones" class="menu-link">
              <i class="menu-icon tf-icons bx bx-collection"></i>
              <div data-i18n="Basic">Telefon</div>
            </router-link>
          </li>

          <!-- Stock Operations -->
          <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Stok İşlemleri</span>
          </li>
          <li class="menu-item" :class="{ active: route.name === 'stockcards' }">
            <router-link to="/stockcards" class="menu-link">
              <i class="menu-icon tf-icons bx bx-collection"></i>
              <div data-i18n="Basic">Stok Kartları</div>
            </router-link>
          </li>
          <li class="menu-item" :class="{ active: route.name === 'transfers' }">
            <router-link to="/transfers" class="menu-link">
              <i class="menu-icon tf-icons bx bx-collection"></i>
              <div data-i18n="Basic">Sevk</div>
            </router-link>
          </li>
          <li class="menu-item" :class="{ active: route.name === 'sales' }">
            <router-link to="/sales" class="menu-link">
              <i class="menu-icon tf-icons bx bx-collection"></i>
              <div data-i18n="Basic">Satış</div>
            </router-link>
          </li>
        </ul>
      </aside>

      <!-- Main Content -->
      <div class="layout-page">
        <!-- Navbar -->
        <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme">
          <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
              <i class="bx bx-menu bx-sm"></i>
            </a>
          </div>

          <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
            <!-- Search -->
            <div class="navbar-nav align-items-center">
              <div class="nav-item d-flex align-items-center">
                <i class="bx bx-search fs-4 lh-0"></i>
                <input
                  type="text"
                  class="form-control border-0 shadow-none"
                  placeholder="Arama..."
                  aria-label="Arama..."
                />
              </div>
            </div>

            <ul class="navbar-nav flex-row align-items-center ms-auto">
              <!-- User -->
              <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                  <div class="avatar avatar-online">
                    <i class="bx bx-user w-px-40 h-auto rounded-circle" style="font-size: 35px; background: blueviolet; color: #fff; text-align: center;"></i>
                  </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <a class="dropdown-item" href="#">
                      <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                          <div class="avatar avatar-online">
                            <i class="bx bx-user w-px-40 h-auto rounded-circle" style="font-size: 35px; background: blueviolet; color: #fff; text-align: center;"></i>
                          </div>
                        </div>
                        <div class="flex-grow-1">
                          <span class="fw-semibold d-block">{{ authStore.currentUser?.name || 'User' }}</span>
                          <small class="text-muted">Admin</small>
                        </div>
                      </div>
                    </a>
                  </li>
                  <li>
                    <div class="dropdown-divider"></div>
                  </li>
                  <li>
                    <a class="dropdown-item" href="#" @click="handleLogout">
                      <i class="bx bx-power-off me-2"></i>
                      <span class="align-middle">Log Out</span>
                    </a>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </nav>

        <!-- Content -->
        <div class="content-wrapper">
          <router-view />
          <Footer />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import Logo from '@/components/Logo.vue'
import Footer from '@/components/Footer.vue'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const handleLogout = async () => {
  try {
    await authStore.logout()
    router.push({ name: 'login' })
  } catch (error) {
    console.error('Logout error:', error)
  }
}
</script>

<style scoped>
.layout-wrapper {
  min-height: 100vh;
}

.layout-container {
  display: flex;
}

.layout-menu {
  width: 260px;
  flex-shrink: 0;
}

.layout-page {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.content-wrapper {
  flex: 1;
  padding: 1.5rem;
}
</style>
