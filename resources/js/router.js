import { createRouter, createWebHistory } from 'vue-router'
import UploadPage from './pages/UploadPage.vue'
import DataPasien from './pages/DataPasien.vue'
import PreviewPasien from './pages/PreviewPasien.vue'
import DataKasus from './pages/DataKasus.vue'
import DataUser from './pages/DataUser.vue'
import DataRetensi from './pages/DataRetensi.vue'
import DataAlihMedia from './pages/AlihMedia.vue'
import ValidasiOCR from './pages/ValidasiOCR.vue'
import DataPemusnahan from './pages/DataPemusnahan.vue'
import LogAktivitas from './pages/LogAktivitas.vue'
import AdvancedSettings from './pages/AdvancedSettings.vue'
import LaporanRetensi from './pages/LaporanRetensi.vue'
import LaporanAlihMedia from './pages/LaporanAlihMedia.vue'
import LaporanPemusnahan from './pages/LaporanPemusnahan.vue'
import Login from './pages/Login.vue'

const routes = [
  { path: '/login', component: Login, name: 'login' },
  { path: '/', component: UploadPage, name: 'home' },
  { path: '/pasien', component: DataPasien, name: 'dataPasien' },
  { path: '/pasien/:no_rm', component: PreviewPasien, name: 'previewPasien' },
  { path: '/kasus', component: DataKasus, name: 'dataKasus' },
  { path: '/users', component: DataUser, name: 'dataUser' },
  { path: '/retensi', component: DataRetensi, name: 'dataRetensi' },
  { path: '/alih-media', component: DataAlihMedia, name: 'alihMedia' },
  { path: '/pemusnahan', component: DataPemusnahan, name: 'pemusnahan' },
  { path: '/validasi-ocr', component: ValidasiOCR, name: 'validasiOCR' },
  { path: '/log-aktivitas', component: LogAktivitas, name: 'logAktivitas' },
  { path: '/advanced-settings', component: AdvancedSettings, name: 'advancedSettings' },
  { path: '/laporan-retensi', component: LaporanRetensi, name: 'laporanRetensi' },
  { path: '/laporan-alih-media', component: LaporanAlihMedia, name: 'laporanAlihMedia' },
  { path: '/laporan-pemusnahan', component: LaporanPemusnahan, name: 'laporanPemusnahan' }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guard to verify user authentication status
router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('auth_user') !== null

  if (to.name !== 'login' && !isAuthenticated) {
    // Redirect to login if trying to access any page without being authenticated
    next({ name: 'login' })
  } else if (to.name === 'login' && isAuthenticated) {
    // Redirect to dashboard home if already authenticated and trying to access login page
    next({ name: 'home' })
  } else {
    next()
  }
})

export default router
