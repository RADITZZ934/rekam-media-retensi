<template>
  <div class="min-h-screen bg-gray-50 p-8">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Transaksi Pemusnahan</h1>
      <p class="text-gray-600 mt-2">Kelola proses pemusnahan arsip rekam medis</p>
    </div>

    <!-- Filter and Search Section -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <!-- Cari Pasien -->
        <div>
          <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Cari Pasien</label>
          <input
            v-model="searchText"
            type="text"
            placeholder="No RM atau Nama Pasien"
            class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs font-semibold"
            @keyup.enter="handleSearch"
          />
        </div>

        <!-- Status -->
        <div>
          <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Status</label>
          <select
            v-model="filterStatus"
            class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs font-semibold cursor-pointer"
            @change="handleSearch"
          >
            <option value="">Semua Status</option>
            <option value="menunggu_eksekusi">Menunggu Eksekusi</option>
            <option value="dimusnahkan">Dimusnahkan</option>
          </select>
        </div>

        <!-- Tahun -->
        <div>
          <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tahun</label>
          <select
            v-model="filterTahun"
            class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs font-semibold cursor-pointer"
            @change="handleSearch"
          >
            <option value="">Semua Tahun</option>
            <option v-for="year in getYears" :key="year" :value="year">{{ year }}</option>
          </select>
        </div>

        <!-- Kasus Medis -->
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

      <!-- Action buttons -->
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

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[800px]">
          <thead class="bg-blue-600 text-white">
            <tr class="text-[10px] font-bold uppercase tracking-widest">
              <th class="px-6 py-4 text-left">No</th>
              <th class="px-6 py-4 text-left">No RM</th>
              <th class="px-6 py-4 text-left">Nama Pasien</th>
              <th class="px-6 py-4 text-left">Tanggal Retensi</th>
              <th class="px-6 py-4 text-left">Status</th>
              <th class="px-6 py-4 text-left">Tgl Pemusnahan</th>
              <th class="px-6 py-4 text-left">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="pemusnahanList.length === 0" class="border-t border-gray-200 hover:bg-gray-50">
              <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Tidak ada data pemusnahan
              </td>
            </tr>
            <tr v-for="(pemusnahan, index) in pemusnahanList" :key="pemusnahan.id" class="border-t border-gray-200 hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4 text-xs text-gray-900 font-medium">{{ (currentPage - 1) * perPage + index + 1 }}</td>
              <td class="px-6 py-4 text-xs text-gray-900">
                <div class="font-medium">{{ pemusnahan.no_rm }}</div>
              </td>
              <td class="px-6 py-4 text-xs text-gray-900">{{ pemusnahan.nama_pasien }}</td>
              <td class="px-6 py-4 text-xs text-gray-600">{{ formatDate(pemusnahan.tanggal_retensi) }}</td>
              <td class="px-6 py-4 text-xs">
                <span
                  :class="[
                    'px-3 py-1 rounded-full text-xs font-semibold',
                    pemusnahan.status === 'dimusnahkan'
                      ? 'bg-red-100 text-red-800'
                      : 'bg-yellow-100 text-yellow-800'
                  ]"
                >
                  {{ formatStatus(pemusnahan.status) }}
                </span>
              </td>
              <td class="px-6 py-4 text-xs text-gray-600">{{ pemusnahan.tanggal_pemusnahan ? formatDate(pemusnahan.tanggal_pemusnahan) : '-' }}</td>
              <td class="px-6 py-4 text-xs">
                <div class="flex gap-2">
                  <!-- Musnahkan Button -->
                  <button
                    v-if="pemusnahan.status !== 'dimusnahkan'"
                    @click="musnahkan(pemusnahan)"
                    class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-100 flex items-center justify-center transition-all duration-200 cursor-pointer"
                    title="Musnahkan"
                  >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                      <path fill-rule="evenodd" d="M12.963 2.286a.75.75 0 00-1.071-.136 9.742 9.742 0 00-3.539 6.177A7.547 7.547 0 016.648 6.61a.75.75 0 00-1.152.082A9 9 0 1015.68 4.534a7.46 7.46 0 01-2.717-2.248zM15.75 14.25a3.75 3.75 0 11-7.313-1.172c.628.465 1.35.81 2.133 1a5.99 5.99 0 011.925-3.545 3.75 3.75 0 013.255 3.717z" clip-rule="evenodd" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="bg-gray-50 px-6 py-4 flex items-center justify-between border-t border-gray-200">
        <div class="text-sm text-gray-600">
          Menampilkan 1 sampai {{ pemusnahanList.length }} dari {{ totalPemusnahan }} hasil
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
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { showSuccessToast, showErrorToast, showWarningToast, showConfirmDialog } from '../utils/notification';

export default {
  name: 'DataPemusnahan',
  setup() {
    const router = useRouter();
    const pemusnahanList = ref([]);
    const totalPemusnahan = ref(0);
    const currentPage = ref(1);
    const perPage = ref(10);
    const searchText = ref('');
    const filterStatus = ref('');
    const filterTahun = ref('');
    const filterKasusId = ref('');
    const kasusList = ref([]);
    const loading = ref(false);
    const userRole = ref(''); // akan diisi dari auth

    const totalPages = computed(() => Math.ceil(totalPemusnahan.value / perPage.value));
    
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

    const getYears = computed(() => {
      const currentYear = new Date().getFullYear();
      const years = [];
      for (let i = currentYear; i >= 2015; i--) {
        years.push(i);
      }
      return years;
    });

    const canApproveKepalaRM = computed(() => {
      return userRole.value === 'kepala_rm' || userRole.value === 'admin';
    });

    const canApproveDirektur = computed(() => {
      return userRole.value === 'direktur' || userRole.value === 'admin';
    });

    const formatDate = (date) => {
      if (!date) return '-';
      const d = new Date(date);
      return d.toLocaleDateString('id-ID', { year: 'numeric', month: '2-digit', day: '2-digit' });
    };

    const formatStatus = (status) => {
      const statusMap = {
        menunggu_eksekusi: 'Menunggu Eksekusi',
        dimusnahkan: 'Dimusnahkan',
      };
      return statusMap[status] || status;
    };

    const fetchPemusnahan = async () => {
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
        if (filterTahun.value) {
          params.append('tahun', filterTahun.value);
        }
        if (filterKasusId.value) {
          params.append('kasus_id', filterKasusId.value);
        }

        const response = await fetch(`/api/pemusnahan?${params.toString()}`);
        const data = await response.json();

        if (data.success) {
          pemusnahanList.value = data.data;
          totalPemusnahan.value = data.total;
        } else {
          await showErrorToast('Gagal memuat data pemusnahan');
        }
      } catch (error) {
        console.error('Error fetching pemusnahan:', error);
        await showErrorToast('Terjadi kesalahan saat memuat data');
      } finally {
        loading.value = false;
      }
    };

    const handleSearch = () => {
      currentPage.value = 1;
      fetchPemusnahan();
    };

    const prevPage = () => {
      if (currentPage.value > 1) {
        currentPage.value--;
        fetchPemusnahan();
      }
    };

    const nextPage = () => {
      if (currentPage.value < totalPages.value) {
        currentPage.value++;
        fetchPemusnahan();
      }
    };

    const goToPage = (page) => {
      currentPage.value = page;
      fetchPemusnahan();
    };

    const approveKepalaRM = async (pemusnahan) => {
      const result = await showConfirmDialog(
        'Setujui Pemusnahan?',
        `Dokumen pasien ${pemusnahan.nama_pasien} (${pemusnahan.no_rm}) akan disetujui oleh Kepala RM`,
        'Ya, Setujui',
        'Batal',
        '#16a34a'
      );

      if (result.isConfirmed) {
        try {
          const response = await fetch(`/api/pemusnahan/${pemusnahan.id}/approve-kepala-rm`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            },
          });

          const data = await response.json();

          if (data.success) {
            await showSuccessToast('Persetujuan Kepala RM berhasil');
            fetchPemusnahan();
          } else {
            await showErrorToast(data.message || 'Gagal menyetujui pemusnahan');
          }
        } catch (error) {
          console.error('Error:', error);
          await showErrorToast('Terjadi kesalahan saat menyetujui pemusnahan');
        }
      }
    };

    const approveDirektur = async (pemusnahan) => {
      const result = await showConfirmDialog(
        'Setujui Pemusnahan?',
        `Dokumen pasien ${pemusnahan.nama_pasien} (${pemusnahan.no_rm}) akan disetujui oleh Direktur`,
        'Ya, Setujui',
        'Batal',
        '#16a34a'
      );

      if (result.isConfirmed) {
        try {
          const response = await fetch(`/api/pemusnahan/${pemusnahan.id}/approve-direktur`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            },
          });

          const data = await response.json();

          if (data.success) {
            await showSuccessToast('Persetujuan Direktur berhasil');
            fetchPemusnahan();
          } else {
            await showErrorToast(data.message || 'Gagal menyetujui pemusnahan');
          }
        } catch (error) {
          console.error('Error:', error);
          await showErrorToast('Terjadi kesalahan saat menyetujui pemusnahan');
        }
      }
    };

    const musnahkan = async (pemusnahan) => {
      const result = await showConfirmDialog(
        'Musnahkan Dokumen?',
        `Dokumen pasien ${pemusnahan.nama_pasien} (${pemusnahan.no_rm}) akan dimusnahkan secara permanen. Aksi ini tidak dapat dibatalkan.`,
        'Ya, Musnahkan',
        'Batal',
        '#dc2626'
      );

      if (result.isConfirmed) {
        try {
          const response = await fetch(`/api/pemusnahan/${pemusnahan.id}/musnahkan`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            },
          });

          const data = await response.json();

          if (data.success) {
            await showSuccessToast('Dokumen berhasil dimusnahkan');
            fetchPemusnahan();
          } else {
            await showErrorToast(data.message || 'Gagal memusnahkan dokumen');
          }
        } catch (error) {
          console.error('Error:', error);
          await showErrorToast('Terjadi kesalahan saat memusnahkan dokumen');
        }
      }
    };



    const rejectPemusnahan = async (pemusnahan) => {
      const result = await showConfirmDialog(
        'Tolak Pemusnahan?',
        `Permintaan pemusnahan untuk pasien ${pemusnahan.nama_pasien} (${pemusnahan.no_rm}) akan ditolak dan dikembalikan ke daftar retensi`,
        'Ya, Tolak',
        'Batal',
        '#dc2626'
      );

      if (result.isConfirmed) {
        try {
          const response = await fetch(`/api/pemusnahan/${pemusnahan.id}/reject`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            },
          });

          const data = await response.json();

          if (data.success) {
            await showSuccessToast('Pemusnahan berhasil ditolak');
            fetchPemusnahan();
          } else {
            await showErrorToast(data.message || 'Gagal menolak pemusnahan');
          }
        } catch (error) {
          console.error('Error:', error);
          await showErrorToast('Terjadi kesalahan saat menolak pemusnahan');
        }
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
      filterTahun.value = '';
      filterKasusId.value = '';
      currentPage.value = 1;
      fetchPemusnahan();
    };

    onMounted(() => {
      // Get user role from meta or auth endpoint
      const roleElement = document.querySelector('meta[name="user-role"]');
      if (roleElement) {
        userRole.value = roleElement.content;
      }
      fetchPemusnahan();
      fetchKasus();
    });

    return {
      pemusnahanList,
      totalPemusnahan,
      currentPage,
      perPage,
      searchText,
      filterStatus,
      filterTahun,
      filterKasusId,
      kasusList,
      loading,
      totalPages,
      resetFilters,
      pageNumbers,
      getYears,
      canApproveKepalaRM,
      canApproveDirektur,
      formatDate,
      formatStatus,
      handleSearch,
      prevPage,
      nextPage,
      goToPage,
      approveKepalaRM,
      approveDirektur,
      musnahkan,
      rejectPemusnahan,
    };
  },
};
</script>

<style scoped>
/* Animations and transitions */
</style>
