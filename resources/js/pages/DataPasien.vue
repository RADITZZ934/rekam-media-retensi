<template>
  <div class="min-h-screen bg-gray-50 p-8">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Data Pasien</h1>
      <p class="text-gray-600 mt-2">Kelola seluruh data pasien rekam medis</p>
    </div>

    <!-- Action Bar -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
      <div class="w-full">
        <!-- Search -->
        <div class="relative">
          <input
            v-model="searchText"
            type="text"
            placeholder="Cari berdasarkan nomor RM atau nama pasien..."
            class="w-full pl-10 pr-4 py-2.5 bg-gray-50/50 border border-gray-200/80 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/10 focus:border-blue-500 text-sm font-semibold transition-all"
            @input="handleSearch"
          />
          <svg class="absolute left-3.5 top-3.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
      </div>
    </div>

    <!-- Table Pasien -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[800px]">
          <thead class="bg-blue-600 text-white">
            <tr class="text-[10px] font-bold uppercase tracking-widest">
              <th class="px-6 py-4 text-left">No RM</th>
              <th class="px-6 py-4 text-left">Nama Pasien</th>
              <th class="px-6 py-4 text-left">Jenis Kelamin</th>
              <th class="px-6 py-4 text-left">Tanggal Lahir</th>
              <th class="px-6 py-4 text-left">Kasus Medis</th>
              <th class="px-6 py-4 text-left">Alamat</th>
              <th class="px-6 py-4 text-left">No HP</th>
              <th class="px-6 py-4 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="pasienList.length === 0" class="border-t border-gray-200">
              <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                Tidak ada data pasien
              </td>
            </tr>
            <tr v-for="pasien in pasienList" :key="pasien.no_rm" class="border-t border-gray-200 hover:bg-gray-50">
              <td class="px-6 py-4 text-xs font-medium text-gray-900">{{ pasien.no_rm }}</td>
              <td class="px-6 py-4 text-xs text-gray-700">{{ pasien.nama_pasien }}</td>
              <td class="px-6 py-4 text-xs text-gray-700">{{ pasien.jenis_kelamin }}</td>
              <td class="px-6 py-4 text-xs text-gray-700">{{ pasien.tanggal_lahir }}</td>
              <td class="px-6 py-4 text-xs text-gray-700">{{ pasien.kasus_nama || '-' }}</td>
              <td class="px-6 py-4 text-xs text-gray-700 truncate max-w-[200px]" :title="pasien.alamat">{{ pasien.alamat || '-' }}</td>
              <td class="px-6 py-4 text-xs text-gray-700" style="white-space: nowrap;">{{ pasien.no_telepon || '-' }}</td>
              <td class="px-6 py-4 text-xs text-center">
                <div class="flex gap-2 justify-center">
                  <button
                    @click="previewPasien(pasien)"
                    class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 flex items-center justify-center transition-all duration-200 cursor-pointer"
                    title="Preview Detail"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                  <button
                    @click="deletePasien(pasien.no_rm)"
                    class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition-all duration-200 cursor-pointer"
                    title="Hapus"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
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
          Menampilkan 1 sampai {{ pasienList.length }} dari {{ totalPasien }} hasil
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

    <!-- Form Modal -->
    <FormPasien
      v-if="showFormModal"
      :pasien="editingPasien"
      @close="closeFormModal"
      @save="savePasien"
    />
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';
import FormPasien from '../components/FormPasien.vue';
import { showSuccessToast, showErrorToast, showWarningToast, showConfirmDialog } from '../utils/notification';

export default {
  name: 'DataPasien',
  components: {
    FormPasien,
  },
  setup() {
    const router = useRouter();
    const pasienList = ref([]);
    const totalPasien = ref(0);
    const currentPage = ref(1);
    const perPage = ref(10);
    const searchText = ref('');
    const filterStatusRm = ref('');
    const filterStatusRetensi = ref('');
    const showFormModal = ref(false);
    const editingPasien = ref(null);
    const loading = ref(false);

    const totalPages = computed(() => Math.ceil(totalPasien.value / perPage.value));
    const pageNumbers = computed(() => {
      const pages = [];
      for (let i = 1; i <= totalPages.value; i++) {
        pages.push(i);
      }
      return pages;
    });

    const fetchPasien = async () => {
      loading.value = true;
      try {
        const params = new URLSearchParams({
          page: currentPage.value,
          per_page: perPage.value,
        });

        if (searchText.value) {
          params.append('search', searchText.value);
        }
        if (filterStatusRm.value) {
          params.append('status_rm', filterStatusRm.value);
        }
        if (filterStatusRetensi.value) {
          params.append('status_retensi', filterStatusRetensi.value);
        }

        const response = await fetch(`/api/pasien?${params}`);
        const data = await response.json();

        pasienList.value = data.data || [];
        totalPasien.value = data.total || 0;
        currentPage.value = data.current_page || 1;
      } catch (error) {
        console.error('Error fetching pasien:', error);
        await showErrorToast('Gagal memuat data pasien');
      } finally {
        loading.value = false;
      }
    };

    const handleSearch = () => {
      currentPage.value = 1;
      fetchPasien();
    };

    const applyFilters = () => {
      currentPage.value = 1;
      fetchPasien();
    };

    const prevPage = () => {
      if (currentPage.value > 1) {
        currentPage.value--;
        fetchPasien();
      }
    };

    const nextPage = () => {
      if (currentPage.value < totalPages.value) {
        currentPage.value++;
        fetchPasien();
      }
    };

    const goToPage = (page) => {
      currentPage.value = page;
      fetchPasien();
    };

    const openFormModal = (pasien = null) => {
      editingPasien.value = pasien;
      showFormModal.value = true;
    };

    const closeFormModal = () => {
      showFormModal.value = false;
      editingPasien.value = null;
    };

    const previewPasien = (pasien) => {
      router.push({
        name: 'previewPasien',
        params: { no_rm: pasien.no_rm },
      });
    };

    const savePasien = async (data) => {
      try {
        if (data.no_rm) {
          // Update
          const response = await fetch(`/api/pasien/${data.no_rm}`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            },
            body: JSON.stringify(data),
          });
          const result = await response.json();
          if (result.success) {
            await showSuccessToast('Pasien berhasil diperbarui');
            closeFormModal();
            fetchPasien();
          } else {
            await showErrorToast(result.message || 'Terjadi kesalahan');
          }
        } else {
          // Create
          const response = await fetch('/api/pasien', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            },
            body: JSON.stringify(data),
          });
          const result = await response.json();
          if (result.success) {
            await showSuccessToast('Pasien berhasil ditambahkan');
            closeFormModal();
            currentPage.value = 1;
            fetchPasien();
          } else {
            await showErrorToast(result.message || 'Terjadi kesalahan');
          }
        }
      } catch (error) {
        console.error('Error saving pasien:', error);
        await showErrorToast('Gagal menyimpan pasien');
      }
    };

    const deletePasien = async (no_rm) => {
      const result = await showConfirmDialog(
        'Hapus Pasien?',
        'Apakah Anda yakin ingin menghapus pasien ini?',
        'Ya, Hapus',
        'Batal',
        '#dc2626'
      );

      if (!result.isConfirmed) {
        return;
      }

      try {
        const response = await fetch(`/api/pasien/${no_rm}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
          },
        });
        const data = await response.json();
        if (data.success) {
          await showSuccessToast('Pasien berhasil dihapus');
          if (pasienList.value.length === 1 && currentPage.value > 1) {
            currentPage.value--;
          }
          fetchPasien();
        } else {
          await showErrorToast(data.message || 'Terjadi kesalahan');
        }
      } catch (error) {
        console.error('Error deleting pasien:', error);
        await showErrorToast('Gagal menghapus pasien');
      }
    };

    onMounted(() => {
      fetchPasien();
    });

    return {
      pasienList,
      totalPasien,
      currentPage,
      searchText,
      filterStatusRm,
      filterStatusRetensi,
      showFormModal,
      editingPasien,
      loading,
      totalPages,
      pageNumbers,
      fetchPasien,
      handleSearch,
      applyFilters,
      prevPage,
      nextPage,
      goToPage,
      openFormModal,
      closeFormModal,
      previewPasien,
      savePasien,
      deletePasien,
    };
  },
};
</script>

<style scoped>
/* No additional styles needed - using Tailwind CSS */
</style>
