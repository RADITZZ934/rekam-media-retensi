<template>
  <div class="p-8 bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-2">Transaksi Log Aktivitas</h1>
      <p class="text-gray-600">Riwayat aktivitas pengguna sistem</p>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Cari user, modul, aksi, atau deskripsi..."
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
      />
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
      <table class="w-full min-w-[800px]">
        <!-- Table Header -->
        <thead class="bg-blue-600 text-white">
          <tr class="text-[10px] font-bold uppercase tracking-widest">
            <th class="px-6 py-4 text-left w-12">No</th>
            <th class="px-6 py-4 text-left">Waktu</th>
            <th class="px-6 py-4 text-left">Nama User</th>
            <th class="px-6 py-4 text-left">Modul</th>
            <th class="px-6 py-4 text-left">Aksi</th>
            <th class="px-6 py-4 text-left">Deskripsi</th>
            <th class="px-6 py-4 text-center w-12">Detail</th>
          </tr>
        </thead>

        <!-- Table Body -->
        <tbody class="divide-y divide-gray-200">
          <tr v-if="loading">
            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
              <div class="flex justify-center items-center gap-2">
                <div class="w-5 h-5 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                <span>Memuat data log aktivitas...</span>
              </div>
            </td>
          </tr>
          <tr v-else v-for="(log, idx) in filteredLogs" :key="idx" class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 text-xs text-gray-900 font-medium">{{ (currentPage - 1) * itemsPerPage + idx + 1 }}</td>
            <td class="px-6 py-4 text-xs text-gray-600 font-semibold">{{ log.waktu }}</td>
            <td class="px-6 py-4 text-xs text-blue-600 font-medium">{{ log.namaUser }}</td>
            <td class="px-6 py-4 text-xs text-gray-700 font-medium">{{ log.modul }}</td>
            <td class="px-6 py-4 text-xs text-gray-700 font-semibold">{{ log.aksi }}</td>
            <td class="px-6 py-4 text-xs text-gray-700 max-w-xs truncate">{{ log.deskripsi }}</td>
            <td class="px-6 py-4 text-center">
              <button
                @click="showDetail(log)"
                class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 flex items-center justify-center transition-all duration-200 cursor-pointer mx-auto"
                title="Detail"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Empty State -->
      <div v-if="!loading && filteredLogs.length === 0" class="px-6 py-12 text-center">
        <p class="text-gray-500">Tidak ada data log aktivitas</p>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="filteredLogs.length > 0" class="mt-6 flex justify-center items-center gap-2">
      <button 
        @click="currentPage = Math.max(1, currentPage - 1)"
        :disabled="currentPage === 1"
        class="px-3 py-1 border border-gray-300 rounded disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-100 text-sm"
      >
        ←
      </button>
      
      <div class="flex gap-1">
        <button
          v-for="page in totalPages"
          :key="page"
          @click="currentPage = page"
          :class="currentPage === page ? 'bg-blue-600 text-white' : 'border border-gray-300 hover:bg-gray-100'"
          class="px-3 py-1 rounded text-sm transition-colors"
        >
          {{ page }}
        </button>
      </div>

      <button 
        @click="currentPage = Math.min(totalPages, currentPage + 1)"
        :disabled="currentPage === totalPages"
        class="px-3 py-1 border border-gray-300 rounded disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-100 text-sm"
      >
        →
      </button>
    </div>

    <!-- Detail Modal -->
    <div v-if="showDetailModal" @click.self="showDetailModal = false" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-4">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold text-gray-900">Detail Log Aktivitas</h2>
          <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div v-if="selectedLog" class="space-y-3">
          <div>
            <p class="text-sm text-gray-600">Waktu</p>
            <p class="font-medium text-gray-900">{{ selectedLog.waktu }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-600">Nama User</p>
            <p class="font-medium text-gray-900">{{ selectedLog.namaUser }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-600">Modul</p>
            <p class="font-medium text-gray-900">{{ selectedLog.modul }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-600">Aksi</p>
            <p class="font-medium text-gray-900">{{ selectedLog.aksi }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-600">Deskripsi</p>
            <p class="font-medium text-gray-900">{{ selectedLog.deskripsi }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-600">IP Address</p>
            <p class="font-mono text-gray-900 text-sm">{{ selectedLog.ipAddress }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-600">User Agent</p>
            <p class="font-mono text-gray-900 text-xs break-all">{{ selectedLog.userAgent }}</p>
          </div>
        </div>

        <div class="mt-6 flex justify-end">
          <button @click="showDetailModal = false" class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800 transition-colors text-sm">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const searchQuery = ref('')
const currentPage = ref(1)
const itemsPerPage = 10
const showDetailModal = ref(false)
const selectedLog = ref(null)

const loading = ref(false)
const logs = ref([])

const fetchLogs = async () => {
  loading.value = true
  try {
    const res = await axios.get('/api/activity-logs')
    logs.value = res.data
  } catch (err) {
    console.error('Failed to fetch activity logs', err)
  } finally {
    loading.value = false
  }
}

// Filtered logs
const filteredLogs = computed(() => {
  let result = logs.value

  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase()
    result = result.filter(log =>
      (log.namaUser && log.namaUser.toLowerCase().includes(q)) ||
      (log.modul && log.modul.toLowerCase().includes(q)) ||
      (log.aksi && log.aksi.toLowerCase().includes(q)) ||
      (log.deskripsi && log.deskripsi.toLowerCase().includes(q))
    )
  }

  // Pagination
  const start = (currentPage.value - 1) * itemsPerPage
  return result.slice(start, start + itemsPerPage)
})

const totalPages = computed(() => {
  let result = logs.value

  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase()
    result = result.filter(log =>
      (log.namaUser && log.namaUser.toLowerCase().includes(q)) ||
      (log.modul && log.modul.toLowerCase().includes(q)) ||
      (log.aksi && log.aksi.toLowerCase().includes(q)) ||
      (log.deskripsi && log.deskripsi.toLowerCase().includes(q))
    )
  }

  return Math.ceil(result.length / itemsPerPage)
})

// Helper functions
const showDetail = (log) => {
  selectedLog.value = log
  showDetailModal.value = true
}

onMounted(() => {
  const authUserStr = localStorage.getItem('auth_user')
  if (authUserStr) {
    const user = JSON.parse(authUserStr)
    if (user.role !== 'Administrator') {
      router.push('/')
    } else {
      fetchLogs()
    }
  } else {
    router.push('/login')
  }
})
</script>
