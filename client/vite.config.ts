import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'

const devApiTarget = process.env.VITE_DEV_API_PROXY ?? 'http://localhost:8080'

export default defineConfig({
  plugins: [react(), tailwindcss()],
  server: {
    host: true,
    port: 5174,
    proxy: {
      '/api': {
        target: devApiTarget,
        changeOrigin: true,
        secure: false,
      },
      '/sanctum': {
        target: devApiTarget,
        changeOrigin: true,
        secure: false,
      },
      '/storage': {
        target: devApiTarget,
        changeOrigin: true,
        secure: false,
      },
    },
  },
})
