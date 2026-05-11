<template>
  <div class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50" @click="$emit('close')"></div>

    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
      <div class="bg-white rounded-lg shadow-lg max-w-3xl w-full p-8 relative z-50">
        <!-- Close Button -->
        <button
          @click="$emit('close')"
          class="absolute top-4 right-4 text-gray-400 hover:text-gray-600"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>

        <!-- Title -->
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Detail Retensi</h2>

        <!-- Content -->
        <div v-if="retensi" class="space-y-6">
          <!-- Section 1: Data Pasien -->
          <div class="border-b border-gray-200 pb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Pasien</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <p class="text-sm text-gray-600 font-medium">No RM</p>
                <p class="text-gray-900 font-semibold">{{ retensi.no_rm }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600 font-medium">Nama Pasien</p>
                <p class="text-gray-900 font-semibold">{{ retensi.nama_pasien }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600 font-medium">Jenis Kelamin</p>
                <p class="text-gray-900">{{ retensi.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600 font-medium">No Telepon</p>
                <p class="text-gray-900">{{ retensi.no_telepon }}</p>
              </div>
              <div class="md:col-span-2">
                <p class="text-sm text-gray-600 font-medium">Alamat</p>
                <p class="text-gray-900">{{ retensi.alamat }}</p>
              </div>
            </div>
          </div>

          <!-- Section 2: Data Kasus & Layanan -->
          <div class="border-b border-gray-200 pb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Kasus & Layanan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <p class="text-sm text-gray-600 font-medium">Nama Kasus</p>
                <p class="text-gray-900 font-semibold">{{ retensi.nama_kasus }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600 font-medium">Kategori</p>
                <p class="text-gray-900">{{ retensi.kategori }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600 font-medium">Jenis Layanan</p>
                <p class="text-gray-900">{{ retensi.jenis_layanan }}</p>
              </div>
            </div>
          </div>

          <!-- Section 3: Perhitungan Retensi -->
          <div class="border-b border-gray-200 pb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Perhitungan Retensi</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <p class="text-sm text-gray-600 font-medium">Tanggal Kunjungan Terakhir</p>
                <p class="text-gray-900 font-semibold">{{ retensi.tanggal_kunjungan_terakhir }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600 font-medium">Masa Aktif</p>
                <p class="text-gray-900 font-semibold">{{ retensi.masa_aktif }} tahun</p>
              </div>
              <div>
                <p class="text-sm text-gray-600 font-medium">Masa Inaktif</p>
                <p class="text-gray-900 font-semibold">{{ retensi.masa_inaktif }} tahun</p>
              </div>
              <div>
                <p class="text-sm text-gray-600 font-medium">Tanggal Batas Aktif</p>
                <p class="text-gray-900 font-semibold">{{ retensi.tanggal_batas_aktif }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600 font-medium">Tanggal Batas Musnah</p>
                <p class="text-gray-900 font-semibold">{{ retensi.tanggal_batas_musnah }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600 font-medium">Selisih Tahun (Saat Ini)</p>
                <p class="text-gray-900 font-semibold">{{ retensi.selisih_tahun }} tahun</p>
              </div>
            </div>
          </div>

          <!-- Section 4: Timeline Journey -->
          <div class="border-b border-gray-200 pb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Timeline Retensi</h3>
            <div class="relative">
              <!-- Timeline visualization -->
              <div class="flex items-center justify-between">
                <!-- Start: Kunjungan -->
                <div class="flex flex-col items-center">
                  <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">1</div>
                  <p class="mt-2 text-xs text-gray-600 font-medium text-center">Kunjungan</p>
                  <p class="text-xs text-gray-500">{{ retensi.tanggal_kunjungan_terakhir }}</p>
                </div>

                <!-- Line 1 -->
                <div class="flex-1 h-1 bg-gradient-to-r from-blue-500 to-green-500 mx-2"></div>

                <!-- Middle: Batas Aktif -->
                <div class="flex flex-col items-center">
                  <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white font-semibold">2</div>
                  <p class="mt-2 text-xs text-gray-600 font-medium text-center">Batas Aktif</p>
                  <p class="text-xs text-gray-500">+{{ retensi.masa_aktif }} thn</p>
                </div>

                <!-- Line 2 -->
                <div class="flex-1 h-1 bg-gradient-to-r from-green-500 to-yellow-500 mx-2"></div>

                <!-- End: Batas Musnah -->
                <div class="flex flex-col items-center">
                  <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center text-white font-semibold">3</div>
                  <p class="mt-2 text-xs text-gray-600 font-medium text-center">Batas Musnah</p>
                  <p class="text-xs text-gray-500">+{{ retensi.masa_inaktif }} thn</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Section 5: Status Retensi -->
          <div class="pb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Retensi Saat Ini</h3>
            <div class="p-6 rounded-lg" :class="[
              retensi.status_retensi === 'Aktif'
                ? 'bg-green-50 border border-green-200'
                : retensi.status_retensi === 'Inaktif'
                ? 'bg-yellow-50 border border-yellow-200'
                : 'bg-red-50 border border-red-200'
            ]">
              <div class="flex items-center gap-4">
                <div :class="[
                  'w-16 h-16 rounded-full flex items-center justify-center',
                  retensi.status_retensi === 'Aktif'
                    ? 'bg-green-100'
                    : retensi.status_retensi === 'Inaktif'
                    ? 'bg-yellow-100'
                    : 'bg-red-100'
                ]">
                  <svg v-if="retensi.status_retensi === 'Aktif'" class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  <svg v-else-if="retensi.status_retensi === 'Inaktif'" class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <svg v-else class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </div>
                <div>
                  <p class="text-sm text-gray-600 font-medium">Status</p>
                  <p :class="[
                    'text-2xl font-bold',
                    retensi.status_retensi === 'Aktif'
                      ? 'text-green-600'
                      : retensi.status_retensi === 'Inaktif'
                      ? 'text-yellow-600'
                      : 'text-red-600'
                  ]">
                    {{ retensi.status_retensi }}
                  </p>
                  <p v-if="retensi.status_retensi === 'Aktif'" class="text-sm text-gray-600 mt-2">
                    Rekam medis masih dalam periode aktif dan dapat digunakan
                  </p>
                  <p v-else-if="retensi.status_retensi === 'Inaktif'" class="text-sm text-gray-600 mt-2">
                    Rekam medis mengalami periode inaktif dan disimpan di arsip
                  </p>
                  <p v-else class="text-sm text-gray-600 mt-2">
                    Rekam medis siap untuk dimusnahkan sesuai peraturan
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Close Button at Bottom -->
        <div class="flex gap-4 pt-6 border-t border-gray-200">
          <button
            @click="$emit('close')"
            class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'FormRetensi',
  props: {
    retensi: {
      type: Object,
      default: null,
    },
  },
  emits: ['close'],
};
</script>

<style scoped>
/* No additional styles needed - using Tailwind CSS */
</style>
