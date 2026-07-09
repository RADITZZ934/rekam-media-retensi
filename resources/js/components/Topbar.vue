<template>
  <header class="bg-white border-b border-gray-100 px-4 md:px-8 py-4 flex items-center justify-between sticky top-0 z-10 shadow-sm">
    <div class="flex items-center gap-3">
      <!-- Hamburger Menu for Mobile -->
      <button 
        @click="emit('toggle-sidebar')" 
        class="lg:hidden p-2 text-gray-500 hover:text-blue-600 hover:bg-gray-50 rounded-lg transition-colors focus:outline-none"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
      <h1 class="text-xl md:text-2xl font-bold text-[#2b3c5a]">{{ pageTitle }}</h1>
    </div>
    <div class="flex items-center gap-6">

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

const emit = defineEmits(['toggle-sidebar'])

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
    pemusnahan: 'Transaksi Pemusnahan',
    validasiOCR: 'Validasi OCR',
    logAktivitas: 'Log Aktivitas',
    laporanRetensi: 'Laporan Retensi',
    laporanAlihMedia: 'Laporan Alih Media',
    laporanPemusnahan: 'Laporan Pemusnahan',
    pengajuanSK: 'Pengajuan SK Pemusnahan'
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

const handleLogout = () => {
  window.dispatchEvent(new CustomEvent('trigger-logout'))
}

onMounted(() => {
  loadUser()
})
</script>
