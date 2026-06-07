<template>
  <div class="min-h-screen w-screen flex flex-col md:flex-row bg-white font-outfit overflow-x-hidden">
    <!-- Left Panel (Branding & Server Corridor Background) -->
    <div 
      class="hidden md:flex md:w-[60%] relative bg-cover bg-center flex-col justify-between p-16 text-white overflow-hidden" 
      :style="{ backgroundImage: 'url(/bg-login.png)' }"
    >
      <!-- Deep Blue Gradient Overlay matching the mock page -->
      <div class="absolute inset-0 bg-gradient-to-b from-[#0b3c8f]/90 via-[#032b73]/95 to-[#011845]/98 mix-blend-multiply z-0"></div>
      
      <!-- Subtle Tech Dot Pattern overlay -->
      <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:24px_24px] z-0"></div>

      <!-- Top Spacer -->
      <div></div>

      <!-- Center Branding Content -->
      <div class="relative z-10 flex flex-col items-center text-center">
        <!-- Logo circle -->
        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-2xl mb-6 transform hover:scale-105 transition-transform duration-300">
          <!-- SVG Leaf Icon -->
          <svg class="w-11 h-11 text-[#10B981]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M2 22C2 12 12 2 22 2C22 12 12 22 2 22Z" />
            <path d="M2 22L16 8" />
          </svg>
        </div>
        <h1 class="text-4xl font-extrabold tracking-wide mb-3 uppercase">RSU Kaliwates</h1>
        <p class="text-lg text-white/90 font-medium max-w-md">Sistem Retensi & Digitasi Rekam Medis</p>
      </div>

      <!-- Bottom Slider / Carousel Indicator -->
      <div class="relative z-10 flex justify-center items-center gap-2">
        <span class="w-8 h-1 bg-[#10B981] rounded-full"></span>
        <span class="w-1.5 h-1.5 bg-white/30 rounded-full"></span>
        <span class="w-1.5 h-1.5 bg-white/30 rounded-full"></span>
      </div>
    </div>

    <!-- Right Panel (Login Form) -->
    <div class="w-full md:w-[40%] min-h-screen flex flex-col justify-center items-center bg-white px-8 py-12 md:px-16 relative">
      <div class="w-full max-w-md">
        <!-- Header -->
        <div class="mb-10 text-left">
          <h2 class="text-3xl font-bold text-gray-900 mb-2 font-outfit">Selamat Datang</h2>
          <p class="text-sm text-gray-500 font-medium">Silakan masuk menggunakan kredensial Anda.</p>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleLogin" class="space-y-6">
          <!-- Username Input -->
          <div class="relative group">
            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 pointer-events-none group-focus-within:text-[#10B981] transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
              </svg>
            </span>
            <input 
              v-model="username" 
              type="text" 
              required
              placeholder="username"
              class="w-full pl-12 pr-5 py-3.5 border border-gray-200 rounded-full text-gray-800 text-sm placeholder-gray-400 focus:outline-none focus:border-[#10B981] focus:ring-2 focus:ring-[#10B981]/15 transition-all duration-300 shadow-sm"
            />
          </div>

          <!-- Password Input -->
          <div class="relative group">
            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 pointer-events-none group-focus-within:text-[#10B981] transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
              </svg>
            </span>
            <input 
              v-model="password" 
              type="password" 
              required
              placeholder="password"
              class="w-full pl-12 pr-5 py-3.5 border border-gray-200 rounded-full text-gray-800 text-sm placeholder-gray-400 focus:outline-none focus:border-[#10B981] focus:ring-2 focus:ring-[#10B981]/15 transition-all duration-300 shadow-sm"
            />
          </div>

          <!-- Submit Button -->
          <button 
            type="submit" 
            :disabled="loading"
            class="w-full py-4 bg-[#10B981] hover:bg-[#0d9f68] text-white font-bold rounded-full shadow-lg shadow-emerald-500/10 hover:shadow-emerald-500/25 active:scale-[0.99] disabled:opacity-50 disabled:scale-100 transition-all duration-300 flex items-center justify-center gap-2 mt-8 uppercase tracking-wider text-sm"
          >
            <svg v-if="loading" class="animate-spin w-5 h-5 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ loading ? 'MENGECEK DATA...' : 'MASUK' }}
          </button>
        </form>

        <!-- Help Link -->
        <div class="text-center mt-8">
          <p class="text-xs text-gray-400">
            Butuh bantuan? <a href="#" class="text-[#2563eb] hover:underline font-semibold transition-colors">Hubungi Administrator</a>
          </p>
        </div>

        <!-- Collapsible Developer Credentials Panel -->
        <div class="mt-8 pt-6 border-t border-gray-100">
          <div 
            class="flex justify-between items-center cursor-pointer text-gray-400 hover:text-gray-600 transition-colors select-none" 
            @click="showCredentials = !showCredentials"
          >
            <p class="text-xs font-medium">Bantuan Kredensial Uji Coba</p>
            <svg 
              class="w-4 h-4 transition-transform duration-300" 
              :class="{ 'rotate-180': showCredentials }" 
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </div>
          
          <div 
            v-show="showCredentials" 
            class="grid grid-cols-2 gap-3 mt-4 transition-all duration-300"
          >
            <div 
              @click="fillCredentials('admin', 'admin123')" 
              class="p-2.5 rounded-xl bg-gray-50 border border-gray-100 hover:bg-gray-100 cursor-pointer transition-all duration-200 text-left"
            >
              <p class="text-xs font-bold text-blue-600 mb-0.5">Administrator</p>
              <p class="text-[10px] text-gray-500 font-mono">admin / admin123</p>
            </div>
            <div 
              @click="fillCredentials('staff', 'staff123')" 
              class="p-2.5 rounded-xl bg-gray-50 border border-gray-100 hover:bg-gray-100 cursor-pointer transition-all duration-200 text-left"
            >
              <p class="text-xs font-bold text-emerald-600 mb-0.5">Staff Rekam Medis</p>
              <p class="text-[10px] text-gray-500 font-mono">staff / staff123</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { showSuccessToast, showErrorToast } from '../utils/notification'

const router = useRouter()
const username = ref('')
const password = ref('')
const loading = ref(false)
const showCredentials = ref(false)

const fillCredentials = (user, pass) => {
  username.value = user
  password.value = pass
}

const handleLogin = async () => {
  loading.value = true
  try {
    const response = await fetch('/api/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
      },
      body: JSON.stringify({
        username: username.value,
        password: password.value,
      })
    })

    const res = await response.json()
    if (response.ok && res.success) {
      // Store user details in localStorage to persist front-end session
      localStorage.setItem('auth_user', JSON.stringify(res.user))
      
      showSuccessToast('Selamat datang kembali, ' + res.user.nama_lengkap, 'Login Berhasil!')
      
      // Redirect to home
      router.push({ name: 'home' })
    } else {
      showErrorToast(res.message || 'Username atau password salah.', 'Gagal Masuk')
    }
  } catch (err) {
    showErrorToast('Terjadi kesalahan jaringan atau server.', 'Koneksi Bermasalah')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

.font-outfit {
  font-family: 'Outfit', sans-serif;
}
</style>
