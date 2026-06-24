<template>
  <div>
    <div class="min-h-screen bg-gray-50 p-8">
    <!-- HEADER -->
    <div class="mb-8 flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-gray-100">
      <div class="flex items-center gap-3 text-[#2b3c5a]">
        <svg class="w-8 h-8" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
          <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
          <rect x="4" y="4" width="6" height="6" rx="1" />
          <rect x="14" y="4" width="6" height="6" rx="1" />
          <rect x="4" y="14" width="6" height="6" rx="1" />
          <path d="M14 14h.01" />
          <path d="M17 14h.01" />
          <path d="M20 14h.01" />
          <path d="M14 17h.01" />
          <path d="M17 17h.01" />
          <path d="M20 17h.01" />
          <path d="M14 20h.01" />
          <path d="M17 20h.01" />
          <path d="M20 20h.01" />
        </svg>
        <h1 class="text-2xl font-extrabold tracking-tight">Alih Media</h1>
      </div>

      <div class="flex items-center gap-3">
        <button 
          v-if="selectedIds.length > 0"
          @click="handleBulkDelete" 
          class="flex items-center gap-2 px-4 py-2 border-2 border-red-500 text-red-600 rounded-lg font-semibold hover:bg-red-50 transition-colors shadow-sm"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
          Hapus Terpilih ({{ selectedIds.length }})
        </button>
        <button 
          @click="showUploadForm = !showUploadForm" 
          class="flex items-center gap-2 px-4 py-2 border-2 border-blue-500 text-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition-colors shadow-sm"
        >
          <svg class="w-4 h-4" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <rect x="4" y="4" width="6" height="6" rx="1" />
            <rect x="14" y="4" width="6" height="6" rx="1" />
            <rect x="4" y="14" width="6" height="6" rx="1" />
            <path d="M14 17v.01" /><path d="M20 17v.01" /><path d="M17 14v.01" /><path d="M17 20v.01" /><path d="M14 14v.01" /><path d="M20 14v.01" /><path d="M14 20v.01" /><path d="M20 20v.01" />
          </svg>
          Scan Dokumen
        </button>
        <button @click="showManualDialog = true" class="flex items-center gap-2 px-4 py-2 border-2 border-yellow-500 text-yellow-600 rounded-lg font-semibold hover:bg-yellow-50 transition-colors shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
          Manual
        </button>
      </div>
    </div>

    <!-- UPLOAD SECTION -->
    <div 
      class="grid transition-all duration-500 ease-in-out"
      :class="showUploadForm ? 'grid-rows-[1fr] opacity-100 mb-8' : 'grid-rows-[0fr] opacity-0 mb-0'"
    >
      <div class="overflow-hidden">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <div class="mb-5">
            <div 
              class="relative w-full border-2 border-dashed border-[#caced6] rounded-xl bg-[#f8fafe] transition-all flex flex-col items-center justify-center py-10 px-4 group hover:border-[#0f4392] hover:bg-[#f0f4ff]"
              :class="{'border-[#0f4392] bg-[#f0f4ff]': currentSelectedFiles.length > 0}"
            >
              <input 
                type="file" 
                ref="fileInput" 
                @change="handleFileSelect" 
                multiple 
                accept=".pdf,.jpg,.jpeg,.png" 
                :disabled="uploading || processingOcr" 
                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" 
              />
              
              <div class="w-14 h-14 bg-[#e5ecfb] rounded-xl flex items-center justify-center mb-4 text-[#0f4392] transition-transform group-hover:scale-110">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5z"/>
                  <path d="M12 17v-6m-2.5 2.5L12 11l2.5 2.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                </svg>
              </div>

              <h3 class="text-lg font-bold text-[#202938] mb-1.5" v-if="currentSelectedFiles.length === 0">Drag and drop documents here</h3>
              <h3 class="text-lg font-bold text-[#0f4392] mb-1.5" v-else>{{ currentSelectedFiles.length }} File(s) Selected</h3>
              
              <p class="text-[14px] text-[#6b7280] mb-6 text-center" v-if="currentSelectedFiles.length === 0">Scan invoices, reports, or legal archives automatically.</p>
              <p class="text-[14px] text-[#0f4392] mb-6 text-center max-w-md truncate" v-else>{{ currentSelectedFiles.map(f => f.name).join(', ') }}</p>

              <button class="bg-[#0f4392] text-white font-semibold text-[14px] py-2 px-6 rounded-md mb-6 shadow-sm transition-colors relative z-0 pointer-events-none group-hover:bg-[#0c3676]">
                Choose File
              </button>

              <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 bg-[#e2e8f0] text-[#475569] text-[12px] font-bold rounded">PDF</span>
                <span class="px-2.5 py-1 bg-[#e2e8f0] text-[#475569] text-[12px] font-bold rounded">JPG</span>
                <span class="px-2.5 py-1 bg-[#e2e8f0] text-[#475569] text-[12px] font-bold rounded">PNG</span>
              </div>
            </div>
          </div>

          <!-- Progress Stepper -->
          <div class="relative flex items-center justify-between w-full mt-10 mb-14 px-2 mx-auto">
            <!-- Line Background -->
            <div class="absolute left-0 top-2.5 transform -translate-y-1/2 w-full h-1 bg-gray-500 rounded"></div>
            
            <!-- Step 1 -->
            <div class="relative z-10 flex flex-col items-center" style="width: 20px;">
              <div class="w-5 h-5 rounded-full transition-colors duration-300" :class="currentStep >= 1 ? 'bg-[#00c853]' : 'bg-gray-300'"></div>
              <span class="absolute top-7 left-1/2 transform -translate-x-1/2 whitespace-nowrap text-sm font-bold transition-colors duration-300" :class="currentStep >= 1 ? 'text-[#00c853]' : 'text-gray-400'">Upload</span>
            </div>
            
            <!-- Step 2 -->
            <div class="relative z-10 flex flex-col items-center" style="width: 20px;">
              <div class="w-5 h-5 rounded-full transition-colors duration-300" :class="currentStep >= 2 ? 'bg-[#7cb342]' : 'bg-gray-300'"></div>
              <span class="absolute top-7 left-1/2 transform -translate-x-1/2 whitespace-nowrap text-sm font-bold transition-colors duration-300 text-center leading-tight" :class="currentStep >= 2 ? 'text-[#7cb342]' : 'text-gray-400'">Convert to<br>image</span>
            </div>
            
            <!-- Step 3 -->
            <div class="relative z-10 flex flex-col items-center" style="width: 20px;">
              <div class="w-5 h-5 rounded-full transition-colors duration-300" :class="currentStep >= 3 ? 'bg-[#29b6f6]' : 'bg-gray-300'"></div>
              <span class="absolute top-7 left-1/2 transform -translate-x-1/2 whitespace-nowrap text-sm font-bold transition-colors duration-300" :class="currentStep >= 3 ? 'text-[#29b6f6]' : 'text-gray-400'">Process OCR</span>
            </div>

            <!-- Step 4 -->
            <div class="relative z-10 flex flex-col items-center" style="width: 20px;">
              <div class="w-5 h-5 rounded-full transition-colors duration-300" :class="currentStep >= 4 ? 'bg-[#5c6bc0]' : 'bg-gray-300'"></div>
              <span class="absolute top-7 left-1/2 transform -translate-x-1/2 whitespace-nowrap text-sm font-bold transition-colors duration-300" :class="currentStep >= 4 ? 'text-[#5c6bc0]' : 'text-gray-400'">Redirect..</span>
            </div>
          </div>

          <button 
            @click="startFullProcess" 
            :disabled="uploading || processingOcr || redirecting || currentSelectedFiles.length === 0"
            class="w-full py-3 bg-[#113fb6] hover:bg-blue-800 text-white rounded-lg font-bold shadow-md transition-colors flex items-center justify-center gap-2 disabled:bg-gray-400 disabled:cursor-not-allowed"
          >
            <svg v-if="uploading || processingOcr || redirecting" class="animate-spin w-5 h-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
            {{ redirecting ? 'Mengalihkan...' : (processingOcr ? 'Sedang Memproses OCR...' : (uploading ? 'Sedang Upload...' : 'Upload & Proses OCR')) }}
          </button>


        </div>
      </div>
    </div>

    <!-- SEARCH BAR -->
    <div class="bg-white rounded-xl shadow-sm p-5 mb-6 border border-gray-100">
      <div class="flex flex-col md:flex-row gap-4 items-end">
        <div class="flex-1">
          <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama File</label>
          <input 
            v-model="searchNamaFile" 
            @keyup.enter="handleSearch"
            type="text" 
            placeholder="Masukkan nama file" 
            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-shadow"
          />
        </div>
        <div class="flex-1">
          <label class="block text-sm font-semibold text-gray-700 mb-1.5">No. RM</label>
          <input 
            v-model="searchNoRm" 
            @keyup.enter="handleSearch"
            type="text" 
            placeholder="Masukkan nomor RM" 
            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-shadow"
          />
        </div>
          <button 
            @click="handleSearch"
            class="px-8 py-2.5 bg-[#2b3c5a] hover:bg-[#1f2e47] text-white rounded-lg font-semibold text-sm transition-colors flex items-center gap-2 shadow-sm whitespace-nowrap"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            Search
          </button>
          <button 
            @click="fetchDokumen"
            class="p-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-600 rounded-lg transition-colors shadow-sm"
            title="Refresh Data"
            :disabled="loading"
          >
            <svg class="w-5 h-5" :class="{'animate-spin': loading}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
          </button>
        </div>
      </div>

    <!-- DATA TABLE -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[800px]">
          <thead class="bg-blue-600 text-white">
            <tr class="text-sm font-semibold">
              <th class="px-6 py-4 text-left" style="width: 40px;">
                <input 
                  type="checkbox" 
                  :checked="isAllSelected" 
                  @change="toggleSelectAll"
                  class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
              </th>
              <th class="px-6 py-4 text-left">No</th>
              <th class="px-6 py-4 text-left">Nama File</th>
              <th class="px-6 py-4 text-left">No. RM</th>
              <th v-if="showEngineColumn" class="px-6 py-4 text-left">Engine</th>
              <th class="px-6 py-4 text-left">Petugas</th>
              <th class="px-6 py-4 text-left">Tanggal Upload</th>
              <th class="px-6 py-4 text-center">Status</th>
              <th class="px-6 py-4 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <!-- Loading Skeleton -->
            <tr v-if="loading && dokumentList.length === 0" v-for="i in 5" :key="'skel'+i" class="animate-pulse border-b border-gray-100">
              <td class="py-4 px-4"><div class="h-4 bg-gray-200 rounded w-6"></div></td>
              <td class="py-4 px-4"><div class="h-4 bg-gray-200 rounded w-32 md:w-48"></div></td>
              <td class="py-4 px-4"><div class="h-4 bg-gray-200 rounded w-20"></div></td>
              <td v-if="showEngineColumn" class="py-4 px-4"><div class="h-4 bg-gray-200 rounded w-16"></div></td>
              <td class="py-4 px-4"><div class="h-4 bg-gray-200 rounded w-24"></div></td>
              <td class="py-4 px-4"><div class="h-4 bg-gray-200 rounded w-24"></div></td>
              <td class="py-4 px-4 text-center"><div class="h-6 bg-gray-200 rounded-full w-20 mx-auto"></div></td>
              <td class="py-4 px-4">
                <div class="flex items-center justify-center gap-1.5">
                  <div class="w-8 h-8 rounded-md bg-gray-200"></div>
                  <div class="w-8 h-8 rounded-md bg-gray-200"></div>
                  <div class="w-8 h-8 rounded-md bg-gray-200"></div>
                </div>
              </td>
            </tr>
            <!-- Empty -->
            <tr v-else-if="dokumentList.length === 0">
              <td :colspan="showEngineColumn ? 9 : 8" class="py-12 text-center text-gray-400">Belum ada data dokumen</td>
            </tr>
            <!-- Data Rows -->
            <tr v-for="(doc, index) in dokumentList" :key="doc.id" class="border-b border-gray-100 hover:bg-gray-50/70 transition-colors" :class="{'bg-blue-50/50': selectedIds.includes(doc.id)}">
              <td class="py-3 px-4">
                <input 
                  type="checkbox" 
                  v-model="selectedIds" 
                  :value="doc.id"
                  class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
              </td>
              <td class="py-3 px-4 text-sm text-gray-600">{{ (currentPage - 1) * perPage + index + 1 }}</td>
              <td class="py-3 px-4 text-sm text-gray-900 font-medium max-w-[200px] truncate" :title="doc.nama_file">{{ doc.nama_file }}</td>
              <td class="py-3 px-4 text-sm text-gray-600">{{ doc.no_rm || '-' }}</td>
              <td v-if="showEngineColumn" class="py-3 px-4">
                <span v-if="doc.engine === 'gemini'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-blue-100 to-indigo-100 text-indigo-700 border border-indigo-200">
                  <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                  Gemini AI
                </span>
                <span v-else-if="doc.engine === 'tesseract'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-amber-100 to-orange-100 text-orange-700 border border-orange-200">
                  <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                  Tesseract <span class="text-[10px] font-medium opacity-70">(Fallback)</span>
                </span>
                <span v-else-if="doc.engine === 'yuulabs'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-teal-100 to-emerald-100 text-teal-700 border border-teal-200">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                  YuuLabs AI
                </span>
                <span v-else-if="doc.engine === 'manual'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                  Manual
                </span>
                <span v-else class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-400 border border-gray-200">
                  -
                </span>
              </td>
              <td class="py-3 px-4 text-sm text-gray-600">{{ doc.user_name || '-' }}</td>
              <td class="py-3 px-4 text-sm text-gray-600">{{ doc.tanggal_upload }}</td>
              <td class="py-3 px-4 text-center">
                <span 
                  class="inline-block px-3 py-1 rounded-full text-xs font-bold"
                  :class="getStatusBadgeClass(doc.status)"
                >
                  {{ formatStatus(doc.status) }}
                </span>
              </td>
              <td class="py-3 px-4">
                <div class="flex items-center justify-center gap-1.5">
                  <!-- Preview -->
                  <button @click="previewDokumen(doc)" class="w-8 h-8 rounded-md bg-blue-100 text-blue-600 hover:bg-blue-200 flex items-center justify-center transition-colors" title="Preview">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                  </button>
                  <!-- OCR / Validasi -->
                  <button 
                    v-if="doc.status === 'success' || doc.status === 'validated'"
                    @click="goToValidation(doc)" 
                    class="w-8 h-8 rounded-md bg-green-100 text-green-600 hover:bg-green-200 flex items-center justify-center transition-colors" 
                    title="Validasi"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                  </button>
                  <button 
                    v-if="!['success', 'validated', 'failed'].includes(doc.status)"
                    disabled 
                    class="w-8 h-8 rounded-md bg-gray-100 text-gray-300 flex items-center justify-center cursor-not-allowed" 
                    title="Menunggu proses..."
                  >
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                  </button>
                  <!-- Delete -->
                  <button @click="deleteDokumen(doc)" class="w-8 h-8 rounded-md bg-red-100 text-red-600 hover:bg-red-200 flex items-center justify-center transition-colors" title="Hapus">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Pagination -->
      <div v-if="totalDokumen > 0" class="px-6 py-4 flex items-center justify-center border-t border-gray-100">
        <div class="flex items-center gap-1">
          <button @click="prevPage" :disabled="currentPage === 1" class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-300 text-sm text-gray-500 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">&lt;</button>
          <button 
            v-for="page in pageNumbers" :key="page"
            @click="goToPage(page)" 
            class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-semibold transition-colors"
            :class="page === currentPage ? 'bg-[#4ea4f6] text-white shadow-md' : 'border border-gray-300 text-gray-600 hover:bg-gray-50'"
          >
            {{ page }}
          </button>
          <button @click="nextPage" :disabled="currentPage >= totalPages" class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-300 text-sm text-gray-500 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">&gt;</button>
        </div>
      </div>
    </div>
  </div>

  <!-- MANUAL DIALOG -->
  <teleport to="body">
    <transition name="fade">
      <div v-if="showManualDialog" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showManualDialog = false"></div>
        <!-- Dialog -->
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto z-10">
          <!-- Header -->
          <div class="flex items-center justify-between p-6 border-b border-gray-100">
            <h2 class="text-xl font-bold text-[#2b3c5a]">Input Data Manual</h2>
            <button @click="showManualDialog = false" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-600 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>
          <!-- Body -->
          <div class="p-6 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama File <span class="text-red-500">*</span></label>
                <input v-model="manualForm.nama_file" type="text" placeholder="Masukkan nama file" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">No. RM</label>
                <input v-model="manualForm.no_rm" type="text" placeholder="Masukkan nomor RM" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Pasien</label>
                <input v-model="manualForm.nama_pasien" type="text" placeholder="Masukkan nama pasien" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jenis Kelamin</label>
                <select v-model="manualForm.jenis_kelamin" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                  <option value="">Pilih</option>
                  <option value="Laki-laki">Laki-laki</option>
                  <option value="Perempuan">Perempuan</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Lahir</label>
                <input v-model="manualForm.tanggal_lahir" type="date" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Engine</label>
                <select v-model="manualForm.engine" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                  <option value="litellm">LiteLLM</option>
                  <option value="tesseract">Tesseract</option>
                  <option value="manual">Manual</option>
                </select>
              </div>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat</label>
              <textarea v-model="manualForm.alamat" rows="2" placeholder="Masukkan alamat pasien" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none resize-none"></textarea>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Upload File Dokumen</label>
              <div class="border border-gray-300 rounded-lg flex items-center bg-gray-50 relative overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 transition-shadow">
                <input type="file" @change="handleManualFile" accept=".pdf,.jpg,.jpeg,.png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                <div class="flex items-center w-full pointer-events-none">
                  <div class="px-4 py-2.5 bg-gray-200 border-r border-gray-300 text-sm font-medium text-gray-700 whitespace-nowrap">Choose File</div>
                  <div class="px-4 py-2.5 text-sm text-gray-500 flex-1 truncate bg-white">{{ manualForm.file ? manualForm.file.name : 'No file chosen' }}</div>
                </div>
              </div>
              <p class="text-xs text-gray-400 mt-1">Format: PDF, JPG, PNG | Max 10MB</p>
            </div>
          </div>
          <!-- Footer -->
          <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl">
            <button @click="showManualDialog = false" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-600 font-semibold text-sm hover:bg-gray-100 transition-colors">Batal</button>
            <button @click="submitManual" :disabled="submittingManual" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold text-sm transition-colors shadow-sm disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center gap-2">
              <svg v-if="submittingManual" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
              {{ submittingManual ? 'Menyimpan...' : 'Simpan Data' }}
            </button>
          </div>
        </div>
      </div>
    </transition>
  </teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { showSuccessToast, showErrorToast, showWarningToast, showConfirmDialog } from '../utils/notification';

const router = useRouter();
const uploading = ref(false);
const uploadProgress = ref([]);
const dokumentList = ref([]);
const loading = ref(true);
const showUploadForm = ref(false);
const uploadedDokumenIds = ref([]);
const processingOcr = ref(false);
const redirecting = ref(false);
const selectedIds = ref([]);
const userRole = ref('');
const showEngineColumn = ref(false);

const isAllSelected = computed(() => {
  return dokumentList.value.length > 0 && selectedIds.value.length === dokumentList.value.length;
});

const toggleSelectAll = () => {
  if (isAllSelected.value) {
    selectedIds.value = [];
  } else {
    selectedIds.value = dokumentList.value.map(doc => doc.id);
  }
};

const currentStep = computed(() => {
  if (redirecting.value) return 4;
  if (processingOcr.value) return 3;
  if (uploadedDokumenIds.value.length > 0) return 2;
  if (uploading.value) return 1;
  return 0;
});

const currentPage = ref(1);
const perPage = ref(10);
const totalDokumen = ref(0);
const currentSelectedFiles = ref([]);
const fileInput = ref(null);
const searchNamaFile = ref('');
const searchNoRm = ref('');
const showManualDialog = ref(false);
const submittingManual = ref(false);
const manualForm = ref({
  nama_file: '',
  no_rm: '',
  nama_pasien: '',
  jenis_kelamin: '',
  tanggal_lahir: '',
  engine: 'manual',
  alamat: '',
  file: null,
});

const totalPages = computed(() => Math.ceil(totalDokumen.value / perPage.value));
const pageNumbers = computed(() => {
  const pages = [];
  const maxVisible = 5;
  const half = Math.floor(maxVisible / 2);
  let start = Math.max(1, currentPage.value - half);
  let end = Math.min(totalPages.value, start + maxVisible - 1);
  if (end - start < maxVisible - 1) start = Math.max(1, end - maxVisible + 1);
  for (let i = start; i <= end; i++) pages.push(i);
  return pages;
});

// Mengambil list dokumen ke tabel
const fetchDokumen = async () => {
  loading.value = true;
  try {
    const params = new URLSearchParams({ page: currentPage.value, per_page: perPage.value });
    if (searchNamaFile.value) params.append('search', searchNamaFile.value);
    if (searchNoRm.value) params.append('no_rm', searchNoRm.value);
    const response = await fetch(`/api/alih-media?${params.toString()}`);
    const res = await response.json();
    if (res.success) {
      dokumentList.value = res.data;
      totalDokumen.value = res.total;
    }
  } catch (err) {
    console.error('Fetch error:', err);
  } finally {
    loading.value = false;
  }
};

const handleSearch = () => {
  currentPage.value = 1;
  selectedIds.value = [];
  fetchDokumen();
};

const goToPage = (page) => {
  currentPage.value = page;
  selectedIds.value = [];
  fetchDokumen();
};

// Handlers untuk File Input
const handleFileSelect = (e) => {
  const files = e.target.files;
  if (files && files.length > 0) {
    currentSelectedFiles.value = Array.from(files);
  } else {
    currentSelectedFiles.value = [];
  }
  // Reset state ocr ketika pilih file baru
  uploadedDokumenIds.value = [];
};

const startFullProcess = async () => {
  if (currentSelectedFiles.value.length === 0) return;

  // Step 1: Upload (dan convert otomatis dari API)
  uploading.value = true;
  const result = await executeUploadApi(currentSelectedFiles.value);
  uploading.value = false;

  if (result && result.redirect_url) {
    redirecting.value = true;
    showSuccessToast('Upload & Konversi Berhasil! Mengalihkan ke Validasi...');
    setTimeout(() => {
      router.push(result.redirect_url);
    }, 800);
  } else if (result && result.success) {
    showSuccessToast('Berhasil upload beberapa dokumen. Silakan pilih di tabel.');
    showUploadForm.value = false;
    currentSelectedFiles.value = [];
  }
};

// Validasi & Upload File Multi
const executeUploadApi = async (fileList) => {
  const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
  const maxSize = 10 * 1024 * 1024; // 10MB
  const validFiles = [];
  
  for (let i = 0; i < fileList.length; i++) {
    const file = fileList[i];
    if (!allowedTypes.includes(file.type)) {
      showWarningToast(`Format file ${file.name} tidak valid. Harus PDF/JPG/PNG.`);
      continue;
    }
    if (file.size > maxSize) {
      showWarningToast(`Ukuran file ${file.name} (Max 10MB) terlalu besar.`);
      continue;
    }
    validFiles.push(file);
  }

  if (validFiles.length === 0) return;

  uploading.value = true;
  uploadProgress.value = validFiles.map(f => f.name);

  try {
    const formData = new FormData();
    validFiles.forEach(file => {
      formData.append('files[]', file);
    });

    const response = await fetch('/api/alih-media/upload', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
      },
      body: formData
    });

    const res = await response.json();
    if (res.success) {
      await fetchDokumen();
      return res;
    } else {
      showErrorToast(res.message || 'Gagal mengupload file.');
      return null;
    }
  } catch (error) {
    console.error('Upload error:', error);
    showErrorToast('Terjadi kesalahan jaringan saat upload.');
    return null;
  } finally {
    uploadProgress.value = [];
  }
};

const executeOcrApi = async (id) => {
  // Update state to UI immediately
  const doc = dokumentList.value.find(d => d.id === id);
  if (doc) {
    doc.status = 'processing OCR';
  }

  try {
    const response = await fetch(`/api/alih-media/${id}/start-ocr`, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
      }
    });
    const res = await response.json();
    if (res.success) {
      redirecting.value = true;
      const engineLabel = res.engine === 'gemini' ? '✨ Gemini AI' : (res.engine === 'tesseract' ? '⚠️ Tesseract (Fallback)' : res.engine || 'Unknown');
      showSuccessToast(`OCR Berhasil menggunakan ${engineLabel}! Mengalihkan ke halaman validasi...`);
      // Delay sedikit agar user bisa melihat animasi Step 4
      setTimeout(() => {
        router.push(`/validasi-ocr?id=${res.dokumen_id || id}`);
      }, 700);
      return res.dokumen_id || id;
    } else {
      showErrorToast(res.message || 'Gagal menjalankan OCR.');
      fetchDokumen(); // revert status
      return null;
    }
  } catch (err) {
    console.error('Start OCR Err:', err);
    showErrorToast('Kesalahan server saat memproses OCR.');
    fetchDokumen(); // revert 
    return null;
  }
};

// Hapus Dokumen
const deleteDokumen = async (doc) => {
  const confirm = await showConfirmDialog(
    'Hapus Dokumen?',
    `Yakin ingin menghapus dokumen "${doc.nama_file}"? Semua data yang terkait akan terhapus.`,
    'Ya, Hapus', 'Batal', '#ef4444'
  );
  if (confirm.isConfirmed) {
    try {
      const response = await fetch(`/api/alih-media/${doc.id}`, {
        method: 'DELETE',
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        }
      });
      const res = await response.json();
      if (res.success) {
        showSuccessToast('Dokumen berhasil dihapus.');
        fetchDokumen();
      }
    } catch (e) {
      showErrorToast('Gagal menghapus dokumen.');
    }
  }
};

const handleBulkDelete = async () => {
  if (selectedIds.value.length === 0) return;

  const confirm = await showConfirmDialog(
    'Hapus Massal?',
    `Yakin ingin menghapus ${selectedIds.value.length} dokumen terpilih?`,
    'Ya, Hapus Semua', 'Batal', '#ef4444'
  );

  if (confirm.isConfirmed) {
    try {
      const response = await fetch('/api/alih-media/bulk', {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        },
        body: JSON.stringify({ ids: selectedIds.value })
      });
      const res = await response.json();
      if (res.success) {
        showSuccessToast(res.message || 'Dokumen berhasil dihapus.');
        selectedIds.value = [];
        fetchDokumen();
      } else {
        showErrorToast(res.message || 'Gagal menghapus dokumen.');
      }
    } catch (e) {
      console.error('Bulk delete error:', e);
      showErrorToast('Terjadi kesalahan saat menghapus massal.');
    }
  }
};

const goToValidation = (doc) => {
  router.push(`/validasi-ocr?id=${doc.id}`);
};

const nextPage = () => { currentPage.value++; fetchDokumen(); };
const prevPage = () => { if(currentPage.value > 1) { currentPage.value--; fetchDokumen(); } };

// UI Helpers (UX Flow)
const getStatusProgress = (status) => {
  switch(status) {
    case 'uploaded': return 25;
    case 'processing': return 85;
    case 'success': return 100;
    case 'failed': return 100;
    default: return 0;
  }
};

const getStatusColorClass = (status) => {
  switch(status) {
    case 'uploaded': return 'bg-sky-400';
    case 'processing': return 'bg-purple-500';
    case 'success': return 'bg-emerald-500';
    case 'failed': return 'bg-red-500';
    default: return 'bg-gray-400';
  }
};

const getTextColorClass = (status) => {
  switch(status) {
    case 'processing': return 'text-purple-600';
    case 'success': return 'text-emerald-600';
    case 'failed': return 'text-red-600';
    default: return 'text-gray-600';
  }
};

const formatStatus = (status) => {
  if (status === 'processing') return 'Processing';
  if (status === 'success') return 'Selesai';
  if (status === 'validated') return 'Aktif';
  if (status === 'failed') return 'Tidak Aktif';
  if (status === 'uploaded') return 'Uploaded';
  return status || '-';
};

const getStatusBadgeClass = (status) => {
  switch(status) {
    case 'success': return 'bg-green-100 text-green-700';
    case 'validated': return 'bg-green-100 text-green-700';
    case 'failed': return 'bg-red-100 text-red-600';
    case 'processing': return 'bg-purple-100 text-purple-700';
    case 'uploaded': return 'bg-sky-100 text-sky-700';
    default: return 'bg-gray-100 text-gray-600';
  }
};

const previewDokumen = (doc) => {
  window.open(`/api/alih-media/${doc.id}/file`, '_blank');
};

const handleManualFile = (e) => {
  manualForm.value.file = e.target.files[0] || null;
};

const resetManualForm = () => {
  manualForm.value = { nama_file: '', no_rm: '', nama_pasien: '', jenis_kelamin: '', tanggal_lahir: '', engine: 'manual', alamat: '', file: null };
};

const submitManual = async () => {
  if (!manualForm.value.nama_file) {
    showWarningToast('Nama file wajib diisi.');
    return;
  }
  submittingManual.value = true;
  try {
    const fd = new FormData();
    fd.append('nama_file', manualForm.value.nama_file);
    fd.append('no_rm', manualForm.value.no_rm);
    fd.append('nama_pasien', manualForm.value.nama_pasien);
    fd.append('jenis_kelamin', manualForm.value.jenis_kelamin);
    fd.append('tanggal_lahir', manualForm.value.tanggal_lahir);
    fd.append('engine', manualForm.value.engine);
    fd.append('alamat', manualForm.value.alamat);
    if (manualForm.value.file) {
      fd.append('file', manualForm.value.file);
    }

    const response = await fetch('/api/alih-media/manual', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
      },
      body: fd
    });
    const res = await response.json();
    if (res.success) {
      showSuccessToast('Data manual berhasil disimpan.');
      showManualDialog.value = false;
      resetManualForm();
      fetchDokumen();
    } else {
      showErrorToast(res.message || 'Gagal menyimpan data.');
    }
  } catch (err) {
    console.error('Manual submit error:', err);
    showErrorToast('Terjadi kesalahan saat menyimpan data.');
  } finally {
    submittingManual.value = false;
  }
};

onMounted(() => {
  const authUser = JSON.parse(localStorage.getItem('auth_user') || '{}');
  userRole.value = authUser.role || '';
  fetchDokumen();
});

onUnmounted(() => {
  // No polling to clear
});
</script>

<style scoped>

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.25s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
@keyframes stripes {
  100% { background-position: 1rem 0; }
}
</style>
