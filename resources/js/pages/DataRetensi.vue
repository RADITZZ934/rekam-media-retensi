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
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <!-- Cari Pasien -->
        <div>
          <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Cari Pasien</label>
          <div class="relative">
            <input
              v-model="searchText"
              type="text"
              placeholder="No. RM atau Nama Pasien"
              class="w-full pl-3 pr-10 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs font-semibold"
              @keyup.enter="handleSearch"
            />
          </div>
        </div>

        <!-- Filter Status -->
        <div>
          <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Status</label>
          <select
            v-model="filterStatus"
            class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs font-semibold cursor-pointer"
            @change="handleSearch"
          >
            <option value="">Semua Status</option>
            <option value="Aktif">Aktif</option>
            <option value="Inaktif">Inaktif</option>
            <option value="Siap Dimusnahkan">Siap Dimusnahkan</option>
            <option value="Dimusnahkan">Dimusnahkan</option>
          </select>
        </div>

        <!-- Filter Tahun -->
        <div>
          <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tahun Kunjungan</label>
          <select
            v-model="filterTahun"
            class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs font-semibold cursor-pointer"
            @change="handleSearch"
          >
            <option value="">Semua Tahun</option>
            <option v-for="year in tahunList" :key="year" :value="year">{{ year }}</option>
          </select>
        </div>

        <!-- Filter Kasus -->
        <div>
          <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Kasus Medis</label>
          <select
            v-model="filterKasusId"
            class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs font-semibold cursor-pointer"
            @change="handleSearch"
          >
            <option value="">Semua Kasus</option>
            <option v-for="kasus in kasusList" :key="kasus.id" :value="kasus.id">{{ kasus.nama_kasus }}</option>
          </select>
        </div>
      </div>
      
      <!-- Reset & Search buttons below or aligned -->
      <div class="flex justify-end gap-3 mt-4">
        <button
          @click="resetFilters"
          class="px-5 py-2 border border-gray-200 text-gray-500 hover:text-gray-800 hover:bg-gray-50 rounded-xl text-xs font-bold transition-all cursor-pointer"
        >
          Reset
        </button>
        <button
          @click="handleSearch"
          class="px-6 py-2 bg-[#2b3c5a] hover:bg-[#1f2e47] text-white rounded-xl text-xs font-bold transition-all cursor-pointer shadow-sm"
        >
          Cari
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
      <table class="w-full border-collapse min-w-[900px]">
        <!-- Table Header -->
        <thead class="bg-blue-600 text-white">
          <tr class="text-[10px] font-bold uppercase tracking-widest">
            <th class="px-6 py-4 text-center w-20">No</th>
            <th class="px-6 py-4 text-left">Nama Pasien</th>
            <th class="px-6 py-4 text-left">No. RM</th>
            <th class="px-6 py-4 text-left">Jenis Kasus Medis</th>
            <th class="px-6 py-4 text-left">Tgl Batas Aktif</th>
            <th class="px-6 py-4 text-left">Tgl Batas Musnah</th>
            <th class="px-6 py-4 text-left">Status</th>
            <th class="px-6 py-4 text-center">Aksi</th>
          </tr>
        </thead>

        <!-- Table Body -->
        <tbody class="divide-y divide-gray-100">
          <tr v-for="(retensi, index) in retensiList" :key="retensi.id" class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 text-xs text-center text-gray-700">{{ (currentPage - 1) * perPage + index + 1 }}</td>
            <td class="px-6 py-4 text-xs font-medium text-gray-900">{{ retensi.nama_pasien }}</td>
            <td class="px-6 py-4 text-xs text-gray-700">{{ retensi.no_rm }}</td>
            <td class="px-6 py-4 text-xs text-gray-700">{{ retensi.nama_kasus }}</td>
            <td class="px-6 py-4 text-xs text-gray-700">{{ retensi.tanggal_batas_aktif }}</td>
            <td class="px-6 py-4 text-xs text-gray-700">{{ retensi.tanggal_batas_musnah }}</td>
            <td class="px-6 py-4 text-xs">
              <span
                :class="[
                  'px-3 py-1 rounded-full text-xs font-semibold',
                  retensi.status === 'Aktif'
                    ? 'bg-green-100 text-green-800'
                    : retensi.status === 'Inaktif'
                    ? 'bg-yellow-100 text-yellow-800'
                    : 'bg-red-100 text-red-800'
                ]"
              >
                {{ retensi.status }}
              </span>
            </td>
            <td class="px-6 py-4 text-center">
              <div class="flex gap-2 justify-center">

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
              </div>
            </td>
          </tr>
          <!-- Empty State -->
          <tr v-if="retensiList.length === 0">
            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
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
      @saved="fetchRetensi"
    />
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import FormRetensi from '../components/FormRetensi.vue';
import { showSuccessToast, showErrorToast, showInfoToast, showConfirmDialog } from '../utils/notification';

export default {
  name: 'DataRetensi',
  components: {
    FormRetensi,
  },
  setup() {
    const router = useRouter();
    const retensiList = ref([]);
    const totalRetensi = ref(0);
    const currentPage = ref(1);
    const perPage = ref(10);
    const searchText = ref('');
    const filterStatus = ref('');
    const filterKategori = ref('');
    const filterTahun = ref('');
    const filterKasusId = ref('');
    const kategoriList = ref([]);
    const tahunList = ref([]);
    const kasusList = ref([]);
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

        if (searchText.value) {
          params.append('search', searchText.value);
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
        if (filterKasusId.value) {
          params.append('kasus_id', filterKasusId.value);
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

    const fetchKasus = async () => {
      try {
        const response = await fetch('/api/kasus?per_page=100');
        const data = await response.json();
        kasusList.value = data.data || [];
      } catch (error) {
        console.error('Error fetching kasus:', error);
      }
    };

    const resetFilters = () => {
      searchText.value = '';
      filterStatus.value = '';
      filterKategori.value = '';
      filterTahun.value = '';
      filterKasusId.value = '';
      currentPage.value = 1;
      fetchRetensi();
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
        router.push({ name: 'pemusnahan' });
      }
    };

    onMounted(() => {
      fetchRetensi();
      fetchSummary();
      fetchKategori();
      fetchTahun();
      fetchKasus();
    });

    return {
      retensiList,
      totalRetensi,
      currentPage,
      perPage,
      searchText,
      filterStatus,
      filterKategori,
      filterTahun,
      filterKasusId,
      kategoriList,
      tahunList,
      kasusList,
      loading,
      summary,
      showDetailModal,
      selectedRetensi,
      resetFilters,
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
