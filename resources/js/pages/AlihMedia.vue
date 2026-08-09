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
              :class="{'border-[#0f4392] bg-[#f0f4ff]': currentSelectedFiles.length > 0, 'border-[#0ea5e9] bg-[#eef8ff]': uploading || redirecting}"
            >
              <input 
                type="file" 
                ref="fileInput" 
                @change="handleFileSelect" 
                multiple 
                accept=".pdf,.jpg,.jpeg,.png" 
                :disabled="uploading || processingOcr" 
                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" 
                v-if="!uploading && !redirecting"
              />

              <!-- Lottie Animation: shown during upload/convert -->
              <template v-if="uploading || redirecting">
                <DotLottieVue 
                  src="/blue_working_cat.lottie" 
                  autoplay 
                  loop 
                  style="width: 240px; height: 240px;"
                />
                <h3 class="text-lg font-bold text-[#0f4392] mb-1 mt-2 animate-pulse">
                  {{ redirecting ? 'Mengalihkan ke halaman...' : 'Sedang Upload & Konversi...' }}
                </h3>
                <p class="text-sm text-[#6b7280] text-center">
                  Mohon tunggu, proses sedang berjalan
                </p>
              </template>

              <!-- Normal dropzone content: shown when idle -->
              <template v-else>
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
              </template>
            </div>
          </div>

          <!-- Progress Stepper -->
          <div class="relative flex items-center justify-between w-full mt-10 mb-14 px-3 mx-auto">
            <!-- Line Background and Active Fill -->
            <div class="absolute left-4 right-4 top-3 h-[4px] bg-gray-200 rounded-full">
              <div 
                class="h-full bg-[#0ea5e9] rounded-full transition-all duration-500 ease-out"
                :style="{ width: activeLineWidth }"
              ></div>
            </div>
            
            <!-- Step 1: Upload -->
            <div class="relative z-10 flex flex-col items-center">
              <div 
                class="w-6 h-6 rounded-full flex items-center justify-center border-2 transition-all duration-300"
                :class="getStepCircleClass(1)"
              >
                <!-- Check icon if completed -->
                <svg v-if="currentStep > 1" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <!-- Dot if active -->
                <div v-else-if="currentStep === 1" class="w-2 h-2 bg-[#0ea5e9] rounded-full"></div>
              </div>
              <span 
                class="absolute top-8 whitespace-nowrap text-xs font-bold transition-colors duration-300"
                :class="getStepTextClass(1)"
              >
                Upload
              </span>
            </div>
            
            <!-- Step 2: Convert to Image -->
            <div class="relative z-10 flex flex-col items-center">
              <div 
                class="w-6 h-6 rounded-full flex items-center justify-center border-2 transition-all duration-300"
                :class="getStepCircleClass(2)"
              >
                <!-- Check icon if completed -->
                <svg v-if="currentStep > 2" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <!-- Dot if active -->
                <div v-else-if="currentStep === 2" class="w-2 h-2 bg-[#0ea5e9] rounded-full"></div>
              </div>
              <span 
                class="absolute top-8 whitespace-nowrap text-xs font-bold transition-colors duration-300 text-center leading-tight"
                :class="getStepTextClass(2)"
              >
                Convert to image
              </span>
            </div>
            
            <!-- Step 3: Redirect -->
            <div class="relative z-10 flex flex-col items-center">
              <div 
                class="w-6 h-6 rounded-full flex items-center justify-center border-2 transition-all duration-300"
                :class="getStepCircleClass(3)"
              >
                <!-- Check icon if completed -->
                <svg v-if="currentStep > 3" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <!-- Dot if active -->
                <div v-else-if="currentStep === 3" class="w-2 h-2 bg-[#0ea5e9] rounded-full"></div>
              </div>
              <span 
                class="absolute top-8 whitespace-nowrap text-xs font-bold transition-colors duration-300"
                :class="getStepTextClass(3)"
              >
                Redirect
              </span>
            </div>
          </div>

          <button 
            @click="startFullProcess" 
            :disabled="uploading || redirecting || currentSelectedFiles.length === 0"
            class="w-full py-3 bg-[#113fb6] hover:bg-blue-800 text-white rounded-lg font-bold shadow-md transition-colors flex items-center justify-center gap-2 disabled:bg-gray-400 disabled:cursor-not-allowed"
          >
            <svg v-if="uploading || redirecting" class="animate-spin w-5 h-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
            {{ redirecting ? 'Mengalihkan...' : (uploading ? 'Sedang Upload & Konversi...' : 'Upload & Proses') }}
          </button>


        </div>
      </div>
    </div>

    <!-- SEARCH BAR -->
    <div class="bg-white rounded-xl shadow-sm p-5 mb-6 border border-gray-100">
      <div class="flex flex-col md:flex-row gap-4 items-end">
        <div class="flex-1">
          <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Cari Pasien / File</label>
          <input 
            v-model="searchText" 
            @keyup.enter="handleSearch"
            type="text" 
            placeholder="Cari No. RM atau nama file..." 
            class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs font-semibold focus:ring-2 focus:ring-blue-500 outline-none transition-shadow"
          />
        </div>
        <div class="w-full md:w-64">
          <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tanggal Upload</label>
          <input 
            v-model="filterTanggalUpload" 
            @change="handleSearch"
            type="date" 
            class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs font-semibold focus:ring-2 focus:ring-blue-500 outline-none transition-shadow cursor-pointer"
          />
        </div>
        <div class="flex gap-2">
          <button 
            @click="resetFilters"
            class="px-5 py-2 border border-gray-200 text-gray-500 hover:text-gray-800 hover:bg-gray-50 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap"
          >
            Reset
          </button>
          <button 
            @click="handleSearch"
            class="px-6 py-2 bg-[#2b3c5a] hover:bg-[#1f2e47] text-white rounded-xl text-xs font-bold transition-all cursor-pointer shadow-sm whitespace-nowrap flex items-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            Cari
          </button>
          <button 
            @click="fetchDokumen"
            class="p-2 border border-gray-200 hover:bg-gray-50 text-gray-600 rounded-xl transition-colors shadow-sm"
            title="Refresh Data"
            :disabled="loading"
          >
            <svg class="w-4 h-4" :class="{'animate-spin': loading}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- DATA TABLE -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[800px]">
          <thead class="bg-blue-600 text-white">
            <tr class="text-[10px] font-bold uppercase tracking-widest">
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
              <td :colspan="showEngineColumn ? 8 : 7" class="py-12 text-center text-gray-400">Belum ada data dokumen</td>
            </tr>
            <!-- Data Rows -->
            <tr v-for="(doc, index) in dokumentList" :key="doc.id" class="border-b border-gray-100 hover:bg-gray-50/70 transition-colors">
              <td class="py-3 px-4 text-xs text-gray-600">{{ (currentPage - 1) * perPage + index + 1 }}</td>
              <td class="py-3 px-4 text-xs text-gray-900 font-medium max-w-[200px] truncate" :title="doc.nama_file">{{ doc.nama_file }}</td>
              <td class="py-3 px-4 text-xs text-gray-600">{{ doc.no_rm || '-' }}</td>
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
              <td class="py-3 px-4 text-xs text-gray-600">{{ doc.user_name || '-' }}</td>
              <td class="py-3 px-4 text-xs text-gray-600">{{ doc.tanggal_upload }}</td>
              <td class="py-3 px-4 text-center">
                <span 
                  class="inline-block px-3 py-1 rounded-full text-xs font-bold"
                  :class="getStatusBadgeClass(doc.retensi_status || doc.status)"
                >
                  {{ formatStatus(doc.retensi_status || doc.status) }}
                </span>
              </td>
              <td class="py-3 px-4">
                <div class="flex items-center justify-center gap-1.5">
                  <!-- Preview -->
                  <button
                    @click="previewDokumen(doc)"
                    class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 flex items-center justify-center transition-all duration-200 cursor-pointer"
                    title="Preview"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                  <!-- OCR / Validasi -->
                  <button 
                    v-if="doc.status === 'success' || doc.status === 'validated'"
                    @click="goToValidation(doc)" 
                    class="w-8 h-8 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 flex items-center justify-center transition-all duration-200 cursor-pointer" 
                    title="Validasi"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </button>
                  <button 
                    v-if="!['success', 'validated', 'failed'].includes(doc.status)"
                    disabled 
                    class="w-8 h-8 rounded-lg bg-gray-50 text-gray-300 flex items-center justify-center cursor-not-allowed" 
                    title="Menunggu proses..."
                  >
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                  </button>
                  <!-- Delete -->
                  <button
                    @click="deleteDokumen(doc)"
                    class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition-all duration-200 cursor-pointer"
                    title="Hapus"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Pagination -->
      <div v-if="totalDokumen > 0" class="px-6 py-4 flex items-center justify-between border-t border-gray-100">
        <div class="flex items-center gap-2">
          <span class="text-xs text-gray-500 font-medium">Tampilkan:</span>
          <select 
            v-model="perPage" 
            @change="handlePerPageChange" 
            class="text-xs border border-gray-300 rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white font-semibold text-gray-700 shadow-sm cursor-pointer"
          >
            <option :value="10">10</option>
            <option :value="30">30</option>
            <option :value="50">50</option>
            <option :value="100">100</option>
          </select>
          <span class="text-xs text-gray-500 font-medium">dari {{ totalDokumen }} data</span>
        </div>

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
                <p v-if="checkManualNoRmExists" class="text-xs text-amber-600 font-semibold mt-1.5 flex items-center gap-1">
                  <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                  Peringatan: Dokumen untuk No. RM ini sudah ada di sistem.
                </p>
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
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import { showSuccessToast, showErrorToast, showWarningToast, showConfirmDialog } from '../utils/notification';
import { DotLottieVue } from '@lottiefiles/dotlottie-vue';

const router = useRouter();
const uploading = ref(false);
const uploadProgress = ref([]);
const dokumentList = ref([]);
const loading = ref(true);
const showUploadForm = ref(false);
const uploadedDokumenIds = ref([]);
const processingOcr = ref(false);
const redirecting = ref(false);
const userRole = ref('');
const showEngineColumn = ref(false);

const currentStep = ref(1);

const activeLineWidth = computed(() => {
  if (currentStep.value === 2) return '50%';
  if (currentStep.value >= 3) return '100%';
  return '0%';
});

const getStepCircleClass = (step) => {
  if (currentStep.value > step) {
    return 'bg-[#0ea5e9] border-[#0ea5e9] text-white';
  } else if (currentStep.value === step) {
    return 'bg-white border-[#0ea5e9] text-[#0ea5e9]';
  } else {
    return 'bg-gray-200 border-gray-200 text-gray-400';
  }
};

const getStepTextClass = (step) => {
  if (currentStep.value === step) {
    return 'text-[#0ea5e9] font-extrabold';
  } else if (currentStep.value > step) {
    return 'text-[#0ea5e9]';
  } else {
    return 'text-gray-400';
  }
};

watch(showUploadForm, (newVal) => {
  if (newVal) {
    currentStep.value = 1;
  }
});

const currentPage = ref(1);
const perPage = ref(10);
const totalDokumen = ref(0);
const currentSelectedFiles = ref([]);
const fileInput = ref(null);
const searchText = ref('');
const filterTanggalUpload = ref('');
const showManualDialog = ref(false);
const submittingManual = ref(false);
const checkManualNoRmExists = ref(false);
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

watch(() => manualForm.value.no_rm, async (newNoRm) => {
  if (!newNoRm) {
    checkManualNoRmExists.value = false;
    return;
  }
  try {
    const response = await fetch(`/api/alih-media?no_rm=${newNoRm}`);
    const res = await response.json();
    if (res.success && res.data.length > 0) {
      checkManualNoRmExists.value = true;
    } else {
      checkManualNoRmExists.value = false;
    }
  } catch (err) {
    console.error(err);
  }
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
    if (searchText.value) params.append('search', searchText.value);
    if (filterTanggalUpload.value) params.append('tanggal_upload', filterTanggalUpload.value);
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
  fetchDokumen();
};

const resetFilters = () => {
  searchText.value = '';
  filterTanggalUpload.value = '';
  currentPage.value = 1;
  fetchDokumen();
};

const goToPage = (page) => {
  currentPage.value = page;
  fetchDokumen();
};

const handlePerPageChange = () => {
  currentPage.value = 1;
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

  // Step 1: Upload
  currentStep.value = 1;
  uploading.value = true;
  const result = await executeUploadApi(currentSelectedFiles.value);
  uploading.value = false;

  if (result && result.redirect_url) {
    // Step 2: Convert to Image
    currentStep.value = 2;
    await new Promise(resolve => setTimeout(resolve, 1000));

    // Step 3: Redirect
    currentStep.value = 3;
    redirecting.value = true;
    showSuccessToast('Upload & Konversi Berhasil! Mengalihkan ke Validasi...');
    await new Promise(resolve => setTimeout(resolve, 800));

    router.push(result.redirect_url);
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
    case 'validated':
    case 'Aktif': 
      return 'bg-green-100 text-green-700';
    case 'Inaktif': 
      return 'bg-yellow-100 text-yellow-800';
    case 'Siap Dimusnahkan':
    case 'Dimusnahkan': 
      return 'bg-red-100 text-red-800';
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
  checkManualNoRmExists.value = false;
};

const submitManual = async () => {
  if (!manualForm.value.nama_file) {
    showWarningToast('Nama file wajib diisi.');
    return;
  }
  if (checkManualNoRmExists.value) {
    const confirm = await showConfirmDialog(
      'Dokumen Sudah Ada',
      'Peringatan: Dokumen untuk No. RM ini sudah ada di sistem. Apakah Anda yakin ingin menyimpan dan menimpa/menambah dokumen ini?',
      'Ya, Simpan',
      'Batal'
    );
    if (!confirm.isConfirmed) {
      return;
    }
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
