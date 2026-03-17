<template>
  <div class="p-8">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-2">Dashboard</h1>
      <p class="text-lg text-blue-600">Selamat datang di Sistem Retensi & Alih Media Rekam Medis</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <!-- Dokumen Aktif -->
      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm font-medium mb-2">Dokumen Aktif</p>
            <p class="text-4xl font-bold text-gray-900">105.768</p>
          </div>
          <div class="bg-blue-100 p-4 rounded-lg">
            <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
              <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2H4a1 1 0 110-2V4z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Dokumen Inaktif -->
      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm font-medium mb-2">Dokumen Inaktif</p>
            <p class="text-4xl font-bold text-gray-900">110.743</p>
          </div>
          <div class="bg-green-100 p-4 rounded-lg">
            <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
              <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2H4a1 1 0 110-2V4z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Didigitalisasi -->
      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-400">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm font-medium mb-2">Didigitalisasi</p>
            <p class="text-4xl font-bold text-gray-900">18</p>
          </div>
          <div class="bg-red-100 p-4 rounded-lg">
            <svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
              <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.3A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Aktivitas Terbaru -->
    <div class="bg-white rounded-lg shadow p-6">
      <h2 class="text-xl font-bold text-gray-900 mb-6">Aktivitas Terbaru</h2>
      
      <div class="space-y-4">
        <!-- Activity Item 1 -->
        <div class="flex gap-4 pb-4 border-b border-gray-200 last:border-0">
          <div class="w-3 h-3 rounded-full bg-blue-500 mt-2 flex-shrink-0"></div>
          <div>
            <p class="font-semibold text-gray-900">Dokumen RM20240001 diproses</p>
            <p class="text-sm text-gray-600">2 jam yang lalu</p>
          </div>
        </div>

        <!-- Activity Item 2 -->
        <div class="flex gap-4 pb-4 border-b border-gray-200 last:border-0">
          <div class="w-3 h-3 rounded-full bg-green-500 mt-2 flex-shrink-0"></div>
          <div>
            <p class="font-semibold text-gray-900">Dokumen RM20240002 telah dididigitalisasi</p>
            <p class="text-sm text-gray-600">4 jam yang lalu</p>
          </div>
        </div>

        <!-- Activity Item 3 -->
        <div class="flex gap-4 pb-4 border-b border-gray-200 last:border-0">
          <div class="w-3 h-3 rounded-full bg-yellow-500 mt-2 flex-shrink-0"></div>
          <div>
            <p class="font-semibold text-gray-900">Dokumen RM20240003 siap dimusnahkan</p>
            <p class="text-sm text-gray-600">6 jam yang lalu</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Dashboard component
</script>
