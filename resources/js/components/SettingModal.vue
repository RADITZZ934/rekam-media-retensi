<template>
  <transition name="fade">
    <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-slide-up">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-blue-50/50">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
            <h3 class="font-bold text-gray-800">Advanced Settings</h3>
          </div>
          <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-6">
          <!-- Retention Update Interval -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center justify-between">
              <span>Interval Sinkronisasi Status</span>
              <span class="text-blue-600 bg-blue-50 px-2 py-0.5 rounded text-xs font-bold">
                {{ settings.retention_update_interval }} {{ settings.retention_update_unit === 'minutes' ? 'Menit' : 'Jam' }}
              </span>
            </label>

            <!-- Unit Selector -->
            <div class="flex bg-gray-100 p-1 rounded-lg mb-3 max-w-[160px]">
              <button 
                type="button"
                @click="settings.retention_update_unit = 'hours'"
                :class="[
                  'flex-1 py-1 text-[10px] font-semibold rounded transition-all',
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
                  'flex-1 py-1 text-[10px] font-semibold rounded transition-all',
                  settings.retention_update_unit === 'minutes' 
                    ? 'bg-white text-blue-600 shadow-sm' 
                    : 'text-gray-500 hover:text-gray-900'
                ]"
              >
                Menit (Test)
              </button>
            </div>

            <input 
              v-model="settings.retention_update_interval" 
              type="range" 
              min="1" 
              :max="settings.retention_update_unit === 'minutes' ? 60 : 48" 
              step="1"
              class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600"
            >
            <div class="flex justify-between text-[10px] text-gray-400 mt-1">
              <span>1 {{ settings.retention_update_unit === 'minutes' ? 'Menit' : 'Jam' }}</span>
              <span v-if="settings.retention_update_unit === 'minutes'">30 Menit</span>
              <span v-else>24 Jam (Default)</span>
              <span>{{ settings.retention_update_unit === 'minutes' ? '60 Menit' : '48 Jam' }}</span>
            </div>
            <p class="text-[11px] text-gray-500 mt-3 leading-relaxed">
              <span class="font-medium text-blue-600">Tip:</span> Menentukan seberapa sering sistem akan mengecek dan memperbarui status retensi pasien secara otomatis.
            </p>
          </div>

          <!-- Last Info -->
          <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
            <div class="flex items-center gap-3 text-gray-600">
              <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="text-[11px]">
                <p class="font-medium">Update Terakhir:</p>
                <p class="text-gray-400">{{ settings.last_retention_update || 'Belum pernah' }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">
          <button 
            @click="$emit('close')" 
            class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors"
          >
            Batal
          </button>
          <button 
            @click="saveSettings" 
            :disabled="loading"
            class="px-6 py-2 bg-blue-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 active:scale-95 transition-all disabled:opacity-50"
          >
            <span v-if="loading">Menyimpan...</span>
            <span v-else>Simpan Perubahan</span>
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps(['show'])
const emit = defineEmits(['close', 'saved'])

const settings = ref({
  retention_update_interval: 24,
  retention_update_unit: 'hours',
  last_retention_update: null
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
      alert('Pengaturan berhasil disimpan!')
      emit('saved')
      emit('close')
    }
  } catch (err) {
    alert('Gagal menyimpan pengaturan: ' + (err.response?.data?.message || err.message))
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  if (props.show) fetchSettings()
})
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.animate-slide-up {
  animation: slide-up 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes slide-up {
  from { opacity: 0; transform: translateY(20px) scale(0.95); }
  to { opacity: 1; transform: translateY(0) scale(1); }
}
</style>
