<template>
  <header class="bg-white border-b border-gray-100 px-8 py-4 flex items-center justify-between sticky top-0 z-10 shadow-sm">
    <h1 class="text-2xl font-bold text-[#2b3c5a]">{{ pageTitle }}</h1>
    <div class="flex items-center gap-6">
      <!-- Notification -->
      <button class="relative text-gray-500 hover:text-gray-700 transition">
        <Bell class="w-6 h-6" />
        <span class="absolute top-0 right-0 max-w-fit flex items-center justify-center p-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
      </button>
      <!-- Profile Dropdown Container -->
      <div class="relative">
        <button 
          @click="showDropdown = !showDropdown" 
          class="flex items-center gap-3 hover:opacity-85 transition focus:outline-none cursor-pointer"
        >
          <div class="text-right flex flex-col justify-center">
            <span class="text-sm font-bold text-gray-800 leading-none mb-1 text-left">{{ activeUser.nama_lengkap }}</span>
            <span class="text-xs text-gray-500 text-left">{{ activeUser.role }}</span>
          </div>
          <div class="w-10 h-10 bg-gradient-to-tr from-blue-600 to-indigo-500 rounded-full flex items-center justify-center text-white font-bold shadow-md">
            {{ userInitial }}
          </div>
          <ArrowDown 
            class="w-4 h-4 text-gray-400 transition-transform duration-200" 
            :class="{ 'rotate-180': showDropdown }"
          />
        </button>

        <!-- Backdrop to close dropdown -->
        <div 
          v-if="showDropdown" 
          @click="showDropdown = false" 
          class="fixed inset-0 z-10 cursor-default"
        ></div>

        <!-- White Dropdown Box -->
        <div 
          v-if="showDropdown" 
          class="absolute right-0 mt-2 w-48 bg-white border border-gray-100 rounded-xl shadow-xl py-2 z-20"
        >
          <!-- Logout Button -->
          <button 
            @click="handleLogout" 
            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-gray-600 hover:text-red-600 hover:bg-red-50/50 transition cursor-pointer text-left"
          >
            <SwitchButton class="w-5 h-5" />
            Logout
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { showSuccessToast, showErrorToast } from '../utils/notification'

const route = useRoute()
const router = useRouter()
const showDropdown = ref(false)

const activeUser = ref({
  nama_lengkap: 'User',
  username: 'user',
  role: 'Staff'
})

const userInitial = computed(() => {
  const name = activeUser.value.nama_lengkap || 'User'
  return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2)
})

const pageTitle = computed(() => {
  const titles = {
    home: 'Dashboard',
    dataPasien: 'Data Pasien',
    previewPasien: 'Preview Pasien',
    dataKasus: 'Master Kasus',
    dataUser: 'Data User',
    dataRetensi: 'Data Retensi',
    alihMedia: 'Alih Media',
    pemusnahan: 'Data Pemusnahan',
    validasiOCR: 'Validasi OCR',
    logAktivitas: 'Log Aktivitas',
    laporanRetensi: 'Laporan Retensi',
    laporanAlihMedia: 'Laporan Alih Media',
    laporanPemusnahan: 'Laporan Pemusnahan'
  }
  return titles[route.name] || 'Sistem Rekam Medis'
})

const loadUser = () => {
  const stored = localStorage.getItem('auth_user')
  if (stored) {
    try {
      activeUser.value = JSON.parse(stored)
    } catch (e) {
      console.error(e)
    }
  }
}

const handleLogout = async () => {
  try {
    const response = await fetch('/api/logout', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
      }
    })
    
    if (response.ok) {
      localStorage.removeItem('auth_user')
      showSuccessToast('Sampai jumpa kembali!', 'Logout Berhasil')
      router.push('/login')
    } else {
      showErrorToast('Gagal menghubungi server untuk logout.')
    }
  } catch (err) {
    // Fallback: local session removal
    localStorage.removeItem('auth_user')
    router.push('/login')
  }
}

onMounted(() => {
  loadUser()
})
</script>
