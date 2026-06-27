<template>
  <div class="bg-red-500 text-white p-4">
    TEST TAILWIND
  </div>
  <nav class="fixed top-0 left-0 right-0 z-50" :class="scrolled ? 'shadow-lg backdrop-blur-md bg-gradient-to-r from-blue-600/95 to-purple-600/95' : 'bg-gradient-to-r from-blue-600 to-purple-600'">
    <!-- Main Container -->
    <div class="px-4 sm:px-6 lg:px-8 py-4 max-w-7xl mx-auto">
      <div class="flex items-center justify-between h-16">
        
        <!-- Left: Logo & Brand -->
        <div class="flex items-center gap-3 flex-shrink-0">
          <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
            <span class="text-white text-lg font-bold">RM</span>
          </div>
          <div class="hidden sm:block">
            <h1 class="text-white font-bold text-lg">Retensi RM</h1>
            <p class="text-white/80 text-xs">RSU Kaliwates</p>
          </div>
        </div>

        <!-- Center: Menu (Desktop) -->
        <div class="hidden lg:flex items-center gap-1">
          <router-link
            v-for="item in menuItems"
            :key="item.path"
            :to="item.path"
            :class="[
              'px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300',
              isActive(item.path)
                ? 'text-white bg-white/20 shadow-lg shadow-black/20'
                : 'text-white/80 hover:text-white hover:bg-white/10'
            ]"
          >
            {{ item.label }}
          </router-link>
        </div>

        <!-- Right: Notification & User Menu -->
        <div class="flex items-center gap-4">
          <!-- Notification Bell -->
          <button
            @click="showNotifications = !showNotifications"
            class="relative p-2 text-white hover:bg-white/10 rounded-lg transition-colors duration-300"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0018 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span v-if="notificationCount > 0" class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
          </button>

          <!-- Notification Dropdown -->
          <div
            v-if="showNotifications"
            @click.stop
            class="absolute top-16 right-4 w-80 bg-white text-gray-800 rounded-xl shadow-2xl p-4 animate-fade-in"
          >
            <h3 class="font-bold text-gray-900 mb-3">Notifikasi</h3>
            <div v-if="notifications.length > 0" class="space-y-2 max-h-64 overflow-y-auto">
              <div v-for="notif, idx in notifications" :key="idx" class="p-2 hover:bg-gray-100 rounded-lg cursor-pointer transition-colors">
                <p class="text-sm font-medium text-gray-900">{{ notif.title }}</p>
                <p class="text-xs text-gray-600 mt-1">{{ notif.message }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ formatTime(notif.time) }}</p>
              </div>
            </div>
            <div v-else class="text-center py-4 text-gray-500">Tidak ada notifikasi</div>
          </div>

          <!-- User Avatar & Dropdown -->
          <div class="relative">
            <button
              @click="showUserMenu = !showUserMenu"
              class="w-10 h-10 rounded-full bg-white/20 text-white font-bold flex items-center justify-center hover:bg-white/30 transition-colors duration-300 border border-white/30"
            >
              {{ userInitial }}
            </button>

            <!-- User Dropdown Menu -->
            <div
              v-if="showUserMenu"
              @click.stop
              class="absolute right-0 mt-2 w-48 bg-white text-gray-800 rounded-xl shadow-2xl overflow-hidden animate-fade-in"
            >
              <div class="px-4 py-3 border-b border-gray-200">
                <p class="font-bold text-gray-900">{{ userName }}</p>
                <p class="text-xs text-gray-600">{{ userRole }}</p>
              </div>
              <button
                @click="goToProfile"
                class="w-full text-left px-4 py-2 hover:bg-gray-100 text-sm transition-colors text-gray-700"
              >
                👤 Profil
              </button>
              <button
                @click="goToSettings"
                class="w-full text-left px-4 py-2 hover:bg-gray-100 text-sm transition-colors text-gray-700"
              >
                ⚙️ Pengaturan
              </button>
              <button
                @click="logout"
                class="w-full text-left px-4 py-2 hover:bg-red-50 text-sm transition-colors text-red-600 border-t border-gray-200"
              >
                🚪 Logout
              </button>
            </div>
          </div>

          <!-- Mobile Menu Toggle -->
          <button
            @click="showMobileMenu = !showMobileMenu"
            class="lg:hidden p-2 text-white hover:bg-white/10 rounded-lg transition-colors duration-300"
          >
            <svg v-if="!showMobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div
        v-if="showMobileMenu"
        class="lg:hidden mt-4 pb-4 space-y-2 animate-slide-down border-t border-white/20 pt-4"
      >
        <router-link
          v-for="item in menuItems"
          :key="item.path"
          :to="item.path"
          @click="showMobileMenu = false"
          :class="[
            'block px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300',
            isActive(item.path)
              ? 'text-white bg-white/20'
              : 'text-white/80 hover:text-white hover:bg-white/10'
          ]"
        >
          {{ item.label }}
        </router-link>
      </div>
    </div>
  </nav>

  <!-- Spacer untuk fixed navbar -->
  <div class="h-24"></div>

  <!-- Click outside handler -->
  <div
    v-if="showNotifications || showUserMenu"
    @click="closeDropdowns"
    class="fixed inset-0 z-40"
  ></div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

// State
const showMobileMenu = ref(false)
const showNotifications = ref(false)
const showUserMenu = ref(false)
const scrolled = ref(false)

// User data
const userName = ref('Administrator')
const userRole = ref('Admin')

// Menu items
const menuItems = [
  { label: 'Dashboard', path: '/dashboard' },
  { label: 'Data Pasien', path: '/pasien' },
  { label: 'Alih Media', path: '/alih-media' },
  { label: 'Validasi OCR', path: '/validasi-ocr' },
  { label: 'Master Kasus', path: '/master-kasus' },
  { label: 'Retensi', path: '/retensi' },
  { label: 'Pemusnahan', path: '/pemusnahan' },
]

// Notifications (sample data)
const notificationCount = ref(3)
const notifications = ref([
  { title: 'Dokumen baru', message: 'Ada 5 dokumen menunggu validasi OCR', time: new Date(Date.now() - 5 * 60000) },
  { title: 'Retensi selesai', message: 'Proses retensi untuk bulan Maret selesai', time: new Date(Date.now() - 30 * 60000) },
  { title: 'Pemusnahan dijadwalkan', message: 'Jadwal pemusnahan sudah disiapkan', time: new Date(Date.now() - 2 * 3600000) },
])

// Computed
const userInitial = computed(() => {
  return userName.value.split(' ').map(n => n[0]).join('').toUpperCase()
})

// Methods
const isActive = (path) => {
  return route.path === path || route.path.startsWith(path + '/')
}

const closeDropdowns = () => {
  showNotifications.value = false
  showUserMenu.value = false
}

const formatTime = (date) => {
  const now = new Date()
  const diff = now - date
  const minutes = Math.floor(diff / 60000)
  const hours = Math.floor(diff / 3600000)

  if (minutes < 1) return 'Baru saja'
  if (minutes < 60) return `${minutes}m yang lalu`
  if (hours < 24) return `${hours}h yang lalu`
  return date.toLocaleDateString('id-ID')
}

const goToProfile = () => {
  // Navigate to profile
  // router.push('/profile')
  closeDropdowns()
}

const goToSettings = () => {
  // Navigate to settings
  // router.push('/settings')
  closeDropdowns()
}

const logout = () => {
  closeDropdowns()
  window.dispatchEvent(new CustomEvent('trigger-logout'))
}

// Scroll event handler
const handleScroll = () => {
  scrolled.value = window.scrollY > 20
}

// Lifecycle
onMounted(() => {
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slide-down {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.3s ease-out;
}

.animate-slide-down {
  animation: slide-down 0.3s ease-out;
}

/* Smooth transitions */
router-link {
  position: relative;
}

router-link::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 0;
  height: 2px;
  background: rgba(255, 255, 255, 0.6);
  transition: width 0.3s ease-out;
}

router-link:hover::after {
  width: 100%;
}
</style>
