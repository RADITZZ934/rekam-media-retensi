<template>
  <div class="min-h-screen bg-gray-50 p-8">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 leading-tight">Advanced Settings</h1>
        <p class="text-gray-600 mt-2">Konfigurasi mendalam dan pengaturan sistem otomatis</p>
      </div>
      <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center shadow-sm">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
      </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      
      <!-- Section: Automation -->
      <div class="space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-gray-800 flex items-center gap-2">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
              Sistem Otomasi Retensi
            </h2>
          </div>
          
          <div class="p-8">
            <div class="mb-8">
              <label class="block text-sm font-semibold text-gray-700 mb-4 flex items-center justify-between">
                <span>Interval Sinkronisasi Status</span>
                <span class="text-blue-600 bg-blue-50 px-3 py-1 rounded-lg text-xs font-bold ring-1 ring-blue-100">
                  {{ settings.retention_update_interval }} {{ settings.retention_update_unit === 'minutes' ? 'Menit' : 'Jam' }}
                </span>
              </label>

              <!-- Unit Selector -->
              <div class="flex bg-gray-100 p-1 rounded-xl mb-4 max-w-[200px]">
                <button 
                  type="button"
                  @click="settings.retention_update_unit = 'hours'"
                  :class="[
                    'flex-1 py-1.5 text-xs font-semibold rounded-lg transition-all',
                    settings.retention_update_unit !== 'minutes' 
                      ? 'bg-white text-blue-600 shadow-sm' 
                      : 'text-gray-500 hover:text-gray-900'
                  ]"
                >
                  Jam
                </button>
                <button 
                  type="button"
                  @click="settings.retention_update_unit = 'minutes'"
                  :class="[
                    'flex-1 py-1.5 text-xs font-semibold rounded-lg transition-all',
                    settings.retention_update_unit === 'minutes' 
                      ? 'bg-white text-blue-600 shadow-sm' 
                      : 'text-gray-500 hover:text-gray-900'
                  ]"
                >
                  Menit (Test)
                </button>
              </div>
              
              <div class="relative pt-1 px-4">
                <input 
                  v-model="settings.retention_update_interval" 
                  type="range" 
                  min="1" 
                  :max="settings.retention_update_unit === 'minutes' ? 60 : 48" 
                  step="1"
                  class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600"
                >
                <div class="flex justify-between text-[10px] text-gray-400 mt-4 px-1">
                  <span>1 {{ settings.retention_update_unit === 'minutes' ? 'Menit' : 'Jam' }}</span>
                  <span v-if="settings.retention_update_unit === 'minutes'">30 Menit</span>
                  <span v-else>24 Jam (Default)</span>
                  <span>{{ settings.retention_update_unit === 'minutes' ? '60 Menit' : '48 Jam' }}</span>
                </div>
              </div>

              <div class="mt-8 p-4 bg-blue-50/50 rounded-xl border border-blue-100 flex gap-4">
                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-blue-500 shadow-sm flex-shrink-0">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="text-[11px] text-blue-800 leading-relaxed">
                  <p class="font-bold mb-1">Informasi:</p>
                  <p>Sistem akan menjalankan tugas sinkronisasi setiap <span class="font-bold underline">{{ settings.retention_update_interval }} {{ settings.retention_update_unit === 'minutes' ? 'menit' : 'jam' }}</span> sekali. Ini mencakup pembaruan status retensi pasien dan otomatisasi data pemusnahan.</p>
                </div>
              </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-50">
              <div class="flex items-center gap-3 text-gray-500">
                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="text-[10px]">
                  <p class="font-medium">Update Terakhir:</p>
                  <p class="text-gray-400">{{ settings.last_retention_update || 'Belum pernah dijalankan (Running in background)' }}</p>
                </div>
              </div>
              
              <button 
                @click="saveSettings" 
                :disabled="loading"
                class="px-8 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-100 hover:bg-blue-700 active:scale-95 transition-all disabled:opacity-50 flex items-center gap-2"
              >
                <div v-if="loading" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                <span v-if="loading">Menyimpan...</span>
                <span v-else>Simpan Konfigurasi</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Section: Developer Tools & Demo -->
      <div class="space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-gray-800 flex items-center gap-2">
              <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
              Developer Tools & Demo
            </h2>
          </div>
          
          <div class="p-8">
            <div class="flex items-start justify-between">
              <div>
                <h3 class="text-sm font-semibold text-gray-800 mb-1">Mock AI Interceptor</h3>
                <p class="text-xs text-gray-500 max-w-sm">
                  Gunakan hasil ekstraksi JSON (mock data) untuk demo jika nama file yang diunggah cocok dengan mapping. Menghemat kuota API Gemini dan memercepat proses.
                </p>
              </div>
              
              <!-- Toggle Switch -->
              <button 
                type="button" 
                @click="settings.mock_ai_interceptor = !settings.mock_ai_interceptor"
                :class="settings.mock_ai_interceptor ? 'bg-purple-600' : 'bg-gray-200'"
                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-offset-2"
              >
                <span 
                  :class="settings.mock_ai_interceptor ? 'translate-x-5' : 'translate-x-0'"
                  class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                ></span>
              </button>
            </div>

            <div class="mt-6 p-4 bg-purple-50/50 rounded-xl border border-purple-100 flex gap-4">
              <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-purple-500 shadow-sm flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              </div>
              <div class="text-[11px] text-purple-800 leading-relaxed">
                <p class="font-bold mb-1">Cara Kerja Mock:</p>
                <p>Jika fitur ini <span class="font-bold">ON</span>, upload file PDF seperti <code class="bg-purple-100 px-1 rounded">RM ERNA.pdf</code> akan mengembalikan JSON simulasi setelah delay 3-5 detik tanpa memanggil API AI asli. File tidak terdaftar tetap akan diproses oleh AI.</p>
              </div>
            </div>

            <div class="flex justify-end pt-6 border-t border-gray-50 mt-8">
              <button 
                @click="saveSettings" 
                :disabled="loading"
                class="px-8 py-2.5 bg-purple-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-purple-100 hover:bg-purple-700 active:scale-95 transition-all disabled:opacity-50 flex items-center gap-2"
              >
                <div v-if="loading" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                <span v-if="loading">Menyimpan...</span>
                <span v-else>Simpan Konfigurasi</span>
              </button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { showSuccessToast, showErrorToast } from '../utils/notification'

const settings = ref({
  retention_update_interval: 24,
  retention_update_unit: 'hours',
  last_retention_update: null,
  mock_ai_interceptor: false
})

const loading = ref(false)

const fetchSettings = async () => {
  try {
    const res = await axios.get('/api/settings')
    if (res.data.success) {
      settings.value = res.data.settings
    }
  } catch (err) {
    console.error('Failed to fetch settings', err)
  }
}

const saveSettings = async () => {
  loading.value = true
  try {
    const res = await axios.post('/api/settings', settings.value)
    if (res.data.success) {
      await showSuccessToast('Konfigurasi sistem berhasil diperbarui!')
      fetchSettings()
    }
  } catch (err) {
    await showErrorToast('Gagal menyimpan pengaturan: ' + (err.response?.data?.message || err.message))
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchSettings()
})
</script>

<style scoped>
/* Track Styling for Range Input */
input[type="range"]::-webkit-slider-runnable-track {
  background: #e5e7eb;
  height: 0.5rem;
  border-radius: 9999px;
}

input[type="range"]::-webkit-slider-thumb {
  margin-top: -0.25rem;
  -webkit-appearance: none;
  background: #2563eb;
  height: 1rem;
  width: 1rem;
  border-radius: 9999px;
  box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
}
</style>
