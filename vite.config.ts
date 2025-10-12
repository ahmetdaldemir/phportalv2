import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': resolve(__dirname, 'resources/js'),
      '~': resolve(__dirname, 'resources/js')
    }
  },
  build: {
    outDir: 'public/build',
    assetsDir: 'assets',
    manifest: true,
    minify: 'terser',
    sourcemap: false,
    rollupOptions: {
      input: 'resources/js/app.ts',
      output: {
        manualChunks: {
          vendor: ['vue', 'vue-router'],
          utils: ['axios']
        }
      }
    }
  },
  server: {
    port: 5176,
    host: '0.0.0.0',
    hmr: {
      host: 'localhost'
    },
    proxy: {
      '/api': {
        target: 'http://localhost:80',
        changeOrigin: true,
        secure: false
      }
    }
  }
})
