<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <div class="w-64 bg-blue-900 text-white overflow-y-auto">
      <!-- Header -->
      <div class="p-6 border-b border-blue-800">
        <h2 class="text-lg font-bold mb-1">RSU Kaliwates</h2>
        <p class="text-xs text-blue-100 leading-tight">Sistem Retensi & Alih Media</p>
        <p class="text-xs text-blue-100">Rekam Medis</p>
      </div>

      <!-- User Info -->
      <div class="p-4 border-b border-blue-800 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-white/30 flex items-center justify-center flex-shrink-0">
          <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="min-w-0">
          <p class="font-bold text-sm">Administrator</p>
          <p class="text-xs text-blue-100">admin</p>
        </div>
      </div>

      <!-- Menu Items -->
      <nav class="flex-1 px-2 py-6 space-y-1">
        <!-- Dashboard -->
        <router-link
          to="/"
          :class="isActive('/') ? 'bg-blue-700' : 'hover:bg-blue-800'"
          class="flex items-center gap-4 px-4 py-3 rounded-lg text-white text-sm font-medium transition-colors duration-200"
        >
          <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4z" />
            <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h6v6H4a1 1 0 01-1-1v-4zm8 0a1 1 0 011-1h4v6h-4a1 1 0 01-1-1v-4z" clip-rule="evenodd" />
          </svg>
          <span>Dashboard</span>
        </router-link>

        <!-- Data Master -->
        <div>
          <button
            @click="toggleMenu('dataMaster')"
            :class="isMenuOpen('dataMaster') ? 'bg-blue-700' : 'hover:bg-blue-800'"
            class="w-full flex items-center gap-4 px-4 py-3 rounded-lg text-white text-sm font-medium transition-colors duration-200"
          >
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4z" />
              <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6z" clip-rule="evenodd" />
            </svg>
            <span class="flex-1 text-left">Data Master</span>
            <svg 
              class="w-4 h-4 flex-shrink-0 transition-transform" 
              fill="currentColor" 
              viewBox="0 0 20 20"
              :style="{ transform: isMenuOpen('dataMaster') ? 'rotate(180deg)' : 'rotate(0deg)' }"
            >
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </button>
          <div v-if="isMenuOpen('dataMaster')" class="space-y-0 overflow-hidden">
            <router-link 
              to="/pasien" 
              class="block px-12 py-2 text-sm text-blue-100 hover:text-white hover:bg-blue-700 transition-colors duration-200"
            >
              Data Pasien
            </router-link>
            <router-link 
              to="/kasus" 
              class="block px-12 py-2 text-sm text-blue-100 hover:text-white hover:bg-blue-700 transition-colors duration-200"
            >
              Data Kasus
            </router-link>
            <router-link 
              to="/user" 
              class="block px-12 py-2 text-sm text-blue-100 hover:text-white hover:bg-blue-700 transition-colors duration-200"
            >
              Data User
            </router-link>
          </div>
        </div>

        <!-- Transaksi -->
        <div>
          <button
            @click="toggleMenu('transaksi')"
            :class="isMenuOpen('transaksi') ? 'bg-blue-700' : 'hover:bg-blue-800'"
            class="w-full flex items-center gap-4 px-4 py-3 rounded-lg text-white text-sm font-medium transition-colors duration-200"
          >
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4z" />
              <path fill-rule="evenodd" d="M2 8a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V8zm12-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
            </svg>
            <span class="flex-1 text-left">Transaksi</span>
            <svg 
              class="w-4 h-4 flex-shrink-0 transition-transform" 
              fill="currentColor" 
              viewBox="0 0 20 20"
              :style="{ transform: isMenuOpen('transaksi') ? 'rotate(180deg)' : 'rotate(0deg)' }"
            >
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </button>
          <div v-if="isMenuOpen('transaksi')" class="space-y-0 overflow-hidden">
            <router-link 
              to="/retensi" 
              class="block px-12 py-2 text-sm text-blue-100 hover:text-white hover:bg-blue-700 transition-colors duration-200"
            >
              Retensi
            </router-link>
            <router-link 
              to="/alih-media" 
              class="block px-12 py-2 text-sm text-blue-100 hover:text-white hover:bg-blue-700 transition-colors duration-200"
            >
              Alih Media
            </router-link>
            <router-link 
              to="/pemusnahan" 
              class="block px-12 py-2 text-sm text-blue-100 hover:text-white hover:bg-blue-700 transition-colors duration-200"
            >
              Pemusnahan
            </router-link>
            <router-link 
              to="/log-aktivitas" 
              class="block px-12 py-2 text-sm text-blue-100 hover:text-white hover:bg-blue-700 transition-colors duration-200"
            >
              Log Aktivitas
            </router-link>
          </div>
        </div>

        <!-- Laporan -->
        <div>
          <button
            @click="toggleMenu('laporan')"
            :class="isMenuOpen('laporan') ? 'bg-blue-700' : 'hover:bg-blue-800'"
            class="w-full flex items-center gap-4 px-4 py-3 rounded-lg text-white text-sm font-medium transition-colors duration-200"
          >
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4z" />
              <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6z" clip-rule="evenodd" />
            </svg>
            <span class="flex-1 text-left">Laporan</span>
            <svg 
              class="w-4 h-4 flex-shrink-0 transition-transform" 
              fill="currentColor" 
              viewBox="0 0 20 20"
              :style="{ transform: isMenuOpen('laporan') ? 'rotate(180deg)' : 'rotate(0deg)' }"
            >
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </button>
          <div v-if="isMenuOpen('laporan')" class="space-y-0 overflow-hidden">
            <router-link 
              to="/laporan-retensi" 
              class="block px-12 py-2 text-sm text-blue-100 hover:text-white hover:bg-blue-700 transition-colors duration-200"
            >
              Laporan Retensi
            </router-link>
            <router-link 
              to="/laporan-alih-media" 
              class="block px-12 py-2 text-sm text-blue-100 hover:text-white hover:bg-blue-700 transition-colors duration-200"
            >
              Laporan Alih Media
            </router-link>
            <router-link 
              to="/laporan-pemusnahan" 
              class="block px-12 py-2 text-sm text-blue-100 hover:text-white hover:bg-blue-700 transition-colors duration-200"
            >
              Laporan Pemusnahan
            </router-link>
          </div>
        </div>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 overflow-y-auto">
      <router-view />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const openMenus = ref({
  dataMaster: true,
  transaksi: false,
  laporan: false,
})

const toggleMenu = (menu) => {
  openMenus.value[menu] = !openMenus.value[menu]
}

const isMenuOpen = (menu) => {
  return openMenus.value[menu]
}

const isActive = (path) => {
  return route.path === path || route.name === path
}
</script>

<style scoped>
svg {
  transition: transform 0.3s ease-out;
}

/* Smooth dropdown animation */
div[v-if] {
  animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
