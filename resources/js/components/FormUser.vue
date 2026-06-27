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
          {{ user ? 'Edit User' : 'Tambah User Baru' }}
        </h2>

        <!-- Form -->
        <form @submit.prevent="submitForm" class="space-y-6">
          <!-- Row 1: Username dan Email -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Username *</label>
              <input
                v-model="form.username"
                type="text"
                placeholder="username"
                :disabled="!!user"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
                required
                minlength="4"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
              <input
                v-model="form.email"
                type="email"
                placeholder="email@example.com"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              />
            </div>
          </div>

          <!-- Row 2: Nama Lengkap dan Password -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
              <input
                v-model="form.nama_lengkap"
                type="text"
                placeholder="Nama lengkap"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Password
                <span v-if="user" class="text-xs text-gray-500">(Biarkan kosong jika tidak diubah)</span>
                <span v-else class="text-red-500">*</span>
              </label>
              <input
                v-model="form.password"
                type="password"
                placeholder="••••••"
                :required="!user"
                minlength="6"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
          </div>

          <!-- Row 3: Role dan Status -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
              <select
                v-model="form.role"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              >
                <option value="">-- Pilih Role --</option>
                <option value="Administrator">Administrator</option>
                <option value="Staff">Staff</option>
              </select>
            </div>
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
import Swal from 'sweetalert2';

export default {
  name: 'FormUser',
  props: {
    user: {
      type: Object,
      default: null,
    },
  },
  emits: ['close', 'save'],
  setup(props, { emit }) {
    const loading = ref(false);
    const form = reactive({
      username: '',
      email: '',
      nama_lengkap: '',
      password: '',
      role: 'Staff',
      status: 'Aktif',
    });

    // Pre-fill form jika edit
    watch(
      () => props.user,
      (newUser) => {
        if (newUser) {
          form.username = newUser.username;
          form.email = newUser.email;
          form.nama_lengkap = newUser.nama_lengkap;
          form.role = newUser.role;
          form.status = newUser.status;
          form.password = ''; // Don't pre-fill password
        } else {
          form.username = '';
          form.email = '';
          form.nama_lengkap = '';
          form.password = '';
          form.role = 'Staff';
          form.status = 'Aktif';
        }
      },
      { immediate: true }
    );

    const submitForm = async () => {
      loading.value = true;

      const formData = {
        username: form.username,
        email: form.email,
        nama_lengkap: form.nama_lengkap,
        role: form.role,
        status: form.status,
      };

      // Only include password if it's provided
      if (form.password) {
        formData.password = form.password;
      }

      try {
        const isEdit = !!props.user;
        const method = isEdit ? 'PUT' : 'POST';
        const url = isEdit ? `/api/users/${props.user.id}` : '/api/users';

        const response = await fetch(url, {
          method,
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
          },
          body: JSON.stringify(formData),
        });

        const data = await response.json();

        if (!response.ok) {
          throw new Error(data.message || 'Terjadi kesalahan saat menyimpan data');
        }

        // Show success notification
        await Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: isEdit ? 'User berhasil diperbarui' : 'User berhasil ditambahkan',
          timer: 1500,
          timerProgressBar: true,
          showConfirmButton: false,
        });

        emit('save');
        emit('close');
      } catch (error) {
        loading.value = false;
        await Swal.fire({
          icon: 'error',
          title: 'Gagal!',
          text: error.message || 'Terjadi kesalahan saat menyimpan data',
        });
      }
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
