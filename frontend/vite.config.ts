import tailwindcss from '@tailwindcss/vite'
import react from '@vitejs/plugin-react'
import { defineConfig } from 'vite'

// Un solo túnel de Cloudflare sirve el front y la API:
// /api y /storage se reenvían al backend de Laravel (puerto 8000).
const backendProxy = {
  '/api': {
    target: 'http://127.0.0.1:8000',
    changeOrigin: true,
  },
  '/storage': {
    target: 'http://127.0.0.1:8000',
    changeOrigin: true,
  },
}

// https://vite.dev/config/
export default defineConfig({
  plugins: [react(), tailwindcss()],
  server: {
    proxy: backendProxy,
    allowedHosts: true,
  },
  preview: {
    proxy: backendProxy,
    allowedHosts: true,
  },
})
