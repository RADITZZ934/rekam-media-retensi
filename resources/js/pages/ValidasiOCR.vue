<template>
  <div class="min-h-screen bg-[#f3f4f6]">
    <!-- Top Bar -->
    <div class="bg-[#1e293b] text-white px-6 py-3 flex justify-between items-center shadow-lg sticky top-0 z-50">
      <div class="flex items-center gap-4">
        <button @click="router.push('/alih-media')" class="hover:bg-white/10 p-2 rounded-lg transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </button>
        <h1 class="text-xl font-bold tracking-tight">Validasi AI OCR & Metadata</h1>
      </div>
      <div class="flex items-center gap-3" v-if="selectedDokumen">
        <span class="text-sm bg-blue-500/20 text-blue-300 px-3 py-1 rounded-full border border-blue-500/30 font-medium">
          {{ selectedDokumen.nama_file }}
        </span>
        <button 
          @click="saveMetadata" 
          :disabled="saving"
          class="bg-[#00c853] hover:bg-[#00b24a] text-white px-6 py-1.5 rounded-lg font-bold transition-all shadow-md flex items-center gap-2 disabled:bg-gray-500"
        >
          <svg v-if="saving" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
          <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
          SIMPAN DATA
        </button>
      </div>
    </div>

    <!-- Main Split Layout -->
    <div class="flex h-[calc(100-48px)] overflow-hidden">
      
      <!-- LEFT: PREVIEW DOKUMEN -->
      <div class="w-1/2 h-[calc(100vh-64px)] border-r-2 border-gray-200 bg-gray-200 flex flex-col relative">
        <div class="flex-1 overflow-hidden">
          <template v-if="selectedDokumen">
            <!-- PDF Native Viewer if PDF -->
            <iframe
              v-if="selectedDokumen.nama_file.toLowerCase().endsWith('.pdf')"
              :key="'pdf-' + selectedDokumen.id"
              :src="`/api/alih-media/${selectedDokumen.id}/file`"
              class="w-full h-full border-none"
            ></iframe>
            
            <!-- Image Viewer for converted/images -->
            <div v-else class="w-full h-full overflow-y-auto custom-scrollbar flex flex-col items-center bg-[#2d2d2d] p-4">
              <img 
                :key="'img-' + selectedDokumen.id"
                :src="`/api/alih-media/${selectedDokumen.id}/file?image=1`" 
                class="max-w-full h-auto shadow-2xl rounded-sm" 
                alt="Preview Dokumen"
              />
            </div>
          </template>
          <div v-else class="h-full flex items-center justify-center text-gray-500 italic">
            Memuat dokumen...
          </div>
        </div>
      </div>

      <!-- RIGHT: CONTROLS & FORM -->
      <div class="w-1/2 h-[calc(100vh-64px)] overflow-y-auto p-6 bg-white custom-scrollbar">
        
        <!-- Action Header -->
        <div class="mb-8 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 rounded-xl flex items-center justify-between shadow-sm">
          <div>
            <h3 class="text-[#1e293b] font-bold text-lg mb-1">AI Extraction Hub</h3>
            <p class="text-sm text-blue-600 font-medium">Klik tombol untuk mengekstrak data dari gambar secara otomatis.</p>
          </div>
          <button 
            @click="handleStartOcr" 
            :disabled="processingOcr"
            class="bg-[#1e293b] hover:bg-[#0f172a] text-white px-8 py-3 rounded-xl font-bold transition-all shadow-lg flex items-center gap-3 disabled:bg-gray-400 group"
          >
            <svg v-if="processingOcr" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            <svg v-else class="w-5 h-5 text-yellow-400 group-hover:rotate-12 transition-transform" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            {{ processingOcr ? 'Sedang Memproses...' : 'PROSES AI OCR' }}
          </button>
        </div>

        <!-- OCR Result Raw JSON -->
        <div class="mb-8">
          <div class="flex items-center justify-between mb-2">
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">OCR Result (Raw JSON)</h3>
            <span class="text-[10px] text-gray-400 font-mono">GEMINI-FLASH-VISION</span>
          </div>
          <div class="relative">
             <textarea 
              v-model="rawOcrText"
              rows="6"
              class="w-full bg-[#1e1e1e] text-[#a9dc76] p-4 rounded-xl font-mono text-xs border-2 border-gray-800 shadow-inner resize-none focus:ring-0 outline-none"
              placeholder="{ 'hasil': 'klik tombol proses ocr' }"
            ></textarea>
            <div class="absolute top-2 right-2 flex gap-2">
              <div class="w-2.5 h-2.5 rounded-full bg-[#ff5f56]"></div>
              <div class="w-2.5 h-2.5 rounded-full bg-[#ffbd2e]"></div>
              <div class="w-2.5 h-2.5 rounded-full bg-[#27c93f]"></div>
            </div>
          </div>
        </div>

        <!-- METADATA FORM -->
        <div class="space-y-8 pb-20">
          
          <!-- Section: Fasilitas Kesehatan -->
          <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex items-center gap-2">
              <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
              <h4 class="font-bold text-gray-700">Fasilitas Kesehatan</h4>
            </div>
            <div class="p-4 grid grid-cols-2 gap-4">
              <div class="col-span-2">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Rumah Sakit</label>
                <input v-model="form.nama_rs" type="text" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:border-blue-500 outline-none" />
              </div>
              <div class="col-span-2">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Alamat RS</label>
                <input v-model="form.alamat_rs" type="text" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:border-blue-500 outline-none" />
              </div>
            </div>
          </div>

          <!-- Section: Identitas Pasien -->
          <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex items-center gap-2">
              <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
              <h4 class="font-bold text-gray-700">Identitas Pasien</h4>
            </div>
            <div class="p-4 grid grid-cols-2 gap-4">
              <div class="col-span-1">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nomor RM</label>
                <input v-model="form.nomor_rm" type="text" class="w-full px-3 py-2 bg-[#fffbeb] border border-amber-200 rounded-lg text-sm font-bold text-amber-900 focus:border-amber-500 outline-none" />
              </div>
              <div class="col-span-1">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Pasien</label>
                <input v-model="form.nama_pasien" type="text" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:border-blue-500 outline-none" />
              </div>
              <div class="col-span-1">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal Lahir</label>
                <input v-model="form.tanggal_lahir" type="date" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:border-blue-500 outline-none" />
              </div>
              <div class="col-span-1">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jenis Kelamin</label>
                <select v-model="form.jenis_kelamin" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:border-blue-500 outline-none">
                  <option value="Laki-laki">Laki-laki</option>
                  <option value="Perempuan">Perempuan</option>
                </select>
              </div>
              <div class="col-span-2">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Alamat Pasien</label>
                <textarea v-model="form.alamat_pasien" rows="2" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:border-blue-500 outline-none resize-none"></textarea>
              </div>
              <div class="col-span-2">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kasus Medis (Manual)</label>
                <select v-model="form.kasus_id" class="w-full px-3 py-2 bg-[#fffbeb] border border-amber-200 rounded-lg text-sm font-semibold text-amber-900 focus:border-amber-500 outline-none">
                  <option value="">-- Pilih Kasus Medis --</option>
                  <option v-for="kasus in kasusList" :key="kasus.id" :value="kasus.id">
                    {{ kasus.nama_kasus }} (Masa Aktif: {{ kasus.masa_retensi_aktif }} th, Masa Inaktif: {{ kasus.masa_retensi_inaktif }} th)
                  </option>
                </select>
              </div>
            </div>
          </div>

          <!-- Section: Data Kunjungan -->
          <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex items-center gap-2">
              <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
              <h4 class="font-bold text-gray-700">Data Kunjungan</h4>
            </div>
            <div class="p-4 grid grid-cols-2 gap-4">
              <div class="col-span-1">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tgl Masuk</label>
                <input v-model="form.tanggal_masuk" type="date" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:border-blue-500 outline-none" />
              </div>
              <div class="col-span-1">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tgl Keluar</label>
                <input v-model="form.tanggal_keluar" type="date" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:border-blue-500 outline-none" />
              </div>
              <div class="col-span-1">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Lama Dirawat</label>
                <input v-model="form.lama_dirawat" type="text" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:border-blue-500 outline-none" placeholder="Contoh: 2 hari" />
              </div>
              <div class="col-span-1">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Alasan MRS</label>
                <input v-model="form.alasan_mrs" type="text" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:border-blue-500 outline-none" />
              </div>


              <div class="col-span-2">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Diagnosis Utama</label>
                <input v-model="form.diagnosis" type="text" class="w-full px-3 py-2 bg-blue-50 border border-blue-200 rounded-lg text-sm font-semibold text-blue-900 focus:border-blue-500 outline-none" />
              </div>
            </div>
          </div>

          <!-- Section: Tenaga Medis -->
          <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex items-center gap-2">
              <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
              <h4 class="font-bold text-gray-700">Tenaga Medis</h4>
            </div>
            <div class="p-4 grid grid-cols-2 gap-4">
              <div class="col-span-2">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Dokter DPJP</label>
                <input v-model="form.dokter_dpjp" type="text" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:border-blue-500 outline-none" />
              </div>
            </div>
          </div>

        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { showSuccessToast, showErrorToast } from '../utils/notification';

const route = useRoute();
const router = useRouter();

const selectedDokumen = ref(null);
const rawOcrText = ref('');
const processingOcr = ref(false);
const saving = ref(false);
const kasusList = ref([]);

const form = ref({
  nama_rs: '',
  alamat_rs: '',
  nomor_rm: '',
  nama_pasien: '',
  tanggal_lahir: '',
  jenis_kelamin: '',
  alamat_pasien: '',
  tanggal_masuk: '',
  tanggal_keluar: '',
  lama_dirawat: '',
  alasan_mrs: '',
  diagnosis: '',
  dokter_dpjp: '',
  keterangan: '',
  kasus_id: ''
});

const fetchKasusList = async () => {
  try {
    const response = await fetch('/api/kasus?per_page=100');
    const data = await response.json();
    kasusList.value = data.data || [];
  } catch (error) {
    console.error('Gagal mengambil daftar kasus:', error);
  }
};

const fetchDokumenDetail = async (id) => {
  try {
    const response = await fetch(`/api/alih-media/${id}`);
    const res = await response.json();
    if (res.success) {
      selectedDokumen.value = res.data;
      // Load existing metadata if any
      if (res.data.ocr_result && res.data.ocr_result.parsed_data) {
        let parsed = res.data.ocr_result.parsed_data;
        if (typeof parsed === 'string') {
          try {
            parsed = JSON.parse(parsed);
          } catch (e) {
            console.error('Failed to parse parsed_data', e);
          }
        }
        mapJsonToForm(parsed);
        rawOcrText.value = typeof parsed === 'object' ? JSON.stringify(parsed, null, 2) : parsed;
      }
    }
  } catch (err) {
    showErrorToast('Gagal memuat detail dokumen.');
  }
};

const handleStartOcr = async () => {
  if (!selectedDokumen.value) return;
  
  processingOcr.value = true;
  try {
    const response = await fetch(`/api/alih-media/${selectedDokumen.value.id}/start-ocr`, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]')?.getAttribute('content'),
      }
    });
    
    const res = await response.json();
    if (res.success) {
      showSuccessToast('AI Ekstraksi Berhasil!');
      rawOcrText.value = res.raw_json;
      mapJsonToForm(res.data);
    } else {
      showErrorToast(res.message || 'Gagal memproses AI.');
    }
  } catch (err) {
    showErrorToast('Terjadi kesalahan jaringan.');
  } finally {
    processingOcr.value = false;
  }
};

const mapJsonToForm = (data) => {
  if (!data) return;
  
  // Check if data is flat structure (previously validated/saved draft)
  if (data.nomor_rm !== undefined || data.nama_pasien !== undefined) {
    form.value.nama_rs = data.nama_rs || '';
    form.value.alamat_rs = data.alamat_rs || '';
    form.value.nomor_rm = data.nomor_rm || '';
    form.value.nama_pasien = data.nama_pasien || '';
    form.value.tanggal_lahir = formatToInputDate(data.tanggal_lahir);
    
    const jk = (data.jenis_kelamin || '').toLowerCase();
    if (jk.includes('l') || jk.includes('pria') || jk.includes('laki')) {
      form.value.jenis_kelamin = 'Laki-laki';
    } else if (jk.includes('p') || jk.includes('wanita') || jk.includes('perempuan')) {
      form.value.jenis_kelamin = 'Perempuan';
    } else {
      form.value.jenis_kelamin = '';
    }

    form.value.alamat_pasien = data.alamat_pasien || '';
    form.value.wali_nama = data.wali_nama || '';
    form.value.wali_hubungan = data.wali_hubungan || '';
    form.value.tanggal_masuk = formatToInputDate(data.tanggal_masuk);
    form.value.tanggal_keluar = formatToInputDate(data.tanggal_keluar);
    form.value.lama_dirawat = data.lama_dirawat || '';
    form.value.alasan_mrs = data.alasan_mrs || '';


    form.value.diagnosis = data.diagnosis || '';
    form.value.dokter_dpjp = data.dokter_dpjp || '';
    form.value.keterangan = data.keterangan || '';
    form.value.kasus_id = data.kasus_id || '';
    return;
  }
  
  const f = data.fasilitas_kesehatan || {};
  const p = data.identitas_pasien || {};
  const k = data.data_kunjungan || {};
  const d = data.diagnosa_dan_tindakan || {};
  const t = data.tenaga_medis || {};
  
  form.value.nama_rs = f.nama_rumah_sakit || '';
  form.value.alamat_rs = f.alamat_rs || '';
  
  form.value.nomor_rm = p.nomor_rm || '';
  form.value.nama_pasien = p.nama_pasien || '';
  form.value.tanggal_lahir = formatToInputDate(p.tanggal_lahir);
  
  // Deteksi Jenis Kelamin lebih luas
  const jk = (p.jenis_kelamin || '').toLowerCase();
  if (jk.includes('l') || jk.includes('pria') || jk.includes('laki')) {
    form.value.jenis_kelamin = 'Laki-laki';
  } else if (jk.includes('p') || jk.includes('wanita') || jk.includes('perempuan')) {
    form.value.jenis_kelamin = 'Perempuan';
  } else {
    form.value.jenis_kelamin = '';
  }

  // Fallback Alamat: Jika alamat pasien kosong, gunakan alamat Wali
  const w = data.informasi_keluarga?.wali_hukum_penanggung_jawab || {};
  form.value.alamat_pasien = p.alamat_pasien || w.alamat || '';
  
  // Simpan data wali juga jika perlu (kita tambahkan ke form nanti)
  form.value.wali_nama = w.nama || '';
  form.value.wali_hubungan = w.hubungan || '';
  
  form.value.tanggal_masuk = formatToInputDate(k.tgl_masuk);
  form.value.tanggal_keluar = formatToInputDate(k.tgl_keluar);
  form.value.lama_dirawat = k.lama_dirawat || '';
  form.value.alasan_mrs = k.alasan_mrs || '';

  // Auto-match kasus_id based on diagnosis from AI
  if (k.diagnosis_utama) {
    const diag = k.diagnosis_utama.toLowerCase();
    const matched = kasusList.value.find(c => diag.includes(c.nama_kasus.toLowerCase()) || c.nama_kasus.toLowerCase().includes(diag));
    if (matched) {
      form.value.kasus_id = matched.id;
    }
  }

  form.value.diagnosis = k.diagnosis_utama || '';
  form.value.dokter_dpjp = t.dokter_dpjp || '';
};

const parseIndonesianDateToYmd = (dateStr) => {
  if (!dateStr) return '';
  
  let str = dateStr.trim().toLowerCase();
  
  const months = {
    'januari': '01', 'jan': '01',
    'februari': '02', 'feb': '02',
    'maret': '03', 'mar': '03',
    'april': '04', 'apr': '04',
    'mei': '05',
    'juni': '06', 'jun': '06',
    'juli': '07', 'jul': '07',
    'agustus': '08', 'agt': '08', 'agst': '08', 'aug': '08',
    'september': '09', 'sep': '09', 'sept': '09',
    'oktober': '10', 'okt': '10', 'oct': '10',
    'november': '11', 'nov': '11',
    'desember': '12', 'des': '12', 'dec': '12'
  };
  
  const normalized = str.replace(/[^a-z0-9]/g, ' ');
  const parts = normalized.split(/\s+/).filter(Boolean);
  
  if (parts.length === 3) {
    let day = '', month = '', year = '';
    
    if (months[parts[1]]) {
      day = parts[0];
      month = months[parts[1]];
      year = parts[2];
    } else if (months[parts[0]]) {
      month = months[parts[0]];
      day = parts[1];
      year = parts[2];
    }
    
    if (day && month && year && year.length === 4) {
      return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
    }
  }
  
  return '';
};

const formatToInputDate = (dateStr) => {
  if (!dateStr || typeof dateStr !== 'string') return '';
  
  let str = dateStr.trim();
  
  // Try parsing Indonesian month first (e.g. "17 Februari 2023")
  const parsedIdDate = parseIndonesianDateToYmd(str);
  if (parsedIdDate) {
    return parsedIdDate;
  }
  
  // Otherwise, handle timestamp by splitting space
  const cleanDate = str.split(' ')[0];
  
  // Handle DD/MM/YYYY or DD-MM-YYYY
  const separator = cleanDate.includes('/') ? '/' : (cleanDate.includes('-') ? '-' : null);
  
  if (separator) {
    const parts = cleanDate.split(separator);
    if (parts.length === 3) {
      // Jika bagian pertama adalah tahun (YYYY-MM-DD)
      if (parts[0].length === 4) {
        return `${parts[0]}-${parts[1].padStart(2, '0')}-${parts[2].padStart(2, '0')}`;
      }
      // Jika bagian terakhir adalah tahun (DD-MM-YYYY)
      return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
    }
  }
  
  return dateStr; 
};

const saveMetadata = async () => {
  saving.value = true;
  try {
    const response = await fetch(`/api/alih-media/${selectedDokumen.value.id}/submit-validasi`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
      },
      body: JSON.stringify({ metadata: form.value })
    });
    const res = await response.json();
    if (res.success) {
      showSuccessToast('Data berhasil divalidasi dan disimpan.');
      router.push('/alih-media');
    } else {
      showErrorToast(res.message || 'Gagal menyimpan data.');
    }
  } catch (err) {
    showErrorToast('Gagal menyimpan data.');
  } finally {
    saving.value = false;
  }
};

onMounted(async () => {
  await fetchKasusList();
  const id = route.query.id;
  if (id) fetchDokumenDetail(id);
});

// Sync manual edit in raw JSON back to form (optional helper)
watch(rawOcrText, (newVal) => {
  try {
    const parsed = JSON.parse(newVal);
    mapJsonToForm(parsed);
  } catch (e) {
    // ignore invalid json during typing
  }
});

// Auto-calculate lama dirawat based on tanggal_masuk and tanggal_keluar
watch(
  () => [form.value.tanggal_masuk, form.value.tanggal_keluar],
  ([newMasuk, newKeluar]) => {
    if (newMasuk && newKeluar) {
      const masuk = new Date(newMasuk);
      const keluar = new Date(newKeluar);
      
      if (!isNaN(masuk.getTime()) && !isNaN(keluar.getTime())) {
        const diffTime = keluar.getTime() - masuk.getTime();
        const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24));
        if (diffDays >= 0) {
          form.value.lama_dirawat = `${diffDays} hari`;
        }
      }
    }
  }
);

</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f1f1;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>
