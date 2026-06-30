import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
  root: 'resources/admin',
  envDir: '../..',
  plugins: [react(), tailwindcss()],
  build: {
    outDir: '../../public/admin',
    emptyOutDir: true,
  },
  server: {
    port: 5174,
    proxy: {
      '/api':    { target: 'http://127.0.0.1:8000', changeOrigin: true },
      '/images': { target: 'http://127.0.0.1:8000', changeOrigin: true },
    },
  },
})
