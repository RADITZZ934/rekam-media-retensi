<template>
  <div class="min-h-screen bg-white flex flex-col">
    <div class="p-8 flex-1 bg-[#f9fbff]">
      <!-- Message Box -->
      <div class="bg-[#eff6ff] border border-[#bfdbfe] border-l-4 border-l-blue-500 p-4 rounded-lg mb-8 shadow-sm flex items-center">
        <p class="text-blue-800 font-medium">Selamat datang di Sistem Retensi & Alih Media Rekam Medis</p>
      </div>

      <!-- Skeleton Screen -->
      <div v-if="loading" class="animate-pulse">
        <!-- Stats Cards Skeleton -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div v-for="i in 4" :key="i" class="bg-white rounded-xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.05)] border border-gray-100 p-6 flex flex-col justify-between h-[140px]">
            <div class="flex justify-between items-start mb-4">
              <div class="h-4 bg-gray-200 rounded w-1/2"></div>
              <div class="w-11 h-11 rounded-xl bg-gray-100"></div>
            </div>
            <div class="h-10 bg-gray-200 rounded w-2/5 mt-auto"></div>
          </div>
        </div>

        <!-- 2 Bottom Panels Skeleton -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Aktivitas Terbaru Skeleton -->
          <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-7">
             <div class="h-6 bg-gray-200 rounded w-2/5 mb-8"></div>
             <div class="space-y-5">
               <div v-for="j in 5" :key="j" class="flex items-start gap-4 p-4 rounded-xl bg-[#f8fafc] border border-gray-100">
                 <div class="w-3.5 h-3.5 rounded-full bg-gray-200 mt-1"></div>
                 <div class="flex-1">
                   <div class="h-4 bg-gray-200 rounded w-3/4 mb-2.5"></div>
                   <div class="h-3 bg-gray-200 rounded w-1/4"></div>
                 </div>
               </div>
             </div>
          </div>

          <!-- Statistik Bulanan Skeleton -->
          <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-7 flex flex-col">
             <div class="h-6 bg-gray-200 rounded w-1/3 mb-8"></div>
             <div class="space-y-6 flex-1 flex flex-col justify-center">
               <div v-for="k in 3" :key="k">
                 <div class="flex items-center justify-between mb-3">
                   <div class="h-4 bg-gray-200 rounded w-2/5"></div>
                   <div class="h-6 bg-gray-200 rounded w-10"></div>
                 </div>
                 <div class="h-3 bg-gray-100 rounded-full w-full"></div>
               </div>
             </div>
             <div class="mt-8 pt-5 border-t border-gray-100 flex items-center gap-4 bg-gray-50/50 p-4 rounded-xl">
               <div class="w-10 h-10 bg-gray-200 rounded-lg"></div>
               <div class="flex-1">
                  <div class="h-3 bg-gray-200 rounded w-1/4 mb-2"></div>
                  <div class="h-4 bg-gray-200 rounded w-1/2"></div>
               </div>
             </div>
          </div>
        </div>
      </div>

      <!-- Dashboard Content -->
      <div v-else>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Dokumen Aktif -->
          <div class="bg-white rounded-xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] border border-blue-100 p-6 flex flex-col justify-between hover:shadow-lg transition-shadow">
            <div class="flex justify-between items-start mb-4">
              <h3 class="text-gray-500 font-semibold text-sm">Dokumen Aktif</h3>
              <div class="p-2.5 rounded-xl bg-blue-50 text-blue-600 border border-blue-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
              </div>
            </div>
            <p class="text-[2.5rem] leading-none font-bold text-gray-900">{{ formatNumber(summary.aktif) }}</p>
          </div>

          <!-- Dokumen Inaktif -->
          <div class="bg-white rounded-xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] border border-green-200 p-6 flex flex-col justify-between hover:shadow-lg transition-shadow">
            <div class="flex justify-between items-start mb-4">
              <h3 class="text-gray-500 font-semibold text-sm">Dokumen Inaktif</h3>
              <div class="p-2.5 rounded-xl bg-green-50 text-green-600 border border-green-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              </div>
            </div>
            <p class="text-[2.5rem] leading-none font-bold text-gray-900">{{ formatNumber(summary.inaktif) }}</p>
          </div>

          <!-- Didigitalisasi -->
          <div class="bg-white rounded-xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] border border-red-200 p-6 flex flex-col justify-between hover:shadow-lg transition-shadow">
            <div class="flex justify-between items-start mb-4">
              <h3 class="text-gray-500 font-semibold text-sm">Didigitalisasi</h3>
              <div class="p-2.5 rounded-xl bg-red-50 text-red-600 border border-red-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
              </div>
            </div>
            <p class="text-[2.5rem] leading-none font-bold text-gray-900">{{ formatNumber(summary.didigitalisasi) }}</p>
          </div>

          <!-- Siap Dimusnahkan -->
          <div class="bg-white rounded-xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] border border-yellow-300 p-6 flex flex-col justify-between hover:shadow-lg transition-shadow">
            <div class="flex justify-between items-start mb-4">
              <h3 class="text-gray-500 font-semibold text-sm">Siap Dimusnahkan</h3>
              <div class="p-2.5 rounded-xl bg-yellow-50 text-yellow-500 border border-yellow-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
              </div>
            </div>
            <p class="text-[2.5rem] leading-none font-bold text-gray-900">{{ formatNumber(summary.siapMusnah) }}</p>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Aktivitas Terbaru -->
          <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-7 relative overflow-hidden">
            <!-- Decorative background -->
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-blue-50 rounded-full blur-3xl opacity-60 pointer-events-none"></div>
            
            <h2 class="text-xl font-extrabold text-gray-800 mb-6 flex items-center gap-2">
              <span class="p-2 bg-blue-50 text-blue-500 rounded-lg">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              </span>
              Aktivitas Terbaru
            </h2>
            
            <div class="relative pl-3 space-y-5 before:absolute before:inset-y-0 before:left-[19px] before:w-[2px] before:bg-gray-100">
              <div v-for="(act, idx) in aktivitas" :key="idx" class="relative pl-8 group">
                <div class="absolute left-0 top-1.5 w-3.5 h-3.5 rounded-full border-[3px] border-white shadow-sm ring-4 ring-white z-10" :class="{
                  'bg-blue-500': act.color === 'blue',
                  'bg-green-500': act.color === 'green',
                  'bg-yellow-500': act.color === 'yellow',
                  'bg-red-500': act.color === 'red',
                }"></div>
                <div class="bg-white hover:bg-gray-50 border border-gray-100 hover:border-blue-100 p-4 rounded-xl shadow-sm transition duration-300 transform group-hover:-translate-y-0.5">
                  <p class="text-sm font-bold text-gray-800 leading-tight">{{ act.text }}</p>
                  <p class="text-xs text-gray-400 mt-1.5 font-medium flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ act.time }}
                  </p>
                </div>
              </div>
              <div v-if="aktivitas.length === 0" class="text-center py-6 text-gray-400 text-sm">
                Belum ada aktivitas
              </div>
            </div>
          </div>

          <!-- Statistik Bulanan (Chart representation) -->
          <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-7 relative overflow-hidden flex flex-col">
            <!-- Decorative background -->
             <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-purple-50 rounded-full blur-3xl opacity-70 pointer-events-none"></div>
             
            <h2 class="text-xl font-extrabold text-gray-800 mb-6 flex items-center gap-2">
               <span class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                 <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
               </span>
               Statistik Bulanan
            </h2>
            
            <div class="space-y-6 flex-1 flex flex-col justify-center">
              <!-- Item 1 -->
              <div class="group">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-sm font-bold text-gray-700 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.6)]"></span> Total Diproses
                  </span>
                  <span class="text-xl font-extrabold text-blue-600">{{ statistik.diproses }}</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3 mb-1 overflow-hidden">
                  <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-3 rounded-full transition-all duration-1000 ease-out transform group-hover:scale-y-110" 
                       :style="{ width: statistik.diproses > 0 ? '100%' : '0%' }"></div>
                </div>
              </div>
              
              <!-- Item 2 -->
              <div class="group">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-sm font-bold text-gray-700 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]"></span> Dokumen Didigitalisasi
                  </span>
                  <span class="text-xl font-extrabold text-green-600">{{ statistik.didigitalisasi }}</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3 mb-1 overflow-hidden">
                  <div class="bg-gradient-to-r from-green-400 to-emerald-500 h-3 rounded-full transition-all duration-1000 ease-out transform group-hover:scale-y-110" 
                       :style="{ width: statistik.diproses > 0 ? Math.min(100, (statistik.didigitalisasi / Math.max(1, statistik.diproses)) * 100) + '%' : '0%' }"></div>
                </div>
              </div>

              <!-- Item 3 -->
              <div class="group">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-sm font-bold text-gray-700 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.6)]"></span> Dokumen Dimusnahkan
                  </span>
                  <span class="text-xl font-extrabold text-red-500">{{ statistik.dimusnahkan }}</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3 mb-1 overflow-hidden">
                  <div class="bg-gradient-to-r from-red-400 to-rose-500 h-3 rounded-full transition-all duration-1000 ease-out transform group-hover:scale-y-110" 
                       :style="{ width: statistik.diproses > 0 ? Math.min(100, (statistik.dimusnahkan / Math.max(1, statistik.diproses)) * 100) + '%' : (statistik.dimusnahkan > 0 ? '100%' : '0%') }"></div>
                </div>
              </div>
            </div>
            
            <div class="mt-8 pt-5 border-t border-gray-100 flex items-center gap-4 bg-gray-50/50 p-4 rounded-xl">
               <div class="p-2.5 bg-white shadow-sm rounded-lg text-green-500 border border-green-100">
                 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
               </div>
               <div>
                  <p class="text-[0.7rem] uppercase tracking-wider text-gray-500 font-bold mb-0.5">Kinerja Bulan Ini</p>
                  <p class="text-sm font-extrabold text-gray-800">Menunjukkan Tren Positif</p>
               </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const loading = ref(true)
const summary = ref({
  aktif: 0,
  inaktif: 0,
  didigitalisasi: 0,
  siapMusnah: 0
})
const aktivitas = ref([])
const statistik = ref({
  diproses: 0,
  didigitalisasi: 0,
  dimusnahkan: 0
})

const formatNumber = (num) => {
  if (num === undefined || num === null) return 0;
  return new Intl.NumberFormat('id-ID').format(num)
}

const fetchDashboardData = async () => {
  loading.value = true
  try {
    const response = await fetch('/api/dashboard/summary')
    const json = await response.json()
    if (json.success) {
      summary.value = json.summary
      aktivitas.value = json.aktivitas || []
      statistik.value = json.statistik || { diproses: 0, didigitalisasi: 0, dimusnahkan: 0 }
    }
  } catch (error) {
    console.error('Failed to fetch dashboard summary', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchDashboardData()
})
</script>
