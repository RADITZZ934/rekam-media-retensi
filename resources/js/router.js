import { createRouter, createWebHistory } from 'vue-router'
import UploadPage from './pages/UploadPage.vue'
import DataPasien from './pages/DataPasien.vue'

const routes = [
  { path: '/', component: UploadPage, name: 'home' },
  { path: '/pasien', component: DataPasien, name: 'dataPasien' }
]

export default createRouter({
  history: createWebHistory(),
  routes
})
