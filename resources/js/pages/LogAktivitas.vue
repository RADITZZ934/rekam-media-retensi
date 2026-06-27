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
        placeholder="Cari nama user"
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
      />
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="w-full">
        <!-- Table Header -->
        <thead class="bg-blue-600 text-white">
          <tr class="text-[10px] font-bold uppercase tracking-widest">
            <th class="px-6 py-4 text-left w-12">No</th>
            <th class="px-6 py-4 text-left">Nama User</th>
            <th class="px-6 py-4 text-left">Role</th>
            <th class="px-6 py-4 text-left">Login Terakhir</th>
            <th class="px-6 py-4 text-left">Logout Terakhir</th>
            <th class="px-6 py-4 text-left">Status</th>
            <th class="px-6 py-4 text-center w-12">Aksi</th>
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
            <td class="px-6 py-4 text-xs text-blue-600 font-medium">{{ log.namaUser }}</td>
            <td class="px-6 py-4 text-xs text-gray-700">{{ log.role }}</td>
            <td class="px-6 py-4 text-xs text-gray-700">{{ log.loginTerakhir }}</td>
            <td class="px-6 py-4 text-xs text-gray-700">{{ log.logoutTerakhir }}</td>
            <td class="px-6 py-4 text-xs">
              <span :class="log.status === 'Sedang Login' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-3 py-1 rounded-full text-[10px] font-medium">
                {{ log.status }}
              </span>
            </td>
            <td class="px-6 py-4 text-center">
              <button @click="showDetail(log)" class="p-2 bg-gray-700 text-white rounded hover:bg-gray-800 transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                  <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
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
          <h2 class="text-xl font-bold text-gray-900">Detail User</h2>
          <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div v-if="selectedLog" class="space-y-3">
          <div>
            <p class="text-sm text-gray-600">Nama User</p>
            <p class="font-medium text-gray-900">{{ selectedLog.namaUser }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-600">Role</p>
            <p class="font-medium text-gray-900">{{ selectedLog.role }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-600">Login Terakhir</p>
            <p class="font-mono text-gray-900">{{ selectedLog.loginTerakhir }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-600">Logout Terakhir</p>
            <p class="font-mono text-gray-900">{{ selectedLog.logoutTerakhir }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-600">Status</p>
            <p :class="selectedLog.status === 'Sedang Login' ? 'text-green-800' : 'text-red-800'" class="font-medium">{{ selectedLog.status }}</p>
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
      log.namaUser.toLowerCase().includes(q)
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
      log.namaUser.toLowerCase().includes(q)
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
