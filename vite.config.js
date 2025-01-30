import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react()],
  server: {
    proxy: {
      '/api': {
        target: 'https://cidadao.codemakersbr.com.br/api/',
        changeOrigin: true,
        secure: false, // Pode ser necessário dependendo da configuração do seu backend HTTPS
      },
    },
  },
})