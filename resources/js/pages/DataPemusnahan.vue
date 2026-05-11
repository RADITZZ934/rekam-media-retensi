<template>
  <div class="min-h-screen bg-gray-50 p-8">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Pemusnahan Rekam Medis</h1>
      <p class="text-gray-600 mt-2">Kelola proses pemusnahan arsip rekam medis</p>
    </div>

    <!-- Filter and Search Section -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
          <select
            v-model="filterStatus"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">Semua Status</option>
            <option value="menunggu_persetujuan">Menunggu Persetujuan</option>
            <option value="disetujui">Disetujui</option>
            <option value="dimusnahkan">Dimusnahkan</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
          <select
            v-model="filterTahun"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">Semua Tahun</option>
            <option v-for="year in getYears" :key="year" :value="year">{{ year }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Cari Pasien</label>
          <input
            v-model="searchText"
            type="text"
            placeholder="No RM atau Nama Pasien"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <div class="flex items-end gap-3">
          <button
            @click="handleSearch"
            class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded-lg font-medium flex items-center justify-center gap-2 transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            Search
          </button>
        </div>
      </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-blue-600">
            <tr>
              <th class="px-6 py-3 text-left text-sm font-semibold text-white">No</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-white">No RM</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-white">Nama Pasien</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-white">Tanggal Retensi</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-white">Status</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-white">Kepala RM</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-white">Direktur</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-white">Tgl Pemusnahan</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-white">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="pemusnahanList.length === 0" class="border-t border-gray-200 hover:bg-gray-50">
              <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Tidak ada data pemusnahan
              </td>
            </tr>
            <tr v-for="(pemusnahan, index) in pemusnahanList" :key="pemusnahan.id" class="border-t border-gray-200 hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ (currentPage - 1) * perPage + index + 1 }}</td>
              <td class="px-6 py-4 text-sm text-gray-900">
                <div class="font-medium">{{ pemusnahan.no_rm }}</div>
              </td>
              <td class="px-6 py-4 text-sm text-gray-900">{{ pemusnahan.nama_pasien }}</td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ formatDate(pemusnahan.tanggal_retensi) }}</td>
              <td class="px-6 py-4 text-sm">
                <span
                  :class="[
                    'px-3 py-1 rounded-full text-xs font-semibold',
                    pemusnahan.status === 'dimusnahkan'
                      ? 'bg-red-100 text-red-800'
                      : pemusnahan.status === 'disetujui'
                      ? 'bg-blue-100 text-blue-800'
                      : 'bg-yellow-100 text-yellow-800'
                  ]"
                >
                  {{ formatStatus(pemusnahan.status) }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm text-center">
                <span v-if="pemusnahan.approved_kepala_rm" class="inline-flex items-center justify-center w-6 h-6 bg-green-100 rounded-full">
                  <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                </span>
                <span v-else class="inline-flex items-center justify-center w-6 h-6 bg-gray-200 rounded-full">
                  <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </span>
              </td>
              <td class="px-6 py-4 text-sm text-center">
                <span v-if="pemusnahan.approved_direktur" class="inline-flex items-center justify-center w-6 h-6 bg-green-100 rounded-full">
                  <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                </span>
                <span v-else class="inline-flex items-center justify-center w-6 h-6 bg-gray-200 rounded-full">
                  <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ pemusnahan.tanggal_pemusnahan ? formatDate(pemusnahan.tanggal_pemusnahan) : '-' }}</td>
              <td class="px-6 py-4 text-sm space-x-2 flex flex-wrap gap-2">
                <!-- Approve Kepala RM Button -->
                <button
                  v-if="!pemusnahan.approved_kepala_rm && pemusnahan.status === 'menunggu_persetujuan' && canApproveKepalaRM"
                  @click="approveKepalaRM(pemusnahan)"
                  class="inline-flex items-center justify-center w-9 h-9 border border-green-300 text-green-600 rounded hover:bg-green-50 transition-colors"
                  title="Setujui (Kepala RM)"
                >
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                </button>

                <!-- Approve Direktur Button -->
                <button
                  v-if="!pemusnahan.approved_direktur && pemusnahan.approved_kepala_rm && pemusnahan.status === 'menunggu_persetujuan' && canApproveDirektur"
                  @click="approveDirektur(pemusnahan)"
                  class="inline-flex items-center justify-center w-9 h-9 border border-green-300 text-green-600 rounded hover:bg-green-50 transition-colors"
                  title="Setujui (Direktur)"
                >
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                </button>

                <!-- Musnahkan Button -->
                <button
                  v-if="pemusnahan.status === 'disetujui'"
                  @click="musnahkan(pemusnahan)"
                  class="inline-flex items-center justify-center w-9 h-9 border border-red-300 text-red-600 rounded hover:bg-red-50 transition-colors"
                  title="Musnahkan"
                >
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                  </svg>
                </button>

                <!-- Generate Berita Acara Button -->
                <button
                  v-if="pemusnahan.status === 'dimusnahkan'"
                  @click="generateBeritaAcara(pemusnahan)"
                  class="inline-flex items-center justify-center w-9 h-9 border border-blue-300 text-blue-600 rounded hover:bg-blue-50 transition-colors"
                  title="Generate Berita Acara"
                >
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 012-2h6a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
                  </svg>
                </button>

                <!-- Reject Button (optional) -->
                <button
                  v-if="pemusnahan.status === 'menunggu_persetujuan'"
                  @click="rejectPemusnahan(pemusnahan)"
                  class="inline-flex items-center justify-center w-9 h-9 border border-red-300 text-red-600 rounded hover:bg-red-50 transition-colors"
                  title="Tolak"
                >
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </button>
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
        menunggu_persetujuan: 'Menunggu Persetujuan',
        disetujui: 'Disetujui',
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

    const generateBeritaAcara = async (pemusnahan) => {
      try {
        const response = await fetch(`/api/pemusnahan/${pemusnahan.id}/generate-berita-acara`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
          },
        });

        const data = await response.json();

        if (data.success) {
          await showSuccessToast('Berita acara berhasil dibuat');
          // Download the PDF
          const link = document.createElement('a');
          link.href = data.file_path;
          link.download = `Berita_Acara_${pemusnahan.no_rm}_${new Date().getTime()}.pdf`;
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
        } else {
          await showErrorToast(data.message || 'Gagal membuat berita acara');
        }
      } catch (error) {
        console.error('Error:', error);
        await showErrorToast('Terjadi kesalahan saat membuat berita acara');
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

    onMounted(() => {
      // Get user role from meta or auth endpoint
      const roleElement = document.querySelector('meta[name="user-role"]');
      if (roleElement) {
        userRole.value = roleElement.content;
      }
      fetchPemusnahan();
    });

    return {
      pemusnahanList,
      totalPemusnahan,
      currentPage,
      perPage,
      searchText,
      filterStatus,
      filterTahun,
      loading,
      totalPages,
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
      generateBeritaAcara,
      rejectPemusnahan,
    };
  },
};
</script>

<style scoped>
/* Animations and transitions */
</style>
