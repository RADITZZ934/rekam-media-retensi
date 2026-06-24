<template>
  <div class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 animate-fade-in" @click="$emit('close')"></div>

    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen px-4 py-6">
      <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full p-6 relative z-50 border border-gray-100 animate-scale-in">
        <!-- Close Button -->
        <button
          @click="$emit('close')"
          class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors cursor-pointer"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>

        <!-- Title -->
        <div v-if="retensi" class="mb-5 text-left">
          <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit Retensi: {{ retensi.nama_pasien }}
          </h2>
          <p class="text-xs text-gray-500 font-semibold mt-1">
            No RM: {{ retensi.no_rm }} &nbsp;•&nbsp; Kasus: {{ retensi.nama_kasus }} &nbsp;•&nbsp; Layanan: {{ retensi.jenis_layanan }}
          </p>
        </div>

        <!-- Content -->
        <div v-if="retensi" class="space-y-5 text-left">
          <!-- Section: Form Sunting -->
          <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-[11px] text-gray-400 font-bold uppercase mb-1">Kunjungan Terakhir</label>
                <input
                  v-model="tanggalKunjungan"
                  type="date"
                  class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-shadow"
                />
              </div>
              <div>
                <label class="block text-[11px] text-gray-400 font-bold uppercase mb-1">Masa Aktif (Tahun)</label>
                <input
                  v-model.number="masaAktif"
                  type="number"
                  min="0"
                  class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-shadow"
                />
              </div>
              <div>
                <label class="block text-[11px] text-gray-400 font-bold uppercase mb-1">Masa Inaktif (Tahun)</label>
                <input
                  v-model.number="masaInaktif"
                  type="number"
                  min="0"
                  class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-shadow"
                />
              </div>
            </div>
          </div>

          <!-- Section: Live Calculations & Status -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="border border-gray-100 rounded-xl p-4 space-y-3">
              <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Hasil Kalkulasi
              </h4>
              <div class="grid grid-cols-2 gap-3 text-xs">
                <div>
                  <p class="text-[10px] text-gray-400 font-semibold uppercase">Batas Aktif</p>
                  <p class="text-gray-900 font-bold mt-0.5">{{ tanggalBatasAktif }}</p>
                </div>
                <div>
                  <p class="text-[10px] text-gray-400 font-semibold uppercase">Batas Musnah</p>
                  <p class="text-gray-900 font-bold mt-0.5">{{ tanggalBatasMusnah }}</p>
                </div>
                <div class="col-span-2">
                  <p class="text-[10px] text-gray-400 font-semibold uppercase">Keterangan Waktu</p>
                  <p class="text-gray-700 font-medium mt-0.5">
                    {{ selisihTahun }} tahun <span class="text-gray-400 font-normal">({{ selisihHari }} hari sejak kunjungan)</span>
                  </p>
                </div>
              </div>
            </div>

            <!-- Status Card -->
            <div class="rounded-xl p-4 border transition-all duration-300 flex flex-col justify-between" :class="[
              computedStatus === 'Aktif'
                ? 'bg-green-50/70 border-green-200/40 text-green-800'
                : computedStatus === 'Inaktif'
                ? 'bg-yellow-50/70 border-yellow-200/40 text-yellow-800'
                : 'bg-red-50/70 border-red-200/40 text-red-800'
            ]">
              <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider">Status Retensi</span>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-white border border-gray-200 shadow-sm" :class="[
                  computedStatus === 'Aktif'
                    ? 'text-green-700'
                    : computedStatus === 'Inaktif'
                    ? 'text-yellow-700'
                    : 'text-red-700'
                ]">
                  <span class="w-1.5 h-1.5 rounded-full animate-pulse" :class="[
                    computedStatus === 'Aktif'
                      ? 'bg-green-500'
                      : computedStatus === 'Inaktif'
                      ? 'bg-yellow-500'
                      : 'bg-red-500'
                  ]"></span>
                  {{ computedStatus }}
                </span>
              </div>
              <p class="text-[11px] leading-relaxed mt-2 opacity-80">
                {{ countdownText }}
              </p>
            </div>
          </div>
        </div>

        <!-- Footer Actions -->
        <div class="flex gap-3 pt-4 border-t border-gray-200 mt-5">
          <button
            @click="$emit('close')"
            :disabled="saving"
            class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 font-semibold transition-colors disabled:opacity-50 cursor-pointer"
          >
            Batal
          </button>
          <button
            @click="saveChanges"
            :disabled="saving"
            class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg font-semibold flex items-center justify-center gap-1.5 transition-colors disabled:bg-blue-400 cursor-pointer shadow-sm shadow-blue-500/10 hover:shadow-blue-500/25"
          >
            <svg v-if="saving" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            {{ saving ? 'Menyimpan...' : 'Simpan Perubahan' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { showErrorToast, showSuccessToast } from '../utils/notification'

const props = defineProps({
  retensi: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['close', 'saved'])

const parseDateToYmd = (dateStr) => {
  if (!dateStr || dateStr === '-') return ''
  const parts = dateStr.split('/')
  if (parts.length === 3) {
    return `${parts[2]}-${parts[1]}-${parts[0]}`
  }
  return dateStr
}

const formatDateDmy = (date) => {
  if (!(date instanceof Date) || isNaN(date)) return '-'
  const day = String(date.getDate()).padStart(2, '0')
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const year = date.getFullYear()
  return `${day}/${month}/${year}`
}

const tanggalKunjungan = ref('')
const masaAktif = ref(5)
const masaInaktif = ref(2)
const originalStatus = ref('Aktif')
const saving = ref(false)

onMounted(() => {
  if (props.retensi) {
    tanggalKunjungan.value = parseDateToYmd(props.retensi.tanggal_kunjungan_terakhir)
    masaAktif.value = props.retensi.masa_aktif
    masaInaktif.value = props.retensi.masa_inaktif
    originalStatus.value = props.retensi.status
  }
})

// Live calculations for date bounds and countdown
const selisihHari = computed(() => {
  if (!tanggalKunjungan.value) return 0
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  const kunjungan = new Date(tanggalKunjungan.value)
  kunjungan.setHours(0, 0, 0, 0)
  const diffTime = Math.abs(today - kunjungan)
  return Math.floor(diffTime / (1000 * 60 * 60 * 24))
})

const selisihTahun = computed(() => {
  return Math.floor(selisihHari.value / 365)
})

const tanggalBatasAktif = computed(() => {
  if (!tanggalKunjungan.value) return '-'
  const d = new Date(tanggalKunjungan.value)
  d.setFullYear(d.getFullYear() + Number(masaAktif.value))
  return formatDateDmy(d)
})

const tanggalBatasMusnah = computed(() => {
  if (!tanggalKunjungan.value) return '-'
  const d = new Date(tanggalKunjungan.value)
  d.setFullYear(d.getFullYear() + Number(masaAktif.value) + Number(masaInaktif.value))
  return formatDateDmy(d)
})

// Computed status based on tanggalKunjungan, masaAktif, and masaInaktif
const computedStatus = computed(() => {
  if (originalStatus.value === 'Dimusnahkan') {
    return 'Dimusnahkan'
  }
  
  if (!tanggalKunjungan.value) return 'Aktif'
  
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  
  const dAktif = new Date(tanggalKunjungan.value)
  dAktif.setFullYear(dAktif.getFullYear() + Number(masaAktif.value))
  dAktif.setHours(0, 0, 0, 0)
  
  const dMusnah = new Date(tanggalKunjungan.value)
  dMusnah.setFullYear(dMusnah.getFullYear() + Number(masaAktif.value) + Number(masaInaktif.value))
  dMusnah.setHours(0, 0, 0, 0)
  
  if (today < dAktif) {
    return 'Aktif'
  } else if (today < dMusnah) {
    return 'Inaktif'
  } else {
    return 'Siap Dimusnahkan'
  }
})

const countdownText = computed(() => {
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  
  if (computedStatus.value === 'Aktif' && tanggalBatasAktif.value !== '-') {
    const parts = tanggalBatasAktif.value.split('/')
    const batas = new Date(parts[2], parts[1] - 1, parts[0])
    if (today < batas) {
      const diff = Math.floor((batas - today) / (1000 * 60 * 60 * 24))
      return `${diff} hari lagi sebelum menjadi Inaktif`
    } else {
      return 'Melewati batas aktif (Seharusnya sudah Inaktif)'
    }
  }

  if (computedStatus.value === 'Inaktif' && tanggalBatasMusnah.value !== '-') {
    const parts = tanggalBatasMusnah.value.split('/')
    const batas = new Date(parts[2], parts[1] - 1, parts[0])
    if (today < batas) {
      const diff = Math.floor((batas - today) / (1000 * 60 * 60 * 24))
      return `${diff} hari lagi sebelum Siap Dimusnahkan`
    } else {
      return 'Melewati batas musnah (Seharusnya sudah Siap Dimusnahkan)'
    }
  }

  if (computedStatus.value === 'Siap Dimusnahkan') {
    return 'Siap untuk dieksekusi pemusnahan'
  }

  if (computedStatus.value === 'Dimusnahkan') {
    return 'Dokumen sudah dimusnahkan'
  }

  return '-'
})

const saveChanges = async () => {
  if (!tanggalKunjungan.value) {
    showErrorToast('Tanggal kunjungan terakhir wajib diisi.')
    return
  }

  saving.value = true
  try {
    const response = await fetch(`/api/retensi/${props.retensi.id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
      },
      body: JSON.stringify({
        tanggal_kunjungan_terakhir: tanggalKunjungan.value,
        masa_aktif: Number(masaAktif.value),
        masa_inaktif: Number(masaInaktif.value),
        status: computedStatus.value,
      }),
    })

    const res = await response.json()
    if (response.ok && res.success) {
      showSuccessToast('Data retensi berhasil diperbarui.')
      emit('saved')
      emit('close')
    } else {
      showErrorToast(res.message || 'Gagal memperbarui data retensi.')
    }
  } catch (err) {
    console.error('Update retensi error:', err)
    showErrorToast('Terjadi kesalahan jaringan atau server.')
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes scaleIn {
  from { transform: scale(0.95); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}

.animate-fade-in {
  animation: fadeIn 0.2s ease-out forwards;
}

.animate-scale-in {
  animation: scaleIn 0.25s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}
</style>
