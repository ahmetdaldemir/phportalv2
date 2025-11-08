import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './App.vue'

// Global CSS
import './assets/css/app.css'

// Create Vue app
const app = createApp(App)

// Use plugins
app.use(createPinia())
app.use(router)

// Mount app
app.mount('#app')

// Global properties
declare module '@vue/runtime-core' {
  interface ComponentCustomProperties {
    $http: any
  }
}
