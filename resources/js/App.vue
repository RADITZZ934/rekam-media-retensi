<template>
  <div class="relative w-screen h-screen overflow-hidden">
    <!-- Public Login Layout (No Sidebar/Topbar/Chat widget) -->
    <div v-if="route.name === 'login'" class="min-h-screen w-screen bg-[#0b1329]">
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </div>

    <!-- Authenticated App Dashboard Layout -->
    <div v-else class="flex h-screen bg-gray-100 overflow-hidden">
      <Sidebar />
      <div class="flex-1 flex flex-col overflow-hidden">
        <Topbar />
        <main ref="mainContainer" class="flex-1 overflow-y-auto bg-[#f9fbff]">
          <router-view v-slot="{ Component }">
            <transition name="fade" mode="out-in">
              <component :is="Component" />
            </transition>
          </router-view>
        </main>
      </div>
      <!-- Floating Chat AI Widget -->
      <ChatAiWidget />
    </div>

    <!-- Premium Logout Overlay -->
    <transition name="overlay-fade">
      <div v-if="showLogoutOverlay" class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-[#0b1329] text-white">
        <!-- Pulsing logout animation -->
        <div class="mb-6 relative flex items-center justify-center">
          <div class="absolute w-24 h-24 rounded-full bg-red-500/10 animate-ping duration-[1.5s]"></div>
          <div class="w-16 h-16 rounded-full bg-[#1e293b] border border-red-500/30 flex items-center justify-center shadow-lg shadow-red-500/20">
            <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
          </div>
        </div>
        <h3 class="text-xl font-bold tracking-wide animate-pulse">Menutup Sesi Aman...</h3>
        <p class="text-gray-400 text-sm mt-2">Sampai jumpa kembali</p>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import Sidebar from './components/Sidebar.vue'
import Topbar from './components/Topbar.vue'
import ChatAiWidget from './components/ChatAiWidget.vue'
import { showSuccessToast } from './utils/notification'

const route = useRoute()
const router = useRouter()
const mainContainer = ref(null)
const showLogoutOverlay = ref(false)

// Smooth scroll reset on route path change
watch(() => route.path, () => {
  if (mainContainer.value) {
    mainContainer.value.scrollTop = 0
  }
})

// Handle global trigger-logout event for smooth transition
const handleLogoutEvent = async () => {
  showLogoutOverlay.value = true
  showSuccessToast('Sampai jumpa kembali!', 'Logout Berhasil')
  
  // Call backend logout in background
  try {
    await fetch('/api/logout', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
      }
    })
  } catch (e) {
    console.error('Logout API failed, proceeding client-side cleanup', e)
  }
  
  // Wait for the overlay fade-in animation to complete (1000ms)
  setTimeout(() => {
    localStorage.removeItem('auth_user')
    router.push('/login')
    
    // Wait for route change to finish, then fade out overlay
    setTimeout(() => {
      showLogoutOverlay.value = false
    }, 600)
  }, 1000)
}

// Register global event listener
onMounted(() => {
  window.addEventListener('trigger-logout', handleLogoutEvent)
})

onUnmounted(() => {
  window.removeEventListener('trigger-logout', handleLogoutEvent)
})
</script>

<style>
/* Premium decelleration curve transition */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.24s cubic-bezier(0.16, 1, 0.3, 1),
              transform 0.24s cubic-bezier(0.16, 1, 0.3, 1);
}

.fade-enter-from {
  opacity: 0;
  transform: translateY(8px);
}

.fade-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

/* Premium Logout Overlay Transition */
.overlay-fade-enter-active,
.overlay-fade-leave-active {
  transition: opacity 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}

.overlay-fade-enter-from,
.overlay-fade-leave-to {
  opacity: 0;
}
</style>
