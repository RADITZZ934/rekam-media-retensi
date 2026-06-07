<template>
  <div class="min-h-screen bg-gray-50 p-8">
    <!-- Header -->
    <div class="mb-4">
      <h1 class="text-2xl font-bold text-gray-800">Transaksi Retensi</h1>
    </div>

    <!-- Actions Row -->
    <div class="mb-4 flex justify-end">
      <button
        @click="hitungUlang"
        :disabled="loading"
        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-semibold flex items-center gap-2 transition-all shadow-sm disabled:opacity-50"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        {{ loading ? 'Memproses...' : 'Retensi Otomatis' }}
      </button>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
      <div class="flex flex-col md:flex-row gap-6 items-end">
        <!-- Nomor RM -->
        <div class="flex-1">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor RM</label>
          <input
            v-model="searchNoRm"
            type="text"
            placeholder="Masukkan nomor RM"
            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
          />
        </div>

        <!-- Nama Pasien -->
        <div class="flex-1">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Pasien</label>
          <input
            v-model="searchNamaPasien"
            type="text"
            placeholder="Masukkan nama pasien"
            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
          />
        </div>

        <!-- Search Button -->
        <button
          @click="handleSearch"
          class="px-8 py-2.5 bg-gray-700 hover:bg-gray-800 text-white rounded-lg font-semibold flex items-center justify-center gap-2 transition-colors min-w-[200px]"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          Search
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="w-full border-collapse">
        <!-- Table Header -->
        <thead class="bg-blue-600 text-white">
          <tr class="text-sm font-semibold">
            <th class="px-6 py-4 text-center w-20">No</th>
            <th class="px-6 py-4 text-left">Nama Pasien</th>
            <th class="px-6 py-4 text-left">No. RM</th>
            <th class="px-6 py-4 text-left">Jenis Kelamin</th>
            <th class="px-6 py-4 text-left">Alamat</th>
            <th class="px-6 py-4 text-left">Last Update</th>
            <th class="px-6 py-4 text-center">Aksi</th>
          </tr>
        </thead>

        <!-- Table Body -->
        <tbody class="divide-y divide-gray-100">
          <tr v-for="(retensi, index) in retensiList" :key="retensi.id" class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 text-sm text-center text-gray-700">{{ (currentPage - 1) * perPage + index + 1 }}</td>
            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ retensi.nama_pasien }}</td>
            <td class="px-6 py-4 text-sm text-gray-700">{{ retensi.no_rm }}</td>
            <td class="px-6 py-4 text-sm text-gray-700">{{ retensi.jenis_kelamin }}</td>
            <td class="px-6 py-4 text-sm text-gray-700 truncate max-w-xs">{{ retensi.alamat }}</td>
            <td class="px-6 py-4 text-sm text-gray-700">{{ retensi.last_update }}</td>
            <td class="px-6 py-4 text-center">
              <div class="flex gap-2 justify-center">
                <!-- View -->
                <button
                  @click="openDetail(retensi)"
                  class="p-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors"
                  title="Detail"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>
                <!-- Edit -->
                <button
                  @click="openDetail(retensi)"
                  class="p-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors"
                  title="Edit"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </button>
                <!-- Delete -->
                <button
                  @click="tapMusnah(retensi)"
                  class="p-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors"
                  title="Delete"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </td>
          </tr>
          <!-- Empty State -->
          <tr v-if="retensiList.length === 0">
            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
              Tidak ada data retensi
            </td>
          </tr>
        </tbody>
      </table>
    </div>

      <!-- Pagination -->
      <div class="bg-gray-50 px-6 py-4 flex items-center justify-between border-t border-gray-200">
        <div class="text-sm text-gray-600">
          Menampilkan 1 sampai {{ retensiList.length }} dari {{ totalRetensi }} hasil
        </div>
        <div class="flex gap-2">
          <button
            @click="prevPage"
            :disabled="currentPage === 1"
            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
          >
            Previous
          </button>
          <div class="flex items-center gap-1">
            <button
              v-for="page in pageNumbers"
              :key="page"
              @click="goToPage(page)"
              :class="[
                'px-3 py-2 rounded-lg text-sm font-medium',
                currentPage === page
                  ? 'bg-blue-600 text-white'
                  : 'border border-gray-300 hover:bg-gray-50'
              ]"
            >
              {{ page }}
            </button>
          </div>
          <button
            @click="nextPage"
            :disabled="currentPage >= totalPages"
            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
          >
            Next
          </button>
        </div>
      </div>

    <!-- Detail Modal -->
    <FormRetensi
      v-if="showDetailModal"
      :retensi="selectedRetensi"
      @close="closeDetail"
    />
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import FormRetensi from '../components/FormRetensi.vue';
import { showSuccessToast, showErrorToast, showInfoToast, showConfirmDialog } from '../utils/notification';

export default {
  name: 'DataRetensi',
  components: {
    FormRetensi,
  },
  setup() {
    const retensiList = ref([]);
    const totalRetensi = ref(0);
    const currentPage = ref(1);
    const perPage = ref(10);
    const searchNoRm = ref('');
    const searchNamaPasien = ref('');
    const filterStatus = ref('');
    const filterKategori = ref('');
    const filterTahun = ref('');
    const kategoriList = ref([]);
    const tahunList = ref([]);
    const loading = ref(false);
    const showDetailModal = ref(false);
    const selectedRetensi = ref(null);
    const summary = ref({
      aktif: 0,
      inaktif: 0,
      siapMusnah: 0,
    });

    const totalPages = computed(() => Math.ceil(totalRetensi.value / perPage.value));
    const pageNumbers = computed(() => {
      const pages = [];
      const maxVisible = 5;
      const half = Math.floor(maxVisible / 2);

      let start = Math.max(1, currentPage.value - half);
      let end = Math.min(totalPages.value, start + maxVisible - 1);

      if (end - start < maxVisible - 1) {
        start = Math.max(1, end - maxVisible + 1);
      }

      for (let i = start; i <= end; i++) {
        pages.push(i);
      }
      return pages;
    });

    const fetchRetensi = async () => {
      loading.value = true;
      try {
        const params = new URLSearchParams({
          page: currentPage.value,
          per_page: perPage.value,
        });

        if (searchNoRm.value) {
          params.append('search', searchNoRm.value);
        }
        if (searchNamaPasien.value) {
          params.append('search', searchNamaPasien.value);
        }
        if (filterStatus.value) {
          params.append('status', filterStatus.value);
        }
        if (filterKategori.value) {
          params.append('kategori', filterKategori.value);
        }
        if (filterTahun.value) {
          params.append('tahun', filterTahun.value);
        }

        const response = await fetch(`/api/retensi?${params}`);
        const data = await response.json();

        retensiList.value = data.data || [];
        totalRetensi.value = data.total || 0;
        currentPage.value = data.current_page || 1;
      } catch (error) {
        console.error('Error fetching retensi:', error);
        await showErrorToast('Gagal memuat data retensi');
      } finally {
        loading.value = false;
      }
    };

    const fetchSummary = async () => {
      try {
        const response = await fetch('/api/retensi/summary');
        const data = await response.json();

        if (data.success) {
          summary.value = data.summary;
        }
      } catch (error) {
        console.error('Error fetching summary:', error);
      }
    };

    const fetchKategori = async () => {
      try {
        const response = await fetch('/api/retensi/kategori/list');
        const data = await response.json();
        kategoriList.value = data || [];
      } catch (error) {
        console.error('Error fetching kategori:', error);
      }
    };

    const fetchTahun = async () => {
      try {
        const response = await fetch('/api/retensi/tahun/list');
        const data = await response.json();
        tahunList.value = data || [];
      } catch (error) {
        console.error('Error fetching tahun:', error);
      }
    };

    const handleSearch = () => {
      currentPage.value = 1;
      fetchRetensi();
    };

    const applyFilters = () => {
      currentPage.value = 1;
      fetchRetensi();
    };

    const prevPage = () => {
      if (currentPage.value > 1) {
        currentPage.value--;
        fetchRetensi();
      }
    };

    const nextPage = () => {
      if (currentPage.value < totalPages.value) {
        currentPage.value++;
        fetchRetensi();
      }
    };

    const goToPage = (page) => {
      currentPage.value = page;
      fetchRetensi();
    };

    const hitungUlang = async () => {
      const result = await showConfirmDialog(
        'Hitung Ulang Retensi?',
        'Sistem akan menghitung ulang retensi untuk semua pasien. Proses ini memerlukan beberapa saat.',
        'Ya, Hitung Ulang',
        'Batal',
        '#2563eb'
      );

      if (!result.isConfirmed) {
        return;
      }

      loading.value = true;

      try {
        const response = await fetch('/api/retensi/hitung-ulang', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
          },
        });

        const data = await response.json();

        if (data.success) {
          await showSuccessToast(data.message);
          // Refresh data
          currentPage.value = 1;
          fetchRetensi();
          fetchSummary();
        } else {
          await showErrorToast(data.message || 'Gagal menghitung ulang retensi');
        }
      } catch (error) {
        console.error('Error calculating retensi:', error);
        await showErrorToast(error.message || 'Terjadi kesalahan saat menghitung retensi');
      } finally {
        loading.value = false;
      }
    };

    const openDetail = (retensi) => {
      selectedRetensi.value = retensi;
      showDetailModal.value = true;
    };

    const closeDetail = () => {
      showDetailModal.value = false;
      selectedRetensi.value = null;
    };

    const tapMusnah = async (retensi) => {
      const result = await showConfirmDialog(
        'Proses Pemusnahan?',
        `Pasien ${retensi.nama_pasien} (${retensi.no_rm}) akan diproses untuk pemusnahan. Lanjutkan ke halaman pemusnahan?`,
        'Ya, Lanjutkan',
        'Batal',
        '#dc2626'
      );

      if (result.isConfirmed) {
        await showInfoToast('Akan dialihkan ke halaman pemusnahan dokumen (fitur dalam pengembangan)');
      }
    };

    onMounted(() => {
      fetchRetensi();
      fetchSummary();
      fetchKategori();
      fetchTahun();
    });

    return {
      retensiList,
      totalRetensi,
      currentPage,
      perPage,
      searchNoRm,
      searchNamaPasien,
      filterStatus,
      filterKategori,
      filterTahun,
      kategoriList,
      tahunList,
      loading,
      summary,
      showDetailModal,
      selectedRetensi,
      totalPages,
      pageNumbers,
      fetchRetensi,
      handleSearch,
      applyFilters,
      prevPage,
      nextPage,
      goToPage,
      hitungUlang,
      openDetail,
      closeDetail,
      tapMusnah,
    };
  },
};
</script>

<style scoped>
/* No additional styles needed - using Tailwind CSS */
</style>
