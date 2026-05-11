<template>
  <header class="bg-white border-b border-gray-100 px-8 py-4 flex items-center justify-between sticky top-0 z-10 shadow-sm">
    <h1 class="text-2xl font-bold text-[#2b3c5a]">{{ pageTitle }}</h1>
    <div class="flex items-center gap-6">
      <!-- Notification -->
      <button class="relative text-gray-500 hover:text-gray-700 transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
        <span class="absolute top-0 right-0 max-w-fit flex items-center justify-center p-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
      </button>
      <!-- Profile -->
      <div class="flex items-center gap-3">
        <div class="text-right flex flex-col justify-center">
          <span class="text-sm font-bold text-gray-800 leading-none mb-1">Administrator</span>
          <span class="text-xs text-gray-500">admin</span>
        </div>
        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 shadow-inner">
          <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" /></svg>
        </div>
      </div>
      <!-- Logout -->
      <button class="flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-800 border-l border-gray-200 pl-6 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
        Logout
      </button>
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

const pageTitle = computed(() => {
  const titles = {
    home: 'Dashboard',
    dataPasien: 'Data Pasien',
    previewPasien: 'Preview Pasien',
    dataKasus: 'Master Kasus',
    dataUser: 'Data User',
    dataRetensi: 'Data Retensi',
    aliihMedia: 'Alih Media',
    pemusnahan: 'Data Pemusnahan',
    validasiOCR: 'Validasi OCR',
    logAktivitas: 'Log Aktivitas',
    laporanRetensi: 'Laporan Retensi',
    laporanAlihMedia: 'Laporan Alih Media',
    laporanPemusnahan: 'Laporan Pemusnahan'
  }
  return titles[route.name] || 'Sistem Rekam Medis'
})
</script>
