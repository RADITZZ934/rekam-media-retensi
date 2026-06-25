<template>
  <div class="min-h-screen bg-gray-50 p-8">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Data Kasus</h1>
      <p class="text-gray-600 mt-2">Master data kasus rekam medis untuk kategori retensi</p>
    </div>

    <!-- Action Bar -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Search -->
        <div class="relative">
          <input
            v-model="searchText"
            type="text"
            placeholder="Cari kode/nama kasus..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            @input="handleSearch"
          />
          <svg class="absolute right-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>

        <!-- Filter Status -->
        <select
          v-model="filterStatus"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          @change="applyFilters"
        >
          <option value="">Semua Status</option>
          <option value="Aktif">Aktif</option>
          <option value="Nonaktif">Nonaktif</option>
        </select>

        <!-- Filter Kategori -->
        <select
          v-model="filterKategori"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          @change="applyFilters"
        >
          <option value="">Semua Kategori</option>
          <option v-for="kat in kategoriList" :key="kat" :value="kat">
            {{ kat }}
          </option>
        </select>
      </div>

      <!-- Tambah Kasus Button -->
      <button
        @click="openFormModal()"
        class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold flex items-center gap-2 w-full md:w-auto justify-center md:justify-start"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Kasus
      </button>
    </div>

    <!-- Table Kasus -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-blue-600 text-white">
            <tr class="text-[10px] font-bold uppercase tracking-widest">
              <th class="px-6 py-4 text-left">Kode Kasus</th>
              <th class="px-6 py-4 text-left">Nama Kasus</th>
              <th class="px-6 py-4 text-left">Kategori</th>
              <th class="px-6 py-4 text-left">Retensi Aktif</th>
              <th class="px-6 py-4 text-left">Retensi Inaktif</th>
              <th class="px-6 py-4 text-left">Status</th>
              <th class="px-6 py-4 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="kasusList.length === 0" class="border-t border-gray-200">
              <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                Tidak ada data kasus
              </td>
            </tr>
            <tr v-for="kasus in kasusList" :key="kasus.id" class="border-t border-gray-200 hover:bg-gray-50">
              <td class="px-6 py-4 text-xs font-medium text-gray-900">{{ kasus.kode_kasus }}</td>
              <td class="px-6 py-4 text-xs text-gray-700">{{ kasus.nama_kasus }}</td>
              <td class="px-6 py-4 text-xs text-gray-700">{{ kasus.kategori }}</td>
              <td class="px-6 py-4 text-xs text-gray-700">{{ kasus.masa_retensi_aktif }} tahun</td>
              <td class="px-6 py-4 text-xs text-gray-700">{{ kasus.masa_retensi_inaktif }} tahun</td>
              <td class="px-6 py-4 text-xs">
                <span
                  :class="[
                    'px-3 py-1 rounded-full text-xs font-semibold',
                    kasus.status === 'Aktif'
                      ? 'bg-green-100 text-green-800'
                      : 'bg-gray-100 text-gray-800'
                  ]"
                >
                  {{ kasus.status }}
                </span>
              </td>
              <td class="px-6 py-4 text-xs text-center">
                <div class="flex gap-2 justify-center">
                  <button
                    @click="openFormModal(kasus)"
                    class="text-blue-600 hover:text-blue-800 p-1"
                    title="Edit"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button
                    @click="deleteKasus(kasus.id)"
                    class="text-red-600 hover:text-red-800 p-1"
                    title="Hapus"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
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
          Menampilkan 1 sampai {{ kasusList.length }} dari {{ totalKasus }} hasil
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
    <FormKasus
      v-if="showFormModal"
      :kasus="editingKasus"
      :kategori-list="kategoriList"
      @close="closeFormModal"
      @save="saveKasus"
    />
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import FormKasus from '../components/FormKasus.vue';
import { showSuccessToast, showErrorToast, showConfirmDialog } from '../utils/notification';

export default {
  name: 'DataKasus',
  components: {
    FormKasus,
  },
  setup() {
    const kasusList = ref([]);
    const totalKasus = ref(0);
    const currentPage = ref(1);
    const perPage = ref(10);
    const searchText = ref('');
    const filterStatus = ref('');
    const filterKategori = ref('');
    const kategoriList = ref([]);
    const showFormModal = ref(false);
    const editingKasus = ref(null);
    const loading = ref(false);

    const totalPages = computed(() => Math.ceil(totalKasus.value / perPage.value));
    const pageNumbers = computed(() => {
      const pages = [];
      for (let i = 1; i <= totalPages.value; i++) {
        pages.push(i);
      }
      return pages;
    });

    const fetchKasus = async () => {
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

        const response = await fetch(`/api/kasus?${params}`);
        const data = await response.json();

        kasusList.value = data.data || [];
        totalKasus.value = data.total || 0;
        currentPage.value = data.current_page || 1;
      } catch (error) {
        console.error('Error fetching kasus:', error);
        await showErrorToast('Gagal memuat data kasus');
      } finally {
        loading.value = false;
      }
    };

    const fetchKategori = async () => {
      try {
        const response = await fetch('/api/kasus/kategori/list');
        const data = await response.json();
        kategoriList.value = data || [];
      } catch (error) {
        console.error('Error fetching kategori:', error);
      }
    };

    const handleSearch = () => {
      currentPage.value = 1;
      fetchKasus();
    };

    const applyFilters = () => {
      currentPage.value = 1;
      fetchKasus();
    };

    const prevPage = () => {
      if (currentPage.value > 1) {
        currentPage.value--;
        fetchKasus();
      }
    };

    const nextPage = () => {
      if (currentPage.value < totalPages.value) {
        currentPage.value++;
        fetchKasus();
      }
    };

    const goToPage = (page) => {
      currentPage.value = page;
      fetchKasus();
    };

    const openFormModal = (kasus = null) => {
      editingKasus.value = kasus;
      showFormModal.value = true;
    };

    const closeFormModal = () => {
      showFormModal.value = false;
      editingKasus.value = null;
    };

    const saveKasus = async (data) => {
      try {
        if (data.id) {
          // Update
          const response = await fetch(`/api/kasus/${data.id}`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            },
            body: JSON.stringify(data),
          });
          const result = await response.json();
          if (result.success) {
            await showSuccessToast('Kasus berhasil diperbarui');
            closeFormModal();
            fetchKasus();
          } else {
            await showErrorToast(result.message || 'Gagal memperbarui kasus');
          }
        } else {
          // Create
          const response = await fetch('/api/kasus', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            },
            body: JSON.stringify(data),
          });
          const result = await response.json();
          if (result.success) {
            await showSuccessToast('Kasus berhasil ditambahkan');
            closeFormModal();
            currentPage.value = 1;
            fetchKasus();
            fetchKategori();
          } else {
            await showErrorToast(result.message || 'Gagal menambahkan kasus');
          }
        }
      } catch (error) {
        console.error('Error saving kasus:', error);
        await showErrorToast(error.message || 'Terjadi kesalahan saat menyimpan kasus');
      }
    };

    const deleteKasus = async (id) => {
      const result = await showConfirmDialog(
        'Hapus Kasus?',
        'Data kasus akan dihapus secara permanen dan tidak dapat dikembalikan',
        'Ya, Hapus',
        'Batal',
        '#dc2626'
      );

      if (!result.isConfirmed) {
        return;
      }

      try {
        const response = await fetch(`/api/kasus/${id}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
          },
        });
        const result = await response.json();
        if (result.success) {
          await showSuccessToast('Kasus berhasil dihapus');
          if (kasusList.value.length === 1 && currentPage.value > 1) {
            currentPage.value--;
          }
          fetchKasus();
        } else {
          await showErrorToast(result.message || 'Gagal menghapus kasus');
        }
      } catch (error) {
        console.error('Error deleting kasus:', error);
        await showErrorToast(error.message || 'Terjadi kesalahan saat menghapus kasus');
      }
    };

    onMounted(() => {
      fetchKasus();
      fetchKategori();
    });

    return {
      kasusList,
      totalKasus,
      currentPage,
      searchText,
      filterStatus,
      filterKategori,
      kategoriList,
      showFormModal,
      editingKasus,
      loading,
      totalPages,
      pageNumbers,
      fetchKasus,
      handleSearch,
      applyFilters,
      prevPage,
      nextPage,
      goToPage,
      openFormModal,
      closeFormModal,
      saveKasus,
      deleteKasus,
    };
  },
};
</script>

<style scoped>
/* No additional styles needed - using Tailwind CSS */
</style>
