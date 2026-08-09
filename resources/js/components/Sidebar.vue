<template>
  <div>
    <!-- Backdrop Overlay for Mobile -->
    <div 
      v-if="isOpen" 
      @click="emit('close')" 
      class="fixed inset-0 bg-black/50 z-30 lg:hidden"
    ></div>

    <!-- Sidebar main drawer -->
    <div 
      :class="[
        isOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
        'fixed lg:static inset-y-0 left-0 z-40 w-64 bg-blue-900 text-white overflow-y-auto flex flex-col h-screen sidebar-no-scrollbar transition-transform duration-300 ease-in-out'
      ]"
    >
    <!-- Sidebar Header & Logo Card -->
    <div class="p-5 border-b border-blue-800/60 flex flex-col gap-4">
      <div class="bg-white rounded-2xl p-4 flex items-center justify-center shadow-sm">
        <img :src="'/sidebar_hero.png'" alt="Sidebar Hero" class="max-h-20 w-auto object-contain" />
      </div>
      <div class="px-1">
        <p class="text-sm font-bold text-white leading-tight">Sistem Retensi & Alih Media</p>
        <p class="text-xs text-blue-200 mt-1.5">Rekam Medis</p>
      </div>
    </div>

    <!-- User Info (Advanced Settings Redirect for Administrator) -->
    <router-link 
      v-if="activeUser.role === 'Administrator'"
      to="/advanced-settings"
      class="p-4 border-b border-blue-800 flex items-center gap-4 hover:bg-blue-800 cursor-pointer transition-colors group"
      title="Klik untuk Pengaturan Lanjutan"
    >
      <div class="w-12 h-12 rounded-full bg-white/30 flex items-center justify-center flex-shrink-0 group-hover:bg-white/40 transition-colors">
        <User class="w-7 h-7 text-white" />
      </div>
      <div class="min-w-0">
        <p class="font-bold text-sm">{{ activeUser.nama_lengkap }}</p>
        <p class="text-xs text-blue-100 group-hover:text-white transition-colors">{{ activeUser.role }}</p>
      </div>
      <div class="ml-auto opacity-0 group-hover:opacity-100 transition-opacity">
        <Setting class="w-4 h-4 text-blue-200" />
      </div>
    </router-link>

    <!-- User Info (Static Panel for other roles) -->
    <div 
      v-else
      class="p-4 border-b border-blue-800 flex items-center gap-4 text-white"
    >
      <div class="w-12 h-12 rounded-full bg-white/30 flex items-center justify-center flex-shrink-0">
        <User class="w-7 h-7 text-white" />
      </div>
      <div class="min-w-0">
        <p class="font-bold text-sm">{{ activeUser.nama_lengkap }}</p>
        <p class="text-xs text-blue-100">{{ activeUser.role }}</p>
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
        <Odometer class="w-5 h-5 flex-shrink-0" />
        <span>Dashboard</span>
      </router-link>

      <!-- Data Master -->
      <div v-if="activeUser.role !== 'Direktur'">
        <button
          @click="toggleMenu('dataMaster')"
          :class="isMenuOpen('dataMaster') ? 'bg-blue-700' : 'hover:bg-blue-800'"
          class="w-full flex items-center gap-4 px-4 py-3 rounded-lg text-white text-sm font-medium transition-colors duration-200"
        >
          <Grid class="w-5 h-5 flex-shrink-0" />
          <span class="flex-1 text-left">Data Master</span>
          <ArrowDown 
            class="w-4 h-4 flex-shrink-0 transition-transform" 
            :style="{ transform: isMenuOpen('dataMaster') ? 'rotate(180deg)' : 'rotate(0deg)' }"
          />
        </button>
        <transition name="dropdown">
          <div v-show="isMenuOpen('dataMaster')" class="mt-1 space-y-1 overflow-hidden">
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
              v-if="activeUser.role === 'Administrator'"
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
      <div v-if="activeUser.role !== 'Direktur'">
        <button
          @click="toggleMenu('transaksi')"
          :class="isMenuOpen('transaksi') ? 'bg-blue-700' : 'hover:bg-blue-800'"
          class="w-full flex items-center gap-4 px-4 py-3 rounded-lg text-white text-sm font-medium transition-colors duration-200"
        >
          <Document class="w-5 h-5 flex-shrink-0" />
          <span class="flex-1 text-left">Transaksi</span>
          <ArrowDown 
            class="w-4 h-4 flex-shrink-0 transition-transform" 
            :style="{ transform: isMenuOpen('transaksi') ? 'rotate(180deg)' : 'rotate(0deg)' }"
          />
        </button>
        <transition name="dropdown">
          <div v-show="isMenuOpen('transaksi')" class="mt-1 space-y-1 overflow-hidden">
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
              v-if="activeUser.role === 'Administrator'"
              to="/log-aktivitas" 
              :class="isActive('/log-aktivitas') ? 'bg-blue-600 text-white border-l-4 border-white' : 'text-blue-100 hover:text-white hover:bg-blue-700 border-l-4 border-transparent'"
              class="block px-11 py-2 text-sm transition-all duration-200"
            >
              Log Aktivitas
            </router-link>
          </div>
        </transition>
      </div>

      <!-- Pengajuan SK Pemusnahan (Administrator & Direktur) -->
      <router-link
        v-if="activeUser.role === 'Administrator' || activeUser.role === 'Direktur'"
        to="/pengajuan-sk"
        :class="isActive('/pengajuan-sk') ? 'bg-blue-700' : 'hover:bg-blue-800'"
        class="flex items-center gap-4 px-4 py-3 rounded-lg text-white text-sm font-medium transition-colors duration-200"
      >
        <Files class="w-5 h-5 flex-shrink-0" />
        <span>Pengajuan SK</span>
      </router-link>

      <!-- Laporan -->
      <div>
        <button
          @click="toggleMenu('laporan')"
          :class="isMenuOpen('laporan') ? 'bg-blue-700' : 'hover:bg-blue-800'"
          class="w-full flex items-center gap-4 px-4 py-3 rounded-lg text-white text-sm font-medium transition-colors duration-200"
        >
          <TrendCharts class="w-5 h-5 flex-shrink-0" />
          <span class="flex-1 text-left">Laporan</span>
          <ArrowDown 
            class="w-4 h-4 flex-shrink-0 transition-transform" 
            :style="{ transform: isMenuOpen('laporan') ? 'rotate(180deg)' : 'rotate(0deg)' }"
          />
        </button>
        <transition name="dropdown">
          <div v-show="isMenuOpen('laporan')" class="mt-1 space-y-1 overflow-hidden">
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
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close'])

const route = useRoute()

// Close sidebar on route change (for mobile viewports)
watch(() => route.path, () => {
  emit('close')
})
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
/* Hide scrollbar for Chrome, Safari and Opera */
.sidebar-no-scrollbar::-webkit-scrollbar {
  display: none;
}

/* Hide scrollbar for IE, Edge and Firefox */
.sidebar-no-scrollbar {
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}

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
