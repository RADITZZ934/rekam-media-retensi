import { createRouter, createWebHistory } from 'vue-router'
import UploadPage from './pages/UploadPage.vue'
import DataPasien from './pages/DataPasien.vue'
import DataKasus from './pages/DataKasus.vue'

const routes = [
  { path: '/', component: UploadPage, name: 'home' },
  { path: '/pasien', component: DataPasien, name: 'dataPasien' },
  { path: '/kasus', component: DataKasus, name: 'dataKasus' }
]

export default createRouter({
  history: createWebHistory(),
  routes
})
