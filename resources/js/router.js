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
import PengajuanSK from './pages/PengajuanSK.vue'

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
  { path: '/laporan-pemusnahan', component: LaporanPemusnahan, name: 'laporanPemusnahan' },
  { path: '/pengajuan-sk', component: PengajuanSK, name: 'pengajuanSK' }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guard to verify user authentication status
router.beforeEach((to, from, next) => {
  const authUserStr = localStorage.getItem('auth_user')
  const isAuthenticated = authUserStr !== null

  if (to.name !== 'login' && !isAuthenticated) {
    // Redirect to login if trying to access any page without being authenticated
    next({ name: 'login' })
  } else if (to.name === 'login' && isAuthenticated) {
    // Redirect to appropriate dashboard if already authenticated
    const user = JSON.parse(authUserStr || '{}')
    if (user.role === 'Direktur') {
      next({ name: 'pengajuanSK' })
    } else {
      next({ name: 'home' })
    }
  } else if (isAuthenticated) {
    const user = JSON.parse(authUserStr || '{}')
    if (user.role === 'Direktur') {
      // Direktur can only access specific pages
      const allowedPaths = ['/pengajuan-sk', '/laporan-retensi', '/laporan-alih-media', '/laporan-pemusnahan', '/login']
      const isAllowed = allowedPaths.includes(to.path)
      if (!isAllowed) {
        next({ name: 'pengajuanSK' })
      } else {
        next()
      }
    } else {
      // Non-direktur rules
      if (to.path === '/log-aktivitas' || to.path === '/advanced-settings' || to.path === '/users') {
        if (user.role !== 'Administrator') {
          next({ name: 'home' })
        } else {
          next()
        }
      } else {
        next()
      }
    }
  } else {
    next()
  }
})

export default router
