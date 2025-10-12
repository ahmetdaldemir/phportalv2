<template>
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <h1>PHP Portal</h1>
        <p>Giriş yapın</p>
      </div>
      
      <form @submit.prevent="handleLogin" class="login-form">
        <div class="form-group">
          <label for="email">E-posta</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            class="form-control"
            :class="{ 'is-invalid': errors.email }"
            placeholder="E-posta adresinizi girin"
            required
          />
          <div v-if="errors.email" class="invalid-feedback">
            {{ errors.email }}
          </div>
        </div>

        <div class="form-group">
          <label for="password">Şifre</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            class="form-control"
            :class="{ 'is-invalid': errors.password }"
            placeholder="Şifrenizi girin"
            required
          />
          <div v-if="errors.password" class="invalid-feedback">
            {{ errors.password }}
          </div>
        </div>

        <div class="form-group">
          <button
            type="submit"
            class="btn btn-primary w-100"
            :disabled="authStore.isLoading"
          >
            <span v-if="authStore.isLoading" class="spinner-border spinner-border-sm me-2"></span>
            {{ authStore.isLoading ? 'Giriş yapılıyor...' : 'Giriş Yap' }}
          </button>
        </div>

        <div v-if="error" class="alert alert-danger">
          {{ error }}
        </div>
      </form>

      <div class="login-footer">
        <p class="text-muted">
          Test hesabı: admin@testcompany.com / password
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({
  email: '',
  password: ''
})

const errors = reactive({
  email: '',
  password: ''
})

const error = ref('')

const validateForm = () => {
  errors.email = ''
  errors.password = ''
  
  if (!form.email) {
    errors.email = 'E-posta adresi gereklidir'
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    errors.email = 'Geçerli bir e-posta adresi girin'
  }
  
  if (!form.password) {
    errors.password = 'Şifre gereklidir'
  } else if (form.password.length < 6) {
    errors.password = 'Şifre en az 6 karakter olmalıdır'
  }
  
  return !errors.email && !errors.password
}

const handleLogin = async () => {
  if (!validateForm()) {
    return
  }

  error.value = ''
  
  try {
    await authStore.login(form)
    router.push({ name: 'dashboard' })
  } catch (err: any) {
    if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else if (err.response?.data?.errors) {
      // Handle validation errors
      const errors = err.response.data.errors
      if (errors.email) {
        errors.email = errors.email[0]
      }
      if (errors.password) {
        errors.password = errors.password[0]
      }
    } else {
      error.value = 'Giriş yapılırken bir hata oluştu'
    }
  }
}
</script>

<style scoped>
.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 20px;
}

.login-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  padding: 40px;
  width: 100%;
  max-width: 400px;
}

.login-header {
  text-align: center;
  margin-bottom: 30px;
}

.login-header h1 {
  color: #333;
  font-size: 28px;
  font-weight: 600;
  margin-bottom: 8px;
}

.login-header p {
  color: #666;
  font-size: 16px;
  margin: 0;
}

.login-form {
  margin-bottom: 20px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  color: #333;
  font-weight: 500;
}

.form-control {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e1e5e9;
  border-radius: 8px;
  font-size: 16px;
  transition: border-color 0.3s ease;
}

.form-control:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-control.is-invalid {
  border-color: #dc3545;
}

.invalid-feedback {
  display: block;
  color: #dc3545;
  font-size: 14px;
  margin-top: 5px;
}

.btn {
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-primary {
  background: #667eea;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #5a6fd8;
  transform: translateY(-1px);
}

.btn-primary:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.alert {
  padding: 12px 16px;
  border-radius: 8px;
  margin-top: 20px;
}

.alert-danger {
  background: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

.login-footer {
  text-align: center;
  padding-top: 20px;
  border-top: 1px solid #e1e5e9;
}

.text-muted {
  color: #6c757d;
  font-size: 14px;
  margin: 0;
}
</style>
