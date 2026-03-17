<template>
  <div class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50" @click="$emit('close')"></div>

    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
      <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full p-8 relative z-50">
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
        <h2 class="text-2xl font-bold text-gray-900 mb-6">
          {{ pasien ? 'Edit Pasien' : 'Tambah Pasien Baru' }}
        </h2>

        <!-- Form -->
        <form @submit.prevent="submitForm" class="space-y-6">
          <!-- Row 1: No RM dan Nama Pasien -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">No RM *</label>
              <input
                v-model="form.no_rm"
                type="text"
                placeholder="RM00001001"
                :disabled="!!pasien"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
                required
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pasien *</label>
              <input
                v-model="form.nama_pasien"
                type="text"
                placeholder="Nama lengkap pasien"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              />
            </div>
          </div>

          <!-- Row 2: Jenis Kelamin dan Tanggal Lahir -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin *</label>
              <select
                v-model="form.jenis_kelamin"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              >
                <option value="">-- Pilih Jenis Kelamin --</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir *</label>
              <input
                v-model="form.tanggal_lahir"
                type="date"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              />
            </div>
          </div>

          <!-- Row 3: Tempat Lahir -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir *</label>
            <input
              v-model="form.tempat_lahir"
              type="text"
              placeholder="Kota/Kabupaten"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
            />
          </div>

          <!-- Row 4: Alamat -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
            <textarea
              v-model="form.alamat"
              placeholder="Alamat lengkap pasien"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              rows="3"
            ></textarea>
          </div>

          <!-- Row 5: No Telepon dan Status RM -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">No Telepon</label>
              <input
                v-model="form.no_telepon"
                type="tel"
                placeholder="08xx xxxx xxxx"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Status RM *</label>
              <select
                v-model="form.status_rm"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              >
                <option value="">-- Pilih Status --</option>
                <option value="Aktif">Aktif</option>
                <option value="Inaktif">Inaktif</option>
              </select>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="flex gap-4 pt-6 border-t border-gray-200">
            <button
              type="button"
              @click="$emit('close')"
              class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="loading"
              class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ loading ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, watch } from 'vue';

export default {
  name: 'FormPasien',
  props: {
    pasien: {
      type: Object,
      default: null,
    },
  },
  emits: ['close', 'save'],
  setup(props, { emit }) {
    const loading = ref(false);
    const form = reactive({
      no_rm: '',
      nama_pasien: '',
      jenis_kelamin: '',
      tanggal_lahir: '',
      tempat_lahir: '',
      alamat: '',
      no_telepon: '',
      status_rm: 'Aktif',
    });

    // Pre-fill form jika edit
    watch(
      () => props.pasien,
      (newPasien) => {
        if (newPasien) {
          form.no_rm = newPasien.no_rm;
          form.nama_pasien = newPasien.nama_pasien;
          form.jenis_kelamin = newPasien.jenis_kelamin;
          
          // Convert date format from dd/mm/yyyy to yyyy-mm-dd
          if (newPasien.tanggal_lahir) {
            const dateStr = newPasien.tanggal_lahir;
            const dateParts = dateStr.split('/');
            if (dateParts.length === 3) {
              form.tanggal_lahir = `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`;
            } else {
              form.tanggal_lahir = dateStr;
            }
          }
          
          form.tempat_lahir = newPasien.tempat_lahir;
          form.alamat = newPasien.alamat || '';
          form.no_telepon = newPasien.no_telepon || '';
          form.status_rm = newPasien.status_rm;
        }
      },
      { immediate: true }
    );

    const submitForm = () => {
      loading.value = true;
      
      // Convert tanggal_lahir from yyyy-mm-dd to dd/mm/yyyy for storage
      const dateParts = form.tanggal_lahir.split('-');
      const formData = {
        ...form,
        tanggal_lahir: form.tanggal_lahir, // Server akan handle parsing
      };
      
      emit('save', formData);
      setTimeout(() => {
        loading.value = false;
      }, 500);
    };

    return {
      form,
      loading,
      submitForm,
    };
  },
};
</script>

<style scoped>
/* No additional styles needed - using Tailwind CSS */
</style>
