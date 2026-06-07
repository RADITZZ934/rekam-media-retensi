<template>
  <!-- Sidebar -->
  <div class="w-64 bg-blue-900 text-white overflow-y-auto flex flex-col h-screen">
    <!-- Header -->
    <div class="p-6 border-b border-blue-800">
      <h2 class="text-lg font-bold mb-1">RSU Kaliwates</h2>
      <p class="text-xs text-blue-100 leading-tight">Sistem Retensi & Alih Media</p>
      <p class="text-xs text-blue-100">Rekam Medis</p>
    </div>

    <!-- User Info (Advanced Settings Redirect) -->
    <router-link 
      to="/advanced-settings"
      class="p-4 border-b border-blue-800 flex items-center gap-4 hover:bg-blue-800 cursor-pointer transition-colors group"
      title="Klik untuk Pengaturan Lanjutan"
    >
      <div class="w-12 h-12 rounded-full bg-white/30 flex items-center justify-center flex-shrink-0 group-hover:bg-white/40 transition-colors">
        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
        </svg>
      </div>
      <div class="min-w-0">
        <p class="font-bold text-sm">{{ activeUser.nama_lengkap }}</p>
        <p class="text-xs text-blue-100 group-hover:text-white transition-colors">{{ activeUser.username }}</p>
      </div>
      <div class="ml-auto opacity-0 group-hover:opacity-100 transition-opacity">
        <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
      </div>
    </router-link>

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
        <transition name="dropdown">
          <div v-show="isMenuOpen('dataMaster')" class="space-y-0 overflow-hidden">
            <router-link 
              to="/pasien" 
              :class="isActive('/pasien') ? 'bg-blue-600 text-white border-l-4 border-white' : 'text-blue-100 hover:text-white hover:bg-blue-700 border-l-4 border-transparent'"
              class="block px-11 py-2 text-sm transition-all duration-200"
            >
              Data Pasien
            </router-link>
            <router-link 
              to="/kasus" 
              :class="isActive('/kasus') ? 'bg-blue-600 text-white border-l-4 border-white' : 'text-blue-100 hover:text-white hover:bg-blue-700 border-l-4 border-transparent'"
              class="block px-11 py-2 text-sm transition-all duration-200"
            >
              Data Kasus
            </router-link>
            <router-link 
              to="/users" 
              :class="isActive('/users') ? 'bg-blue-600 text-white border-l-4 border-white' : 'text-blue-100 hover:text-white hover:bg-blue-700 border-l-4 border-transparent'"
              class="block px-11 py-2 text-sm transition-all duration-200"
            >
              Data User
            </router-link>
          </div>
        </transition>
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
        <transition name="dropdown">
          <div v-show="isMenuOpen('transaksi')" class="space-y-0 overflow-hidden">
            <router-link 
              to="/retensi" 
              :class="isActive('/retensi') ? 'bg-blue-600 text-white border-l-4 border-white' : 'text-blue-100 hover:text-white hover:bg-blue-700 border-l-4 border-transparent'"
              class="block px-11 py-2 text-sm transition-all duration-200"
            >
              Retensi
            </router-link>
            <router-link 
              to="/alih-media" 
              :class="isActive('/alih-media') ? 'bg-blue-600 text-white border-l-4 border-white' : 'text-blue-100 hover:text-white hover:bg-blue-700 border-l-4 border-transparent'"
              class="block px-11 py-2 text-sm transition-all duration-200"
            >
              Alih Media
            </router-link>
            <router-link 
              to="/pemusnahan" 
              :class="isActive('/pemusnahan') ? 'bg-blue-600 text-white border-l-4 border-white' : 'text-blue-100 hover:text-white hover:bg-blue-700 border-l-4 border-transparent'"
              class="block px-11 py-2 text-sm transition-all duration-200"
            >
              Pemusnahan
            </router-link>
            <router-link 
              to="/log-aktivitas" 
              :class="isActive('/log-aktivitas') ? 'bg-blue-600 text-white border-l-4 border-white' : 'text-blue-100 hover:text-white hover:bg-blue-700 border-l-4 border-transparent'"
              class="block px-11 py-2 text-sm transition-all duration-200"
            >
              Log Aktivitas
            </router-link>
          </div>
        </transition>
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
        <transition name="dropdown">
          <div v-show="isMenuOpen('laporan')" class="space-y-0 overflow-hidden">
            <router-link 
              to="/laporan-retensi" 
              :class="isActive('/laporan-retensi') ? 'bg-blue-600 text-white border-l-4 border-white' : 'text-blue-100 hover:text-white hover:bg-blue-700 border-l-4 border-transparent'"
              class="block px-11 py-2 text-sm transition-all duration-200"
            >
              Laporan Retensi
            </router-link>
            <router-link 
              to="/laporan-alih-media" 
              :class="isActive('/laporan-alih-media') ? 'bg-blue-600 text-white border-l-4 border-white' : 'text-blue-100 hover:text-white hover:bg-blue-700 border-l-4 border-transparent'"
              class="block px-11 py-2 text-sm transition-all duration-200"
            >
              Laporan Alih Media
            </router-link>
            <router-link 
              to="/laporan-pemusnahan" 
              :class="isActive('/laporan-pemusnahan') ? 'bg-blue-600 text-white border-l-4 border-white' : 'text-blue-100 hover:text-white hover:bg-blue-700 border-l-4 border-transparent'"
              class="block px-11 py-2 text-sm transition-all duration-200"
            >
              Laporan Pemusnahan
            </router-link>
          </div>
        </transition>
      </div>

    </nav>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const openMenus = ref({
  dataMaster: true,
  transaksi: false,
  laporan: false,
})

const activeUser = ref({
  nama_lengkap: 'User',
  username: 'user',
  role: 'Staff'
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

const toggleMenu = (menu) => {
  openMenus.value[menu] = !openMenus.value[menu]
}

const isMenuOpen = (menu) => {
  return openMenus.value[menu]
}

const isActive = (path) => {
  return route.path === path || route.name === path
}

onMounted(() => {
  loadUser()
})
</script>

<style scoped>
svg {
  transition: transform 0.3s ease-out;
}

/* Smooth dropdown menu animation */
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
  max-height: 500px;
  overflow: hidden;
}

.dropdown-enter-from {
  opacity: 0;
  max-height: 0;
  transform: translateY(-8px);
}

.dropdown-leave-to {
  opacity: 0;
  max-height: 0;
  transform: translateY(-8px);
}

.dropdown-enter-to,
.dropdown-leave-from {
  opacity: 1;
  max-height: 500px;
  transform: translateY(0);
}

/* Active menu item indicator */
a {
  position: relative;
}

a.router-link-active {
  font-weight: 500;
}
</style>
