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
          {{ kasus ? 'Edit Kasus' : 'Tambah Kasus Baru' }}
        </h2>

        <!-- Form -->
        <form @submit.prevent="submitForm" class="space-y-6">
          <!-- Row 1: Kode dan Nama Kasus -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Kode Kasus *</label>
              <input
                v-model="form.kode_kasus"
                type="text"
                placeholder="KAS001"
                :disabled="!!kasus"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
                required
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kasus *</label>
              <input
                v-model="form.nama_kasus"
                type="text"
                placeholder="Nama kasus"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              />
            </div>
          </div>

          <!-- Row 2: Kategori -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
            <input
              v-model="form.kategori"
              type="text"
              placeholder="Kategori penyakit"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
              list="kategoriSuggestions"
            />
            <datalist id="kategoriSuggestions">
              <option v-for="kat in kategoriList" :key="kat" :value="kat"></option>
            </datalist>
          </div>

          <!-- Row 3: Retensi Aktif dan Inaktif -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Masa Retensi Aktif (tahun) *</label>
              <input
                v-model.number="form.masa_retensi_aktif"
                type="number"
                min="1"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Masa Retensi Inaktif (tahun) *</label>
              <input
                v-model.number="form.masa_retensi_inaktif"
                type="number"
                min="1"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              />
            </div>
          </div>

          <!-- Row 4: Deskripsi -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
            <textarea
              v-model="form.deskripsi"
              placeholder="Deskripsi kasus"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              rows="3"
            ></textarea>
          </div>

          <!-- Row 5: Status -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
            <select
              v-model="form.status"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
            >
              <option value="">-- Pilih Status --</option>
              <option value="Aktif">Aktif</option>
              <option value="Nonaktif">Nonaktif</option>
            </select>
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
  name: 'FormKasus',
  props: {
    kasus: {
      type: Object,
      default: null,
    },
    kategoriList: {
      type: Array,
      default: () => [],
    },
  },
  emits: ['close', 'save'],
  setup(props, { emit }) {
    const loading = ref(false);
    const form = reactive({
      id: null,
      kode_kasus: '',
      nama_kasus: '',
      deskripsi: '',
      kategori: '',
      masa_retensi_aktif: 5,
      masa_retensi_inaktif: 2,
      status: 'Aktif',
    });

    // Pre-fill form jika edit
    watch(
      () => props.kasus,
      (newKasus) => {
        if (newKasus) {
          form.id = newKasus.id;
          form.kode_kasus = newKasus.kode_kasus;
          form.nama_kasus = newKasus.nama_kasus;
          form.deskripsi = newKasus.deskripsi || '';
          form.kategori = newKasus.kategori;
          form.masa_retensi_aktif = newKasus.masa_retensi_aktif;
          form.masa_retensi_inaktif = newKasus.masa_retensi_inaktif;
          form.status = newKasus.status;
        }
      },
      { immediate: true }
    );

    const submitForm = () => {
      loading.value = true;
      const formData = { ...form };
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
