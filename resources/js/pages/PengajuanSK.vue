<template>
  <div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Page info -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <p class="text-sm text-gray-500 mt-1">Mengelola pengajuan Surat Keputusan (SK) persetujuan dan pembentukan tim pemusnahan berkas Rekam Medis.</p>
      </div>
      <!-- Action Buttons for Admin -->
      <button 
        v-if="userRole === 'Administrator'"
        @click="openCreateModal"
        class="bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-5 py-2.5 rounded-xl font-bold shadow-md hover:shadow-lg transition flex items-center gap-2 cursor-pointer text-sm"
      >
        <Plus class="w-4 h-4" />
        Buat Pengajuan Baru
      </button>
    </div>

    <!-- Filter Card (Replacing Statistics Card) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
      <!-- Search Input -->
      <div class="relative w-full md:w-72">
        <input 
          v-model="searchQuery"
          type="text" 
          placeholder="Cari nomor SK..."
          class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 pl-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
        />
        <Search class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" />
      </div>

      <!-- Dropdown Filters -->
      <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto flex-1 justify-end">
        <!-- Filter Tahun -->
        <div class="w-full md:w-40">
          <select 
            v-model="yearFilter"
            class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all cursor-pointer w-full"
          >
            <option value="">Semua Tahun</option>
            <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
          </select>
        </div>

        <!-- Filter Nama Kepala Tim -->
        <div class="w-full md:w-48">
          <input 
            v-model="ketuaTimFilter"
            type="text"
            placeholder="Cari Ketua Tim..."
            class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all w-full"
          />
        </div>

        <!-- Filter Status -->
        <div class="w-full md:w-40">
          <select 
            v-model="statusFilter"
            class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all cursor-pointer w-full"
          >
            <option value="">Semua Status</option>
            <option value="Pending">Pending</option>
            <option value="Approved">Approved</option>
            <option value="Declined">Declined</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Main Content Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

      <!-- Loading State -->
      <div v-if="loading" class="py-20 flex flex-col items-center justify-center gap-3">
        <div class="w-10 h-10 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
        <p class="text-sm font-semibold text-gray-500">Memuat data pengajuan SK...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredSKList.length === 0" class="py-20 flex flex-col items-center justify-center text-center">
        <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-300 mb-4">
          <Folder class="w-8 h-8" />
        </div>
        <p class="font-bold text-gray-700 text-lg mb-1">Belum Ada Pengajuan SK</p>
        <p class="text-sm text-gray-400 max-w-sm px-4">Seluruh berkas pengajuan SK persetujuan pemusnahan Rekam Medis akan dicantumkan di halaman ini.</p>
      </div>

      <!-- Data Table -->
      <div v-else class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-blue-600 text-white font-semibold text-sm">
              <th class="py-4 px-6">No</th>
              <th class="py-4 px-6">No SK</th>
              <th class="py-4 px-6">Tanggal Pengajuan</th>
              <th class="py-4 px-6">Ketua Tim</th>
              <th class="py-4 px-6 text-center">Jumlah Berkas</th>
              <th class="py-4 px-6 text-center">Status</th>
              <th class="py-4 px-6">Catatan Direktur</th>
              <th class="py-4 px-6 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 text-sm">
            <tr v-for="(sk, idx) in filteredSKList" :key="sk.id" class="hover:bg-blue-50/20 transition">
              <td class="py-4 px-6 font-medium text-gray-500">{{ idx + 1 }}</td>
              <td class="py-4 px-6 font-bold text-gray-800">{{ sk.no_sk }}</td>
              <td class="py-4 px-6 text-gray-600">{{ sk.tanggal_pengajuan }}</td>
              <td class="py-4 px-6 text-gray-700">{{ sk.ketua_tim }}</td>
              <td class="py-4 px-6 text-center font-semibold text-blue-600">{{ sk.jumlah_berkas }} Berkas</td>
              <td class="py-4 px-6 text-center">
                <span :class="statusBadge(sk.status)" class="px-3 py-1 rounded-full text-xs font-bold shadow-sm">
                  {{ sk.status }}
                </span>
              </td>
              <td class="py-4 px-6 text-gray-500 italic max-w-xs truncate" :title="sk.keterangan">{{ sk.keterangan }}</td>
              <td class="py-4 px-6 text-center">
                <button 
                  @click="openDetailModal(sk)"
                  class="bg-indigo-50 hover:bg-indigo-100 text-indigo-600 active:scale-95 px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 mx-auto cursor-pointer"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  Detail / Aksi
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- CREATE SUBMISSION MODAL (Admin POV) -->
    <div v-if="createModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <!-- Backdrop -->
      <div @click="closeCreateModal" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
      
      <!-- Modal Box -->
      <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl relative z-10 overflow-hidden animate-scale-up max-h-[92vh] flex flex-col">
        <!-- Modal Header -->
        <div class="bg-blue-900 text-white p-5 flex items-center justify-between">
          <div>
            <h3 class="text-lg font-bold">Buat Pengajuan SK Baru</h3>
            <p class="text-xs text-blue-200">Pengajuan Surat Keputusan & Pembentukan Tim Pemusnahan</p>
          </div>
          <button @click="closeCreateModal" class="text-white hover:text-red-300 transition cursor-pointer">
            <Close class="w-6 h-6" />
          </button>
        </div>

        <!-- Modal Body Form -->
        <form @submit.prevent="submitCreateSK" class="flex-1 overflow-y-auto p-6 space-y-5">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- No SK -->
            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase mb-2">No SK <span class="text-red-500">*</span></label>
              <input 
                v-model="form.no_sk"
                type="text"
                required
                placeholder="Contoh: SK/2026/VII/RM001"
                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <!-- Tanggal Pengajuan -->
            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tanggal Pengajuan</label>
              <input 
                v-model="form.tanggal_pengajuan"
                type="date"
                disabled
                class="w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm text-gray-400 focus:outline-none"
              />
            </div>
          </div>

          <!-- Ketua Tim -->
          <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Ketua Tim <span class="text-red-500">*</span></label>
            <input 
              v-model="form.ketua_tim"
              type="text"
              required
              placeholder="Ketikkan nama lengkap Ketua Tim"
              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <!-- Anggota Tim -->
          <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-3">Anggota Tim Pemusnahan <span class="text-red-500">*</span></label>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <!-- Anggota Tim 1 -->
              <div>
                <input 
                  v-model="form.anggota_tim_1"
                  type="text"
                  required
                  placeholder="Nama Anggota Tim 1"
                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <!-- Anggota Tim 2 -->
              <div v-if="jumlahAnggotaSelect >= 2">
                <input 
                  v-model="form.anggota_tim_2"
                  type="text"
                  :required="jumlahAnggotaSelect >= 2"
                  placeholder="Nama Anggota Tim 2"
                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <!-- Anggota Tim 3 -->
              <div v-if="jumlahAnggotaSelect >= 3">
                <input 
                  v-model="form.anggota_tim_3"
                  type="text"
                  :required="jumlahAnggotaSelect >= 3"
                  placeholder="Nama Anggota Tim 3"
                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <!-- Anggota Tim 4 -->
              <div v-if="jumlahAnggotaSelect >= 4">
                <input 
                  v-model="form.anggota_tim_4"
                  type="text"
                  :required="jumlahAnggotaSelect >= 4"
                  placeholder="Nama Anggota Tim 4"
                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <!-- Anggota Tim 5 -->
              <div v-if="jumlahAnggotaSelect >= 5">
                <input 
                  v-model="form.anggota_tim_5"
                  type="text"
                  :required="jumlahAnggotaSelect >= 5"
                  placeholder="Nama Anggota Tim 5"
                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <!-- Anggota Tim 6 -->
              <div v-if="jumlahAnggotaSelect >= 6">
                <input 
                  v-model="form.anggota_tim_6"
                  type="text"
                  :required="jumlahAnggotaSelect >= 6"
                  placeholder="Nama Anggota Tim 6"
                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <!-- Anggota Tim 7 -->
              <div v-if="jumlahAnggotaSelect >= 7">
                <input 
                  v-model="form.anggota_tim_7"
                  type="text"
                  :required="jumlahAnggotaSelect >= 7"
                  placeholder="Nama Anggota Tim 7"
                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <!-- Anggota Tim 8 -->
              <div v-if="jumlahAnggotaSelect >= 8">
                <input 
                  v-model="form.anggota_tim_8"
                  type="text"
                  :required="jumlahAnggotaSelect >= 8"
                  placeholder="Nama Anggota Tim 8"
                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
            </div>

            <!-- Action buttons (+ and - box) to add/remove members -->
            <div class="flex items-center gap-2 mt-2">
              <button 
                v-if="jumlahAnggotaSelect < 8"
                type="button"
                @click="jumlahAnggotaSelect++"
                class="w-10 h-10 border border-dashed border-blue-300 rounded-xl bg-blue-50/50 text-blue-600 hover:bg-blue-50 flex items-center justify-center font-bold text-lg transition cursor-pointer focus:outline-none"
                title="Tambah Anggota"
              >
                <Plus class="w-5 h-5" />
              </button>
              <button 
                v-if="jumlahAnggotaSelect > 1"
                type="button"
                @click="jumlahAnggotaSelect--"
                class="w-10 h-10 border border-dashed border-red-300 rounded-xl bg-red-50/50 text-red-600 hover:bg-red-50 flex items-center justify-center font-bold text-lg transition cursor-pointer focus:outline-none"
                title="Kurangi Anggota"
              >
                <Minus class="w-5 h-5" />
              </button>
              <span class="text-xs text-gray-400 font-semibold ml-1">Klik kotak plus (+) di atas untuk menambah anggota tim (maks. 8)</span>
            </div>
          </div>

          <!-- Upload File Laporan Pemusnahan (CSV/XLSX) -->
          <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Upload File Laporan Pemusnahan (CSV / XLSX) <span class="text-xs text-gray-400 font-normal">(Opsional - Jika kosong, semua berkas antrean akan diajukan)</span></label>
            <div 
              class="relative w-full border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50 transition-all flex flex-col items-center justify-center py-7 px-4 group hover:border-blue-600 hover:bg-blue-50/10 cursor-pointer"
              :class="{'border-blue-600 bg-blue-50/10': selectedFile}"
            >
              <input 
                type="file" 
                ref="fileInput" 
                @change="handleFileSelect" 
                accept=".csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" 
                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" 
              />
              
              <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-2 transition-transform group-hover:scale-105">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5z"/>
                  <path d="M12 17v-6m-2.5 2.5L12 11l2.5 2.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                </svg>
              </div>

              <h4 class="text-sm font-bold text-gray-700 mb-0.5" v-if="!selectedFile">Pilih atau Seret File Laporan Pemusnahan</h4>
              <h4 class="text-sm font-bold text-blue-700 mb-0.5" v-else>{{ selectedFile.name }}</h4>
              
              <p class="text-xs text-gray-400 text-center" v-if="!selectedFile">Mendukung format .csv atau .xlsx</p>
              <p class="text-xs text-blue-600 text-center flex items-center justify-center gap-1.5 mt-1" v-else>
                File siap diunggah. Klik / seret file baru untuk mengganti.
                <button type="button" @click.stop="clearSelectedFile" class="text-red-500 hover:text-red-700 font-bold ml-1.5 focus:outline-none relative z-20 cursor-pointer">Hapus</button>
              </p>
            </div>
          </div>

          <!-- Summary Info / Lampiran -->
          <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 flex gap-4 items-center">
            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
              <Document class="w-6 h-6" />
            </div>
            <div>
              <p class="text-sm font-bold text-blue-800">Lampiran: Laporan Pemusnahan Berkas</p>
              <p class="text-xs text-blue-600 mt-0.5" v-if="!selectedFile">
                Sistem akan secara otomatis menyertakan sebanyak <strong>{{ form.jumlah_berkas }} berkas</strong> Rekam Medis yang saat ini dalam antrean status Siap Dimusnahkan.
              </p>
              <p class="text-xs text-blue-600 mt-0.5" v-else>
                Sistem akan menyaring dan melampirkan berkas Rekam Medis yang tercantum di dalam file <strong>{{ selectedFile.name }}</strong> yang Anda unggah.
              </p>
            </div>
          </div>

          <!-- Modal Footer Actions -->
          <div class="border-t border-gray-100 pt-5 flex items-center justify-end gap-3">
            <button 
              type="button"
              @click="closeCreateModal"
              class="border border-gray-200 text-gray-500 hover:bg-gray-50 px-5 py-2.5 rounded-xl text-sm font-bold transition cursor-pointer"
            >
              Batal
            </button>
            <button 
              type="submit"
              :disabled="submitting || (!selectedFile && form.jumlah_berkas === 0)"
              class="bg-blue-600 hover:bg-blue-700 text-white disabled:bg-blue-400 px-6 py-2.5 rounded-xl text-sm font-bold shadow-md hover:shadow-lg transition flex items-center gap-2 cursor-pointer"
            >
              <template v-if="submitting">
                <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                Memproses...
              </template>
              <template v-else>
                Ajukan
              </template>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- DETAIL & persetujuan MODAL (Admin & Direktur POV) -->
    <div v-if="detailModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <!-- Backdrop -->
      <div @click="closeDetailModal" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
      
      <!-- Modal Box -->
      <div class="bg-white w-full max-w-3xl rounded-2xl shadow-2xl relative z-10 overflow-hidden animate-scale-up max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="bg-blue-900 text-white p-5 flex items-center justify-between">
          <div>
            <h3 class="text-lg font-bold">Detail SK Pemusnahan</h3>
            <p class="text-xs text-blue-200">Detail data tim pemusnahan dan berkas terlampir</p>
          </div>
          <button @click="closeDetailModal" class="text-white hover:text-red-300 transition cursor-pointer">
            <Close class="w-6 h-6" />
          </button>
        </div>

        <!-- Modal Body Content -->
        <div v-if="detailLoading" class="p-10 flex flex-col items-center justify-center gap-3">
          <div class="w-8 h-8 border-3 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
          <p class="text-sm text-gray-500">Memuat detail pengajuan...</p>
        </div>

        <div v-else class="flex-1 overflow-y-auto p-6 space-y-6">
          <!-- Information Block -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 rounded-2xl p-5 border border-gray-100">
            <div class="space-y-3">
              <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">No SK</p>
                <p class="text-sm font-bold text-gray-800">{{ activeSK.no_sk }}</p>
              </div>
              <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tanggal Pengajuan</p>
                <p class="text-sm font-semibold text-gray-700">{{ activeSK.tanggal_pengajuan }}</p>
              </div>
              <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status Persetujuan</p>
                <span :class="statusBadge(activeSK.status)" class="px-3 py-1 rounded-full text-xs font-bold shadow-sm inline-block mt-1.5">
                  {{ activeSK.status }}
                </span>
              </div>
            </div>
            <div class="space-y-3">
              <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Ketua Tim</p>
                <p class="text-sm font-bold text-gray-700">{{ activeSK.ketua_tim }}</p>
              </div>
              <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Anggota Tim</p>
                <div class="flex flex-wrap gap-1.5 mt-1.5">
                  <span class="bg-blue-50 text-blue-800 text-xs px-2.5 py-1 rounded-lg font-medium border border-blue-100">{{ activeSK.anggota_tim_1 }}</span>
                  <span v-if="activeSK.anggota_tim_2 && activeSK.anggota_tim_2 !== '-'" class="bg-blue-50 text-blue-800 text-xs px-2.5 py-1 rounded-lg font-medium border border-blue-100">{{ activeSK.anggota_tim_2 }}</span>
                  <span v-if="activeSK.anggota_tim_3 && activeSK.anggota_tim_3 !== '-'" class="bg-blue-50 text-blue-800 text-xs px-2.5 py-1 rounded-lg font-medium border border-blue-100">{{ activeSK.anggota_tim_3 }}</span>
                  <span v-if="activeSK.anggota_tim_4 && activeSK.anggota_tim_4 !== '-'" class="bg-blue-50 text-blue-800 text-xs px-2.5 py-1 rounded-lg font-medium border border-blue-100">{{ activeSK.anggota_tim_4 }}</span>
                  <span v-if="activeSK.anggota_tim_5 && activeSK.anggota_tim_5 !== '-'" class="bg-blue-50 text-blue-800 text-xs px-2.5 py-1 rounded-lg font-medium border border-blue-100">{{ activeSK.anggota_tim_5 }}</span>
                  <span v-if="activeSK.anggota_tim_6 && activeSK.anggota_tim_6 !== '-'" class="bg-blue-50 text-blue-800 text-xs px-2.5 py-1 rounded-lg font-medium border border-blue-100">{{ activeSK.anggota_tim_6 }}</span>
                  <span v-if="activeSK.anggota_tim_7 && activeSK.anggota_tim_7 !== '-'" class="bg-blue-50 text-blue-800 text-xs px-2.5 py-1 rounded-lg font-medium border border-blue-100">{{ activeSK.anggota_tim_7 }}</span>
                  <span v-if="activeSK.anggota_tim_8 && activeSK.anggota_tim_8 !== '-'" class="bg-blue-50 text-blue-800 text-xs px-2.5 py-1 rounded-lg font-medium border border-blue-100">{{ activeSK.anggota_tim_8 }}</span>
                </div>
              </div>
              <!-- Download File Laporan if exists -->
              <div v-if="activeSK.file_laporan">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Lampiran Laporan (File)</p>
                <a 
                  :href="activeSK.file_laporan" 
                  target="_blank" 
                  download
                  class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1.5 mt-1.5 hover:underline"
                >
                  <Document class="w-4.5 h-4.5" />
                  Unduh: {{ activeSK.file_laporan_name }}
                </a>
              </div>
              <div v-if="activeSK.keterangan && activeSK.keterangan !== '-'">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Catatan Direktur</p>
                <p class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-xl border border-red-100 mt-1 font-medium">{{ activeSK.keterangan }}</p>
              </div>
            </div>
          </div>

          <!-- Document List Block -->
          <div>
            <div class="flex items-center justify-between mb-3.5">
              <h4 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                <Document class="w-4 h-4 text-blue-600" />
                Lampiran Laporan Berkas Terlampir ({{ activeSK.berkas ? activeSK.berkas.length : 0 }})
              </h4>
            </div>
            <div class="border border-gray-100 rounded-2xl overflow-hidden max-h-56 overflow-y-auto">
              <table class="w-full text-left border-collapse">
                <thead>
                  <tr class="bg-gray-50 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase">
                    <th class="py-2.5 px-4 w-12">No</th>
                    <th class="py-2.5 px-4">No RM</th>
                    <th class="py-2.5 px-4">Nama Pasien</th>
                    <th class="py-2.5 px-4">Tanggal Retensi</th>
                    <th class="py-2.5 px-4 text-center">Status</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-xs text-gray-600">
                  <tr v-for="(doc, dIdx) in activeSK.berkas" :key="doc.id" class="hover:bg-gray-50/50">
                    <td class="py-2 px-4 font-semibold text-gray-400">{{ dIdx + 1 }}</td>
                    <td class="py-2 px-4 font-bold text-gray-700">{{ doc.no_rm }}</td>
                    <td class="py-2 px-4 font-semibold text-gray-800">{{ doc.nama_pasien }}</td>
                    <td class="py-2 px-4">{{ doc.tanggal_retensi }}</td>
                    <td class="py-2 px-4 text-center font-bold" :class="doc.status === 'Dimusnahkan' ? 'text-red-500' : 'text-yellow-600'">
                      {{ doc.status }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Director Decision Form -->
          <div v-if="userRole === 'Direktur' && activeSK.status === 'Pending'" class="border-t border-gray-100 pt-5 space-y-4">
            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Catatan Persetujuan / Penolakan (Opsional)</label>
              <textarea 
                v-model="decisionForm.keterangan"
                rows="3"
                placeholder="Tuliskan catatan Anda sebagai Direktur disini jika diperlukan..."
                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              ></textarea>
            </div>
            <div class="flex items-center justify-end gap-3">
              <button 
                @click="submitDecision('decline')"
                :disabled="decisionLoading"
                class="bg-red-50 hover:bg-red-600 text-red-600 hover:text-white border border-red-200 hover:border-transparent px-5 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-1.5 cursor-pointer"
              >
                <Close class="w-4 h-4" />
                Decline (Tolak)
              </button>
              <button 
                @click="submitDecision('approve')"
                :disabled="decisionLoading"
                class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-md hover:shadow-lg transition flex items-center gap-1.5 cursor-pointer"
              >
                <Select class="w-4 h-4" />
                Approve (Setujui)
              </button>
            </div>
          </div>
        </div>

        <!-- Modal Footer Actions (Fallback for non-decision modals) -->
        <div v-if="!detailLoading && (userRole !== 'Direktur' || activeSK.status !== 'Pending')" class="border-t border-gray-100 p-4 bg-gray-50 flex items-center justify-end gap-3">
          <a 
            v-if="activeSK.status === 'Approved'"
            :href="`/api/pengajuan-pemusnahan/${activeSK.id}/download-ba`"
            target="_blank"
            class="bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-md hover:shadow-lg transition flex items-center gap-1.5 cursor-pointer text-decoration-none"
          >
            <Download class="w-4 h-4" />
            Unduh Berita Acara (PDF)
          </a>
          <button 
            @click="closeDetailModal"
            class="bg-white hover:bg-gray-100 text-gray-700 border border-gray-200 px-5 py-2 rounded-xl text-xs font-bold shadow-sm transition cursor-pointer"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { showSuccessToast, showErrorToast } from '../utils/notification'

// State
const loading = ref(false)
const listSK = ref([])
const userRole = ref('')
const searchQuery = ref('')
const statusFilter = ref('')
const yearFilter = ref('')
const ketuaTimFilter = ref('')

// Available Years derived dynamically from SK list
const availableYears = computed(() => {
  const years = listSK.value.map(sk => {
    if (!sk.tanggal_pengajuan) return null
    const datePart = sk.tanggal_pengajuan.split('/')
    if (datePart.length === 3) {
      return datePart[2] // yyyy
    }
    return sk.tanggal_pengajuan.split('-')[0] // yyyy
  })
  return [...new Set(years)].filter(Boolean).sort((a, b) => b - a)
})

// Filtered SK list
const filteredSKList = computed(() => {
  let result = listSK.value
  
  if (statusFilter.value) {
    result = result.filter(sk => sk.status === statusFilter.value)
  }

  if (yearFilter.value) {
    result = result.filter(sk => {
      if (!sk.tanggal_pengajuan) return false
      const datePart = sk.tanggal_pengajuan.split('/')
      const year = datePart.length === 3 ? datePart[2] : sk.tanggal_pengajuan.split('-')[0]
      return year === yearFilter.value
    })
  }

  if (ketuaTimFilter.value) {
    const q = ketuaTimFilter.value.toLowerCase()
    result = result.filter(sk => 
      sk.ketua_tim.toLowerCase().includes(q)
    )
  }

  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase()
    result = result.filter(sk => 
      sk.no_sk.toLowerCase().includes(q)
    )
  }

  return result
})

// Create Modal States
const createModalOpen = ref(false)
const submitting = ref(false)
const fileInput = ref(null)
const selectedFile = ref(null)

const form = ref({
  no_sk: '',
  tanggal_pengajuan: '',
  ketua_tim: '',
  anggota_tim_1: '',
  anggota_tim_2: '',
  anggota_tim_3: '',
  anggota_tim_4: '',
  anggota_tim_5: '',
  anggota_tim_6: '',
  anggota_tim_7: '',
  anggota_tim_8: '',
  jumlah_berkas: 0
})

const jumlahAnggotaSelect = ref(4)

// Detail Modal States
const detailModalOpen = ref(false)
const detailLoading = ref(false)
const activeSK = ref({})
const decisionLoading = ref(false)
const decisionForm = ref({
  keterangan: ''
})

// Load SK lists
const fetchSKList = async () => {
  loading.value = true
  try {
    const res = await axios.get('/api/pengajuan-pemusnahan')
    if (res.data.success) {
      listSK.value = res.data.data
    }
  } catch (err) {
    console.error(err)
    showErrorToast('Gagal memuat daftar pengajuan SK.')
  } finally {
    loading.value = false
  }
}

// File Select handler
const handleFileSelect = (e) => {
  const file = e.target.files[0]
  if (file) {
    const ext = file.name.split('.').pop().toLowerCase()
    if (!['csv', 'xlsx'].includes(ext)) {
      showErrorToast('Format file harus berupa CSV atau XLSX.')
      return
    }
    selectedFile.value = file
  }
}

const clearSelectedFile = () => {
  selectedFile.value = null
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

// Open create SK modal
const openCreateModal = async () => {
  // Check count of unassigned "dimusnahkan" documents
  try {
    const res = await axios.get('/api/pemusnahan')
    if (res.data.success) {
      const unassigned = res.data.data.filter(doc => doc.status === 'dimusnahkan' && !doc.pengajuan_id)
      form.value.jumlah_berkas = unassigned.length
    }
  } catch (err) {
    console.error(err)
    form.value.jumlah_berkas = 0
  }

  form.value.no_sk = ''
  form.value.tanggal_pengajuan = new Date().toISOString().split('T')[0]
  form.value.ketua_tim = ''
  form.value.anggota_tim_1 = ''
  form.value.anggota_tim_2 = ''
  form.value.anggota_tim_3 = ''
  form.value.anggota_tim_4 = ''
  form.value.anggota_tim_5 = ''
  form.value.anggota_tim_6 = ''
  form.value.anggota_tim_7 = ''
  form.value.anggota_tim_8 = ''
  jumlahAnggotaSelect.value = 4
  clearSelectedFile()
  createModalOpen.value = true
}

const closeCreateModal = () => {
  createModalOpen.value = false
}

// Submit new SK pengajuan
const submitCreateSK = async () => {
  if (!selectedFile.value && form.value.jumlah_berkas === 0) {
    showErrorToast('Tidak ada berkas (status Dimusnahkan) yang siap diajukan untuk pemusnahan.')
    return
  }

  submitting.value = true
  try {
    // We must use FormData because we are sending a file
    const formData = new FormData()
    formData.append('no_sk', form.value.no_sk)
    formData.append('tanggal_pengajuan', form.value.tanggal_pengajuan)
    formData.append('ketua_tim', form.value.ketua_tim)
    formData.append('anggota_tim_1', form.value.anggota_tim_1)
    
    // Clear and send other team members depending on the selected count
    formData.append('anggota_tim_2', (jumlahAnggotaSelect.value >= 2 && form.value.anggota_tim_2) ? form.value.anggota_tim_2 : '-')
    formData.append('anggota_tim_3', (jumlahAnggotaSelect.value >= 3 && form.value.anggota_tim_3) ? form.value.anggota_tim_3 : '-')
    formData.append('anggota_tim_4', (jumlahAnggotaSelect.value >= 4 && form.value.anggota_tim_4) ? form.value.anggota_tim_4 : '-')
    formData.append('anggota_tim_5', (jumlahAnggotaSelect.value >= 5 && form.value.anggota_tim_5) ? form.value.anggota_tim_5 : '-')
    formData.append('anggota_tim_6', (jumlahAnggotaSelect.value >= 6 && form.value.anggota_tim_6) ? form.value.anggota_tim_6 : '-')
    formData.append('anggota_tim_7', (jumlahAnggotaSelect.value >= 7 && form.value.anggota_tim_7) ? form.value.anggota_tim_7 : '-')
    formData.append('anggota_tim_8', (jumlahAnggotaSelect.value >= 8 && form.value.anggota_tim_8) ? form.value.anggota_tim_8 : '-')
    
    if (selectedFile.value) {
      formData.append('file_laporan', selectedFile.value)
    }

    const res = await axios.post('/api/pengajuan-pemusnahan', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    if (res.data.success) {
      showSuccessToast('Pengajuan SK Pemusnahan berhasil diajukan.')
      closeCreateModal()
      fetchSKList()
    }
  } catch (err) {
    console.error(err)
    const msg = err.response?.data?.message || 'Gagal menyimpan pengajuan SK.'
    showErrorToast(msg)
  } finally {
    submitting.value = false
  }
}

// Open details of SK
const openDetailModal = async (sk) => {
  detailModalOpen.value = true
  detailLoading.value = true
  try {
    const res = await axios.get(`/api/pengajuan-pemusnahan/${sk.id}`)
    if (res.data.success) {
      activeSK.value = res.data.data
      decisionForm.value.keterangan = ''
    }
  } catch (err) {
    console.error(err)
    showErrorToast('Gagal memuat detail SK.')
    closeDetailModal()
  } finally {
    detailLoading.value = false
  }
}

const closeDetailModal = () => {
  detailModalOpen.value = false
}

// Approve or decline submission (Director only)
const submitDecision = async (action) => {
  decisionLoading.value = true
  try {
    const url = `/api/pengajuan-pemusnahan/${activeSK.value.id}/${action}`
    const res = await axios.post(url, { keterangan: decisionForm.value.keterangan })
    if (res.data.success) {
      showSuccessToast(action === 'approve' ? 'SK Pemusnahan berhasil disetujui!' : 'SK Pemusnahan berhasil ditolak.')
      closeDetailModal()
      fetchSKList()
    }
  } catch (err) {
    console.error(err)
    showErrorToast(err.response?.data?.message || 'Gagal menyimpan keputusan persetujuan.')
  } finally {
    decisionLoading.value = false
  }
}

// Helper to determine status badges styling
const statusBadge = (status) => {
  if (status === 'Approved') return 'bg-green-100 text-green-700 border border-green-200'
  if (status === 'Declined') return 'bg-red-100 text-red-700 border border-red-200'
  return 'bg-yellow-100 text-yellow-700 border border-yellow-200'
}

onMounted(() => {
  const stored = localStorage.getItem('auth_user')
  if (stored) {
    const user = JSON.parse(stored)
    userRole.value = user.role
  }
  fetchSKList()
})
</script>

<style scoped>
/* Scale up animation for modal box */
.animate-scale-up {
  animation: scaleUp 0.25s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

@keyframes scaleUp {
  from {
    opacity: 0;
    transform: scale(0.92) translateY(12px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}
</style>