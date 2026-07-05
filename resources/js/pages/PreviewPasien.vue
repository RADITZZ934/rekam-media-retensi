<template>
  <div class="min-h-screen bg-gray-50 p-8">
    <!-- Header with Back Button -->
    <div class="mb-8 flex items-center gap-4">
      <button
        @click="router.back()"
        class="p-2 hover:bg-gray-200 rounded-lg transition-colors"
      >
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Detail Pasien</h1>
        <p class="text-gray-600 mt-2" v-if="pasien">{{ pasien.nama_pasien }}</p>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left Column: Patient Details -->
      <div class="lg:col-span-2">
        <!-- Personal Information -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
          <h2 class="text-xl font-bold text-gray-900 mb-6">Informasi Pribadi</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-600 mb-2">Nomor Rekam Medis</label>
              <p class="text-lg font-semibold text-gray-900">{{ pasien?.no_rm || '-' }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-600 mb-2">Nama Pasien</label>
              <p class="text-lg font-semibold text-gray-900">{{ pasien?.nama_pasien || '-' }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-600 mb-2">Jenis Kelamin</label>
              <p class="text-lg font-semibold text-gray-900">{{ pasien?.jenis_kelamin || '-' }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-600 mb-2">Tanggal Lahir</label>
              <p class="text-lg font-semibold text-gray-900">{{ pasien?.tanggal_lahir || '-' }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-600 mb-2">Alamat</label>
              <p class="text-lg font-semibold text-gray-900">{{ pasien?.alamat || '-' }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-600 mb-2">No Telepon</label>
              <p class="text-lg font-semibold text-gray-900">{{ pasien?.no_telepon || '-' }}</p>
            </div>
          </div>
        </div>

        <!-- Medical Information -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
          <h2 class="text-xl font-bold text-gray-900 mb-6">Informasi Medis</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-600 mb-2">Kasus Medis</label>
              <p class="text-lg font-semibold text-gray-900">{{ pasien?.kasus_nama || '-' }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-600 mb-2">Status Berkas</label>
              <p class="text-lg font-semibold">
                <span
                  :class="[
                    'px-4 py-2 rounded-full text-sm font-semibold',
                    pasien?.status_retensi === 'Aktif'
                      ? 'bg-green-100 text-green-800'
                      : (pasien?.status_retensi === 'Inaktif'
                          ? 'bg-yellow-100 text-yellow-800'
                          : (pasien?.status_retensi === 'Siap Dimusnahkan'
                              ? 'bg-orange-100 text-orange-800'
                              : 'bg-red-100 text-red-800'))
                  ]"
                >
                  {{ pasien?.status_retensi || '-' }}
                </span>
              </p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-600 mb-2">Tanggal Batas Aktif</label>
              <p class="text-lg font-semibold text-gray-900">{{ pasien?.tgl_batas_aktif || '-' }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-600 mb-2">Tanggal Batas Musnah</label>
              <p class="text-lg font-semibold text-gray-900">{{ pasien?.tgl_batas_musnah || '-' }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Medical Documents -->
      <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-sm p-6 sticky top-8">
          <h2 class="text-xl font-bold text-gray-900 mb-6">Dokumen Rekam Medis</h2>

          <div v-if="loading" class="text-center py-8">
            <div class="inline-block">
              <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </div>
            <p class="text-gray-600 mt-4">Memuat dokumen...</p>
          </div>

          <div v-else-if="dokumentList.length === 0" class="text-center py-8 text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            <p>Tidak ada dokumen</p>
          </div>

          <div v-else class="space-y-3">
            <div
              v-for="dokumen in dokumentList"
              :key="dokumen.id"
              class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer"
              @click="viewDocument(dokumen)"
            >
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm0 2h12v10H4V5z" />
                </svg>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900 truncate">{{ dokumen.nama_file }}</p>
                  <p class="text-xs text-gray-500 mt-1">{{ dokumen.tanggal_upload }}</p>
                  <div class="mt-2">
                    <span
                      :class="[
                        'inline-block px-2 py-1 rounded text-xs font-semibold',
                        dokumen.status === 'completed'
                          ? 'bg-green-100 text-green-800'
                          : dokumen.status === 'processing'
                          ? 'bg-blue-100 text-blue-800'
                          : dokumen.status === 'failed'
                          ? 'bg-red-100 text-red-800'
                          : 'bg-gray-100 text-gray-800'
                      ]"
                    >
                      {{ dokumen.status }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import Swal from 'sweetalert2';
import { showErrorToast } from '../utils/notification';

export default {
  name: 'PreviewPasien',
  setup() {
    const router = useRouter();
    const route = useRoute();
    const pasien = ref(null);
    const dokumentList = ref([]);
    const loading = ref(false);

    const fetchPasienDetail = async () => {
      try {
        const response = await fetch(`/api/pasien/${route.params.no_rm}`);
        const data = await response.json();

        if (data.success) {
          pasien.value = data.data;
        } else {
          await showErrorToast('Pasien tidak ditemukan');
          router.back();
        }
      } catch (error) {
        console.error('Error fetching pasien:', error);
        await showErrorToast('Gagal memuat data pasien');
        router.back();
      }
    };

    const fetchDokumen = async () => {
      loading.value = true;
      try {
        const response = await fetch(`/api/alih-media?no_rm=${route.params.no_rm}`);
        const data = await response.json();

        if (data.success) {
          dokumentList.value = data.data || [];
        }
      } catch (error) {
        console.error('Error fetching dokumen:', error);
      } finally {
        loading.value = false;
      }
    };

    const viewDocument = (dokumen) => {
      window.open(`/api/alih-media/${dokumen.id}/file`, '_blank');
    };

    onMounted(() => {
      fetchPasienDetail();
      fetchDokumen();
    });

    return {
      router,
      pasien,
      dokumentList,
      loading,
      viewDocument,
    };
  },
};
</script>

<style scoped>
/* No additional styles needed - using Tailwind CSS */
</style>
