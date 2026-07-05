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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
          <div v-for="i in 5" :key="i" class="bg-white rounded-xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.05)] border border-gray-100 p-6 flex flex-col justify-between h-[140px]">
            <div class="flex justify-between items-start mb-4">
              <div class="h-4 bg-gray-200 rounded w-1/2"></div>
              <div class="w-11 h-11 rounded-xl bg-gray-100"></div>
            </div>
            <div class="h-10 bg-gray-200 rounded w-2/5 mt-auto"></div>
          </div>
        </div>

        <!-- 2 Bottom Panels Skeleton -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Aktivitas Terbaru Skeleton -->
          <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-7 lg:col-span-2">
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
          <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-7 flex flex-col lg:col-span-1">
             <div class="h-6 bg-gray-200 rounded w-1/3 mb-8"></div>
             <div class="flex-1 flex items-end justify-around h-44 pb-2 gap-6">
                <div v-for="k in 3" :key="k" class="flex flex-col items-center w-full max-w-[90px] space-y-3">
                  <div class="h-4 bg-gray-200 rounded w-8"></div>
                  <div class="w-14 bg-gray-100 rounded-t-lg h-24"></div>
                  <div class="h-3 bg-gray-200 rounded w-12"></div>
                </div>
             </div>
             
          </div>
        </div>
      </div>

      <!-- Dashboard Content -->
      <div v-else>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
          <!-- Rekam Medis Aktif -->
          <div class="bg-white rounded-xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] border border-blue-100 p-6 flex flex-col justify-between hover:shadow-lg transition-shadow">
            <div class="flex justify-between items-start mb-4">
              <h3 class="text-gray-500 font-semibold text-sm">Rekam Medis Aktif</h3>
              <div class="p-2.5 rounded-xl bg-blue-50 text-blue-600 border border-blue-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
              </div>
            </div>
            <p class="text-[2.5rem] leading-none font-bold text-gray-900">{{ formatNumber(summary.aktif) }}</p>
          </div>

          <!-- Rekam Medis Inaktif -->
          <div class="bg-white rounded-xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] border border-green-200 p-6 flex flex-col justify-between hover:shadow-lg transition-shadow">
            <div class="flex justify-between items-start mb-4">
              <h3 class="text-gray-500 font-semibold text-sm">Rekam Medis Inaktif</h3>
              <div class="p-2.5 rounded-xl bg-green-50 text-green-600 border border-green-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              </div>
            </div>
            <p class="text-[2.5rem] leading-none font-bold text-gray-900">{{ formatNumber(summary.inaktif) }}</p>
          </div>

          <!-- Alih Media OCR -->
          <div class="bg-white rounded-xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] border border-red-200 p-6 flex flex-col justify-between hover:shadow-lg transition-shadow">
            <div class="flex justify-between items-start mb-4">
              <h3 class="text-gray-500 font-semibold text-sm">Alih Media OCR</h3>
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

          <!-- Sudah Dimusnahkan -->
          <div class="bg-white rounded-xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] border border-purple-200 p-6 flex flex-col justify-between hover:shadow-lg transition-shadow">
            <div class="flex justify-between items-start mb-4">
              <h3 class="text-gray-500 font-semibold text-sm">Sudah Dimusnahkan</h3>
              <div class="p-2.5 rounded-xl bg-purple-50 text-purple-600 border border-purple-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16m-5 4l-4 4-2-2"></path></svg>
              </div>
            </div>
            <p class="text-[2.5rem] leading-none font-bold text-gray-900">{{ formatNumber(summary.dimusnahkan) }}</p>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Aktivitas Terbaru -->
          <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-7 relative overflow-hidden lg:col-span-2">
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
          <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-7 relative overflow-hidden flex flex-col lg:col-span-1">
            <!-- Decorative background -->
             <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-purple-50 rounded-full blur-3xl opacity-70 pointer-events-none"></div>
             
            <h2 class="text-xl font-extrabold text-gray-800 mb-6 flex items-center gap-2">
               <span class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                 <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
               </span>
               Statistik Bulanan
            </h2>
            
            <div class="flex-1 flex flex-col justify-center min-h-[220px]">
              <div class="h-44 flex flex-col justify-between relative mt-4">
                <!-- Chart Grid Lines -->
                <div class="absolute inset-0 flex flex-col justify-between pointer-events-none pb-8 text-[10px] text-gray-300 font-medium">
                  <div class="border-b border-gray-100 w-full pt-1"></div>
                  <div class="border-b border-gray-100 w-full"></div>
                  <div class="border-b border-gray-100 w-full"></div>
                  <div class="border-b border-gray-200 w-full"></div>
                </div>

                <!-- Bars Container -->
                <div class="relative z-10 flex items-end justify-around h-32 pb-2 px-2 gap-6">
                  <!-- Bar 1 (Total Diproses) -->
                  <div class="flex flex-col items-center w-full max-w-[90px] group cursor-pointer">
                    <!-- Tooltip / Value above the bar -->
                    <span class="text-sm font-extrabold text-blue-500 mb-2 transition-all duration-300 transform group-hover:scale-125">
                      {{ statistik.diproses }}
                    </span>
                    <div class="w-14 bg-gray-50 rounded-t-lg h-24 flex items-end relative border border-gray-100 shadow-inner">
                      <div class="w-full bg-blue-500 rounded-t-lg transition-all duration-500 ease-out group-hover:bg-blue-600 shadow-md origin-bottom"
                           :style="{ height: getBarHeight(statistik.diproses) }">
                      </div>
                    </div>
                    <span class="text-[11px] font-bold text-gray-500 mt-2.5 text-center truncate w-full group-hover:text-blue-500 transition-colors">
                      Total Diproses
                    </span>
                  </div>

                  <!-- Bar 2 (Dokumen Didigitalisasi) -->
                  <div class="flex flex-col items-center w-full max-w-[90px] group cursor-pointer">
                    <span class="text-sm font-extrabold text-emerald-500 mb-2 transition-all duration-300 transform group-hover:scale-125">
                      {{ statistik.didigitalisasi }}
                    </span>
                    <div class="w-14 bg-gray-50 rounded-t-lg h-24 flex items-end relative border border-gray-100 shadow-inner">
                      <div class="w-full bg-emerald-500 rounded-t-lg transition-all duration-500 ease-out group-hover:bg-emerald-600 shadow-md origin-bottom"
                           :style="{ height: getBarHeight(statistik.didigitalisasi) }">
                      </div>
                    </div>
                    <span class="text-[11px] font-bold text-gray-500 mt-2.5 text-center truncate w-full group-hover:text-emerald-500 transition-colors">
                      Didigitalisasi
                    </span>
                  </div>

                  <!-- Bar 3 (Dokumen Dimusnahkan) -->
                  <div class="flex flex-col items-center w-full max-w-[90px] group cursor-pointer">
                    <span class="text-sm font-extrabold text-rose-500 mb-2 transition-all duration-300 transform group-hover:scale-125">
                      {{ statistik.dimusnahkan }}
                    </span>
                    <div class="w-14 bg-gray-50 rounded-t-lg h-24 flex items-end relative border border-gray-100 shadow-inner">
                      <div class="w-full bg-rose-500 rounded-t-lg transition-all duration-500 ease-out group-hover:bg-rose-600 shadow-md origin-bottom"
                           :style="{ height: getBarHeight(statistik.dimusnahkan) }">
                      </div>
                    </div>
                    <span class="text-[11px] font-bold text-gray-500 mt-2.5 text-center truncate w-full group-hover:text-rose-500 transition-colors">
                      Dimusnahkan
                    </span>
                  </div>
                </div>
              </div>
            </div>
            

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'

const loading = ref(true)
const summary = ref({
  aktif: 0,
  inaktif: 0,
  didigitalisasi: 0,
  siapMusnah: 0,
  dimusnahkan: 0
})
const aktivitas = ref([])
const statistik = ref({
  diproses: 0,
  didigitalisasi: 0,
  dimusnahkan: 0
})

const maxStatistikVal = computed(() => {
  return Math.max(1, statistik.value.diproses || 0, statistik.value.didigitalisasi || 0, statistik.value.dimusnahkan || 0)
})

const getBarHeight = (value) => {
  if (!value) return '0%'
  return `${(value / maxStatistikVal.value) * 100}%`
}

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
