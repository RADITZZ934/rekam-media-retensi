<template>
  <div class="min-h-screen bg-gray-50/30 p-6 md:p-8 animate-fade-in">
    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <div class="flex items-center gap-2 text-[10px] font-bold text-blue-600 uppercase tracking-widest mb-1">
          <span>Laporan Analisis</span>
          <span class="w-1 h-1 rounded-full bg-blue-600"></span>
          <span>Alih Media</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Laporan Alih Media & Digitalisasi</h1>
      </div>
      
      <!-- Export Action -->
      <button
        @click="exportCsv"
        :disabled="loadingExport"
        class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 hover:bg-emerald-100/80 text-emerald-700 disabled:opacity-50 rounded-xl text-xs font-bold transition-all border border-emerald-200/40 shadow-sm cursor-pointer"
      >
        <svg v-if="loadingExport" class="animate-spin w-4 h-4 text-emerald-700" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        {{ loadingExport ? 'Mengekspor...' : 'Ekspor Laporan (CSV)' }}
      </button>
    </div>

    <!-- Compact Inline Filter Bar -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6 flex flex-col lg:flex-row items-stretch lg:items-center gap-3">
      <!-- Search Input -->
      <div class="relative flex-1">
        <input
          v-model="filters.search"
          type="text"
          placeholder="Cari No. RM atau nama file..."
          class="w-full pl-9 pr-4 py-2.5 bg-gray-50/50 border border-gray-200/80 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/10 focus:border-blue-500 text-xs font-semibold transition-all"
          @keyup.enter="handleSearch"
        />
        <svg class="absolute left-3 top-3 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </div>

      <!-- Date Filter -->
      <div class="w-full lg:w-52">
        <input
          v-model="filters.tanggal_upload"
          type="date"
          class="w-full px-4 py-2.5 bg-gray-50/50 border border-gray-200/80 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/10 focus:border-blue-500 text-xs font-semibold transition-all cursor-pointer"
          @change="handleFilterChange"
        />
      </div>

      <!-- Actions -->
      <div class="flex items-center gap-2">
        <button
          @click="resetFilters"
          class="px-4 py-2.5 border border-gray-200 text-gray-500 hover:text-gray-800 hover:bg-gray-50 rounded-xl text-xs font-bold transition-all cursor-pointer"
        >
          Reset
        </button>
        <button
          @click="handleSearch"
          class="px-5 py-2.5 bg-[#2b3c5a] hover:bg-[#1f2e47] text-white rounded-xl text-xs font-bold transition-all cursor-pointer shadow-sm shadow-gray-500/10"
        >
          Cari
        </button>
      </div>
    </div>

    <!-- Data Table Container -->
    <div class="bg-white rounded-2xl border border-gray-100/85 shadow-sm overflow-hidden flex flex-col justify-between min-h-[450px]">
      <div class="overflow-x-auto">
        <table class="w-full border-collapse min-w-[800px]">
          <!-- Table Header -->
          <thead class="bg-gray-50/40 border-b border-gray-100/60">
            <tr class="text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">
              <th class="px-6 py-4.5 text-center w-16">No</th>
              <th class="px-6 py-4.5">Nama Berkas</th>
              <th class="px-6 py-4.5">No. RM</th>
              <th class="px-6 py-4.5">Pengunggah</th>
              <th class="px-6 py-4.5">Tanggal Upload</th>
              <th class="px-6 py-4.5 text-center">Engine</th>
              <th class="px-6 py-4.5 text-center">Status</th>
              <th class="px-6 py-4.5 text-center w-24">Aksi</th>
            </tr>
          </thead>

          <!-- Table Body -->
          <tbody class="divide-y divide-gray-100/60">
            <tr 
              v-for="(item, idx) in listData" 
              :key="item.id" 
              class="hover:bg-gray-50/30 transition-colors"
            >
              <td class="px-6 py-4 text-xs text-center font-bold text-gray-400">
                {{ (pagination.current - 1) * pagination.perPage + idx + 1 }}
              </td>
              <td class="px-6 py-4 text-xs font-bold text-gray-900 max-w-xs truncate" :title="item.nama_file">
                {{ item.nama_file }}
              </td>
              <td class="px-6 py-4 text-xs font-bold text-gray-700">{{ item.no_rm }}</td>
              <td class="px-6 py-4 text-xs text-gray-600 font-medium">{{ item.user_name }}</td>
              <td class="px-6 py-4 text-xs text-gray-600 font-semibold">{{ item.tanggal_upload }}</td>
              <td class="px-6 py-4 text-xs text-center">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold border" :class="[
                  item.engine === 'gemini' 
                    ? 'bg-indigo-50 text-indigo-700 border-indigo-200/30' 
                    : item.engine === 'manual'
                    ? 'bg-gray-50 text-gray-700 border-gray-200/30'
                    : 'bg-amber-50 text-amber-700 border-amber-200/30'
                ]">
                  {{ item.engine ? item.engine.toUpperCase() : '-' }}
                </span>
              </td>
              <td class="px-6 py-4 text-xs text-center">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold inline-flex items-center gap-1 border" :class="[
                  item.status === 'validated'
                    ? 'bg-green-50/55 text-green-700 border-green-200/35'
                    : item.status === 'success'
                    ? 'bg-yellow-50/55 text-yellow-700 border-yellow-200/35'
                    : item.status === 'processing' || item.status === 'uploaded'
                    ? 'bg-blue-50/55 text-blue-700 border-blue-200/35'
                    : 'bg-red-50/55 text-red-700 border-red-200/35'
                ]">
                  <span class="w-1.5 h-1.5 rounded-full" :class="[
                    item.status === 'validated'
                      ? 'bg-green-500'
                      : item.status === 'success'
                      ? 'bg-yellow-500'
                      : item.status === 'processing' || item.status === 'uploaded'
                      ? 'bg-blue-500 animate-pulse'
                      : 'bg-red-500'
                  ]"></span>
                  {{ item.status.toUpperCase() }}
                </span>
              </td>
              <td class="px-6 py-4 text-center">
                <button
                  @click="viewDetail(item)"
                  class="p-1.5 bg-blue-50/70 hover:bg-blue-600 text-blue-600 hover:text-white rounded-lg transition-all duration-200 cursor-pointer"
                  title="Tinjau Berkas"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                  </svg>
                </button>
              </td>
            </tr>

            <!-- Empty State -->
            <tr v-if="listData.length === 0 && !loading">
              <td colspan="8" class="px-6 py-20 text-center">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h4 class="text-xs font-bold text-gray-700">Tidak ada berkas digitalisasi ditemukan</h4>
                <p class="text-[11px] text-gray-400 mt-1 font-medium">Ubah filter pencarian Anda atau unggah dokumen rekam medis di menu Alih Media.</p>
              </td>
            </tr>

            <!-- Loading Skeleton -->
            <tr v-if="loading" v-for="n in 5" :key="'skeleton-' + n" class="animate-pulse">
              <td colspan="8" class="px-6 py-5">
                <div class="h-4 bg-gray-100 rounded-lg w-full"></div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination Footer -->
      <div v-if="listData.length > 0" class="bg-gray-50/30 px-6 py-4.5 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-gray-100/60">
        <div class="text-[11px] font-semibold text-gray-400">
          Menampilkan <span class="text-gray-700">{{ (pagination.current - 1) * pagination.perPage + 1 }}</span> sampai <span class="text-gray-700">{{ Math.min(pagination.current * pagination.perPage, pagination.total) }}</span> dari <span class="text-gray-700">{{ pagination.total }}</span> hasil
        </div>
        <div class="flex gap-1">
          <button
            @click="goToPage(pagination.current - 1)"
            :disabled="pagination.current === 1"
            class="px-3.5 py-1.5 border border-gray-200 text-gray-500 hover:text-gray-800 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg text-xs font-bold transition-all"
          >
            Prev
          </button>
          
          <button
            v-for="page in totalPagesList"
            :key="page"
            @click="goToPage(page)"
            :class="[
              'px-3 py-1.5 rounded-lg text-xs font-bold transition-all',
              pagination.current === page
                ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/10'
                : 'border border-gray-200 text-gray-500 bg-white hover:bg-gray-50'
            ]"
          >
            {{ page }}
          </button>

          <button
            @click="goToPage(pagination.current + 1)"
            :disabled="pagination.current >= pagination.totalPages"
            class="px-3.5 py-1.5 border border-gray-200 text-gray-500 hover:text-gray-800 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg text-xs font-bold transition-all"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import CustomDropdown from '../components/CustomDropdown.vue'
import { showErrorToast, showSuccessToast } from '../utils/notification'

// Dropdown options
const statusOptions = [
  { value: '', label: 'Semua Status' },
  { value: 'uploaded', label: 'Status: Uploaded' },
  { value: 'processing', label: 'Status: Processing' },
  { value: 'success', label: 'Status: Success' },
  { value: 'validated', label: 'Status: Validated' },
  { value: 'failed', label: 'Status: Failed' }
]

const engineOptions = [
  { value: '', label: 'Semua Engine' },
  { value: 'gemini', label: 'Engine: Gemini AI' },
  { value: 'litellm', label: 'Engine: LiteLLM' },
  { value: 'manual', label: 'Engine: Manual' }
]

const router = useRouter()

// Data & State
const listData = ref([])
const loading = ref(false)
const loadingExport = ref(false)
const authUser = ref(null)

const metrics = reactive({
  uploaded: 0,
  processing: 0,
  success: 0,
  validated: 0,
  failed: 0,
  total: 0,
})

const filters = reactive({
  search: '',
  tanggal_upload: '',
})

const pagination = reactive({
  current: 1,
  perPage: 10,
  total: 0,
  totalPages: 1,
})

// Computed List of Pages
const totalPagesList = computed(() => {
  const pages = []
  const maxPages = 5
  const half = Math.floor(maxPages / 2)
  let start = Math.max(1, pagination.current - half)
  let end = Math.min(pagination.totalPages, start + maxPages - 1)

  if (end - start < maxPages - 1) {
    start = Math.max(1, end - maxPages + 1)
  }

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  return pages
})

// Methods
const fetchSummaryMetrics = async () => {
  try {
    const response = await fetch('/api/alih-media/summary')
    const res = await response.json()
    if (res.success && res.summary) {
      metrics.uploaded = res.summary.uploaded || 0
      metrics.processing = res.summary.processing || 0
      metrics.success = res.summary.success || 0
      metrics.validated = res.summary.validated || 0
      metrics.failed = res.summary.failed || 0
      metrics.total = res.summary.total || 0
    }
  } catch (error) {
    console.error('Error fetching summary metrics:', error)
  }
}

const fetchData = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams({
      page: pagination.current,
      per_page: pagination.perPage,
    })

    if (filters.search) params.append('search', filters.search)
    if (filters.tanggal_upload) params.append('tanggal_upload', filters.tanggal_upload)

    const response = await fetch(`/api/alih-media?${params}`)
    const res = await response.json()

    if (res.success) {
      listData.value = res.data || []
      pagination.total = res.total || 0
      pagination.current = res.current_page || 1
      pagination.totalPages = res.last_page || 1
    } else {
      showErrorToast('Gagal memuat data laporan alih media.')
    }
  } catch (error) {
    console.error('Error fetching alih-media list:', error)
    showErrorToast('Terjadi kesalahan koneksi saat memuat data.')
  } finally {
    loading.value = false
  }
}

const handleSearch = () => {
  pagination.current = 1
  fetchData()
}

const handleFilterChange = () => {
  pagination.current = 1
  fetchData()
}

const resetFilters = () => {
  filters.search = ''
  filters.tanggal_upload = ''
  pagination.current = 1
  fetchData()
}

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.totalPages) {
    pagination.current = page
    fetchData()
  }
}

// Redirect to OCR validation page or show error toast if failed
const viewDetail = (item) => {
  if (item.status === 'success') {
    router.push(`/validasi-ocr?id=${item.id}`)
  } else if (item.status === 'failed') {
    showErrorToast(`Proses Gagal: ${item.error_message || 'Terjadi kesalahan sistem'}`)
  } else {
    // Redirect to main alih media list
    router.push('/alih-media')
  }
}

// Export CSV trigger
const exportCsv = async () => {
  loadingExport.value = true
  try {
    const params = new URLSearchParams()
    
    if (filters.search) params.append('search', filters.search)
    if (filters.tanggal_upload) params.append('tanggal_upload', filters.tanggal_upload)
    if (authUser.value && authUser.value.username) {
      params.append('username', authUser.value.username)
    }

    const url = `/api/alih-media/export?${params.toString()}`
    
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', '')
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    
    showSuccessToast('Proses unduh laporan dimulai.')
  } catch (error) {
    console.error('CSV Export failed:', error)
    showErrorToast('Gagal mengekspor laporan alih media.')
  } finally {
    loadingExport.value = false
  }
}

// Lifecycle Hooks
onMounted(() => {
  const stored = localStorage.getItem('auth_user')
  if (stored) {
    try {
      authUser.value = JSON.parse(stored)
    } catch (e) {
      console.error(e)
    }
  }
  fetchSummaryMetrics()
  fetchData()
})
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(4px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
