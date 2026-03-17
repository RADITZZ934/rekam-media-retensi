<template>
  <div class="min-h-screen bg-gray-50 p-8">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Data Pasien</h1>
      <p class="text-gray-600 mt-2">Kelola seluruh data pasien rekam medis</p>
    </div>

    <!-- Action Bar -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Search -->
        <div class="relative">
          <input
            v-model="searchText"
            type="text"
            placeholder="Cari nama pasien, No RM..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            @input="handleSearch"
          />
          <svg class="absolute right-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>

        <!-- Filter Status RM -->
        <select
          v-model="filterStatusRm"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          @change="applyFilters"
        >
          <option value="">Semua Status RM</option>
          <option value="Aktif">Aktif</option>
          <option value="Inaktif">Inaktif</option>
        </select>

        <!-- Filter Status Retensi -->
        <select
          v-model="filterStatusRetensi"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          @change="applyFilters"
        >
          <option value="">Semua Status Retensi</option>
          <option value="Aktif">Aktif</option>
          <option value="Inaktif">Inaktif</option>
          <option value="Siap Musnah">Siap Musnah</option>
        </select>
      </div>

      <!-- Tambah Pasien Button -->
      <button
        @click="openFormModal()"
        class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold flex items-center gap-2 w-full md:w-auto justify-center md:justify-start"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Pasien
      </button>
    </div>

    <!-- Table Pasien -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-blue-600 text-white">
            <tr>
              <th class="px-6 py-4 text-left text-sm font-semibold">No RM</th>
              <th class="px-6 py-4 text-left text-sm font-semibold">Nama Pasien</th>
              <th class="px-6 py-4 text-left text-sm font-semibold">Jenis Kelamin</th>
              <th class="px-6 py-4 text-left text-sm font-semibold">Tanggal Lahir</th>
              <th class="px-6 py-4 text-left text-sm font-semibold">Tempat Lahir</th>
              <th class="px-6 py-4 text-left text-sm font-semibold">Kunjungan Terakhir</th>
              <th class="px-6 py-4 text-left text-sm font-semibold">No Telp</th>
              <th class="px-6 py-4 text-left text-sm font-semibold">Status RM</th>
              <th class="px-6 py-4 text-left text-sm font-semibold">Status Retensi</th>
              <th class="px-6 py-4 text-center text-sm font-semibold">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="pasienList.length === 0" class="border-t border-gray-200">
              <td colspan="10" class="px-6 py-8 text-center text-gray-500">
                Tidak ada data pasien
              </td>
            </tr>
            <tr v-for="pasien in pasienList" :key="pasien.no_rm" class="border-t border-gray-200 hover:bg-gray-50">
              <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ pasien.no_rm }}</td>
              <td class="px-6 py-4 text-sm text-gray-700">{{ pasien.nama_pasien }}</td>
              <td class="px-6 py-4 text-sm text-gray-700">{{ pasien.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
              <td class="px-6 py-4 text-sm text-gray-700">{{ pasien.tanggal_lahir }}</td>
              <td class="px-6 py-4 text-sm text-gray-700">{{ pasien.tempat_lahir }}</td>
              <td class="px-6 py-4 text-sm text-gray-700">{{ pasien.tgl_kunjungan_terakhir || '-' }}</td>
              <td class="px-6 py-4 text-sm text-gray-700">{{ pasien.no_telepon || '-' }}</td>
              <td class="px-6 py-4 text-sm">
                <span
                  :class="[
                    'px-3 py-1 rounded-full text-xs font-semibold',
                    pasien.status_rm === 'Aktif'
                      ? 'bg-green-100 text-green-800'
                      : 'bg-gray-100 text-gray-800'
                  ]"
                >
                  {{ pasien.status_rm }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm">
                <span
                  :class="[
                    'px-3 py-1 rounded-full text-xs font-semibold',
                    pasien.status_retensi === 'Aktif'
                      ? 'bg-green-100 text-green-800'
                      : pasien.status_retensi === 'Inaktif'
                      ? 'bg-yellow-100 text-yellow-800'
                      : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ pasien.status_retensi }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm text-center">
                <div class="flex gap-2 justify-center">
                  <button
                    @click="openFormModal(pasien)"
                    class="text-blue-600 hover:text-blue-800 p-1"
                    title="Edit"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button
                    @click="deletePasien(pasien.no_rm)"
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
import FormPasien from '../components/FormPasien.vue';

export default {
  name: 'DataPasien',
  components: {
    FormPasien,
  },
  setup() {
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
        alert('Gagal memuat data pasien');
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
            alert('Pasien berhasil diperbarui');
            closeFormModal();
            fetchPasien();
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
            alert('Pasien berhasil ditambahkan');
            closeFormModal();
            currentPage.value = 1;
            fetchPasien();
          }
        }
      } catch (error) {
        console.error('Error saving pasien:', error);
        alert('Gagal menyimpan pasien');
      }
    };

    const deletePasien = async (no_rm) => {
      if (!confirm('Apakah Anda yakin ingin menghapus pasien ini?')) {
        return;
      }

      try {
        const response = await fetch(`/api/pasien/${no_rm}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
          },
        });
        const result = await response.json();
        if (result.success) {
          alert('Pasien berhasil dihapus');
          if (pasienList.value.length === 1 && currentPage.value > 1) {
            currentPage.value--;
          }
          fetchPasien();
        }
      } catch (error) {
        console.error('Error deleting pasien:', error);
        alert('Gagal menghapus pasien');
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
      savePasien,
      deletePasien,
    };
  },
};
</script>

<style scoped>
/* No additional styles needed - using Tailwind CSS */
</style>
