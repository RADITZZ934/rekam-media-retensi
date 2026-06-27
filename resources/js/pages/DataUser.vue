<template>
  <div class="min-h-screen bg-gray-50 p-8">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Data User</h1>
      <p class="text-gray-600 mt-2">Kelola pengguna sistem dan hak akses</p>
    </div>

    <!-- Action Bar -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
      <div class="flex flex-col md:flex-row gap-4">
        <!-- Search -->
        <div class="flex-1 relative">
          <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <Search class="w-5 h-5 text-gray-400" />
          </span>
          <input
            v-model="searchQuery"
            @keyup="performSearch"
            type="text"
            placeholder="Cari username, nama, atau email..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <!-- Filter Role -->
        <select
          v-model="filterRole"
          @change="performSearch"
          class="md:w-40 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Semua Role</option>
          <option value="Administrator">Administrator</option>
          <option value="Staff">Staff</option>
        </select>

        <!-- Filter Status -->
        <select
          v-model="filterStatus"
          @change="performSearch"
          class="md:w-40 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Semua Status</option>
          <option value="Aktif">Aktif</option>
          <option value="Nonaktif">Nonaktif</option>
        </select>

        <!-- Tambah User Button -->
        <button
          v-if="isAdmin"
          @click="openFormModal(null)"
          class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-semibold"
        >
          + Tambah User
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-100 border-b border-gray-300">
          <tr class="text-[10px] font-bold uppercase tracking-widest text-gray-700">
            <th class="px-6 py-3 text-left">ID User</th>
            <th class="px-6 py-3 text-left">Username</th>
            <th class="px-6 py-3 text-left">Nama Lengkap</th>
            <th class="px-6 py-3 text-left">Email</th>
            <th class="px-6 py-3 text-center">Role</th>
            <th class="px-6 py-3 text-center">Status</th>
            <th class="px-6 py-3 text-left">Last Login</th>
            <th v-if="isAdmin" class="px-6 py-3 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="user in users"
            :key="user.id"
            class="border-b border-gray-200 hover:bg-gray-50"
          >
            <!-- ID User -->
            <td class="px-6 py-4 text-xs font-semibold text-blue-600">{{ user.id }}</td>

            <!-- Username -->
            <td class="px-6 py-4 text-xs font-medium text-gray-900">{{ user.username }}</td>

            <!-- Nama Lengkap -->
            <td class="px-6 py-4 text-xs text-gray-700">{{ user.nama_lengkap }}</td>

            <!-- Email -->
            <td class="px-6 py-4 text-xs text-gray-700">{{ user.email }}</td>

            <!-- Role Badge -->
            <td class="px-6 py-4 text-center">
              <span
                class="inline-block px-3 py-1 rounded-full text-[10px] font-semibold"
                :class="
                  user.role === 'Administrator'
                    ? 'bg-blue-100 text-blue-800'
                    : 'bg-gray-100 text-gray-800'
                "
              >
                {{ user.role }}
              </span>
            </td>

            <!-- Status Badge -->
            <td class="px-6 py-4 text-center">
              <span
                class="inline-block px-3 py-1 rounded-full text-[10px] font-semibold"
                :class="
                  user.status === 'Aktif'
                    ? 'bg-green-100 text-green-800'
                    : 'bg-red-100 text-red-800'
                "
              >
                {{ user.status }}
              </span>
            </td>

            <!-- Last Login -->
            <td class="px-6 py-4 text-xs text-gray-700">
              {{ user.last_login || '-' }}
            </td>

            <!-- Actions -->
            <td v-if="isAdmin" class="px-6 py-4 text-center text-xs">
              <div class="flex gap-2 justify-center">
                <!-- Edit Button -->
                <button
                  @click="openFormModal(user)"
                  class="inline-flex items-center justify-center w-9 h-9 border border-blue-300 text-blue-600 rounded hover:bg-blue-50"
                  title="Edit user"
                >
                  <Edit class="w-4 h-4" />
                </button>

                <!-- Delete Button -->
                <button
                  v-if="user.role !== 'Administrator'"
                  @click="deleteUser(user.id)"
                  class="inline-flex items-center justify-center w-9 h-9 border border-red-300 text-red-600 rounded hover:bg-red-50"
                  title="Hapus user"
                >
                  <Delete class="w-4 h-4" />
                </button>
              </div>
            </td>
          </tr>

          <!-- Empty State -->
          <tr v-if="users.length === 0">
            <td :colspan="isAdmin ? 8 : 7" class="px-6 py-12 text-center text-gray-500">
              Tidak ada data user
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="pagination" class="mt-6 flex justify-between items-center">
      <div class="text-sm text-gray-600">
        Menampilkan {{ pagination.from }} sampai {{ pagination.to }} dari {{ pagination.total }} user
      </div>
      <div class="flex gap-2">
        <button
          v-if="pagination.current_page > 1"
          @click="goToPage(pagination.current_page - 1)"
          class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50"
        >
          Previous
        </button>

        <button
          v-for="page in paginationPages"
          :key="page"
          @click="goToPage(page)"
          :class="
            page === pagination.current_page
              ? 'px-4 py-2 bg-blue-600 text-white rounded'
              : 'px-4 py-2 border border-gray-300 rounded hover:bg-gray-50'
          "
        >
          {{ page }}
        </button>

        <button
          v-if="pagination.current_page < pagination.last_page"
          @click="goToPage(pagination.current_page + 1)"
          class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50"
        >
          Next
        </button>
      </div>
    </div>

    <!-- Form Modal -->
    <FormUser
      v-if="showFormModal"
      :user="selectedUser"
      @close="showFormModal = false"
      @save="fetchUsers(pagination?.current_page || 1)"
    />
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import FormUser from '../components/FormUser.vue';
import { showSuccessToast, showErrorToast, showConfirmDialog } from '../utils/notification';

export default {
  name: 'DataUser',
  components: {
    FormUser,
  },
  setup() {
    const users = ref([]);
    const pagination = ref(null);
    const searchQuery = ref('');
    const filterRole = ref('');
    const filterStatus = ref('');
    const showFormModal = ref(false);
    const selectedUser = ref(null);
    const loading = ref(false);
    const authUser = ref({
      role: 'Staff'
    });

    const isAdmin = computed(() => authUser.value.role === 'Administrator');

    const loadAuthUser = () => {
      const stored = localStorage.getItem('auth_user')
      if (stored) {
        try {
          authUser.value = JSON.parse(stored)
        } catch (e) {
          console.error(e)
        }
      }
    };

    const paginationPages = computed(() => {
      if (!pagination.value) return [];
      const pages = [];
      const total = pagination.value.last_page;
      const current = pagination.value.current_page;

      if (total <= 5) {
        for (let i = 1; i <= total; i++) {
          pages.push(i);
        }
      } else {
        if (current > 3) pages.push(1);
        if (current > 4) pages.push('...');

        for (let i = Math.max(1, current - 2); i <= Math.min(total, current + 2); i++) {
          pages.push(i);
        }

        if (current < total - 3) pages.push('...');
        if (current < total - 2) pages.push(total);
      }

      return pages;
    });

    const fetchUsers = async (page = 1) => {
      try {
        loading.value = true;
        const params = new URLSearchParams({
          page,
          per_page: 10,
          search: searchQuery.value,
          role: filterRole.value,
          status: filterStatus.value,
        });

        const response = await fetch(`/api/users?${params}`);
        const data = await response.json();

        users.value = data.data || [];
        pagination.value = {
          current_page: data.current_page,
          from: data.from,
          to: data.to,
          total: data.total,
          last_page: data.last_page,
        };
      } catch (error) {
        console.error('Error fetching users:', error);
        await showErrorToast('Gagal memuat data user');
      } finally {
        loading.value = false;
      }
    };

    const performSearch = () => {
      fetchUsers(1);
    };

    const goToPage = (page) => {
      if (page !== '...') {
        fetchUsers(page);
      }
    };

    const openFormModal = (user) => {
      selectedUser.value = user;
      showFormModal.value = true;
    };



    const deleteUser = async (userId) => {
      const result = await showConfirmDialog(
        'Hapus User?',
        'Apakah Anda yakin ingin menghapus user ini?',
        'Ya, Hapus',
        'Batal',
        '#dc2626'
      );

      if (!result.isConfirmed) {
        return;
      }

      try {
        const response = await fetch(`/api/users/${userId}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
          },
        });

        const data = await response.json();

        if (response.ok) {
          await showSuccessToast(data.message || 'User berhasil dihapus');
          fetchUsers(pagination.value?.current_page || 1);
        } else {
          await showErrorToast(data.message || 'Terjadi kesalahan');
        }
      } catch (error) {
        console.error('Error deleting user:', error);
        await showErrorToast('Gagal menghapus user');
      }
    };

    onMounted(() => {
      loadAuthUser();
      if (!isAdmin.value) {
        window.location.href = '/';
        return;
      }
      fetchUsers();
    });

    return {
      users,
      pagination,
      searchQuery,
      filterRole,
      filterStatus,
      showFormModal,
      selectedUser,
      loading,
      paginationPages,
      performSearch,
      goToPage,
      openFormModal,
      deleteUser,
      isAdmin,
    };
  },
};
</script>

<style scoped>
/* No additional styles needed - using Tailwind CSS */
</style>
