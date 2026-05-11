# Analysis: Is Data Kasus a Primary Source for Automated Retensi Calculations?

## Executive Summary
**Answer: NO** ❌ Kasus is NOT functioning as a primary source for automated retensi calculations. It's only a static reference table with retention period definitions that are **never used** in retensi status calculation logic.

---

## 1. RELATIONSHIPS ANALYSIS

### 1.1 Model Relationships - DISCONNECTED ❌

#### Pasien Model
```php
// app/Models/Pasien.php
public function kunjungan() { ... }
public function retensi() { ... }
public function kunjunganTerakhir() { ... }

// ❌ NO relationship to Kasus
```

#### Kasus Model  
```php
// app/Models/Kasus.php
// ❌ NO relationships defined to Pasien or Retensi
// ❌ Can only be used independently
```

#### Retensi Model
```php
// app/Models/Retensi.php
public function pasien() { ... }

// ❌ NO relationship to Kasus
```

**Finding:** There is zero relationship defined between:
- `Pasien` and `Kasus`
- `Retensi` and `Kasus`
- `Kunjungan` and `Kasus`

### 1.2 Data Flow
```
Pasien → Kunjungan (last visit tracked)
  ↓
Retensi (status stored)
  ↓
❌ DEAD END (no Kasus link)

Kasus (retention periods defined)
  ↓
❌ UNUSED
```

---

## 2. RETENSI LOGIC ANALYSIS

### 2.1 When Pasien is Created

#### PasienController::store()
```php
public function store(Request $request)
{
    $validated = $request->validate([
        'no_rm' => 'required|string|unique:pasien,no_rm',
        'nama_pasien' => 'required|string',
        'jenis_kelamin' => 'required|in:L,P',
        'tanggal_lahir' => 'required|date',
        'tempat_lahir' => 'required|string',
        'alamat' => 'nullable|string',
        'no_telepon' => 'nullable|string',
        'status_rm' => 'required|in:Aktif,Inaktif',
        // ❌ NO kasus_id or case type field
    ]);

    $pasien = Pasien::create($validated);

    // Create default retensi record
    Retensi::create([
        'no_rm' => $pasien->no_rm,
        'status_retensi' => 'Aktif',  // ❌ HARDCODED
        'tanggal_mulai_retensi' => Carbon::now(),  // ✅ Today
        // ❌ NO logic using Kasus data
        // ❌ NO tanggal_batas_aktif calculation
        // ❌ NO tanggal_batas_musnah calculation
    ]);
    
    return response()->json([...]);
}
```

**What's Missing:**
- ❌ No Kasus assignment when creating Pasien
- ❌ No reference to `masa_retensi_aktif` from Kasus
- ❌ No reference to `masa_retensi_inaktif` from Kasus
- ❌ No automatic calculation of retention deadline dates

**Current Logic:** Just creates a default Retensi with status='Aktif'

### 2.2 Retensi Status Calculation

#### PasienController::formatPasienData()
```php
private function formatPasienData($pasien)
{
    $kunjunganTerakhir = $pasien->kunjunganTerakhir;
    $tglKunjunganTerakhir = $kunjunganTerakhir?->tanggal_keluar;

    return [
        'no_rm' => $pasien->no_rm,
        'nama_pasien' => $pasien->nama_pasien,
        // ... other fields ...
        'tgl_kunjungan_terakhir' => $tglKunjunganTerakhir?->format('d/m/Y'),
        'status_retensi' => $pasien->retensi?->status_retensi ?? 'Belum di-set',
        // ❌ JUST RETURNING STORED VALUE
        // ❌ NO calculation using:
        //    - Current date
        //    - Last visit date (tanggal_kunjungan_terakhir)
        //    - Kasus retention periods
        'created_at' => $pasien->created_at,
    ];
}
```

**What's Missing:**
- ❌ No comparison: `now() vs (last_visit + masa_retensi_aktif)`
- ❌ No comparison: `now() vs (last_visit + masa_retensi_aktif + masa_retensi_inaktif)`
- ❌ No logic to determine Aktif/Inaktif/Siap Musnah based on dates
- ❌ No reference to Kasus data at all

**Current Logic:** Just echoes the stored status_retensi value from database

---

## 3. DATABASE STRUCTURE ANALYSIS

### 3.1 Pasien Table
```php
// database/migrations/2026_03_17_120636_create_pasien_table.php
Schema::create('pasien', function (Blueprint $table) {
    $table->string('no_rm')->primary();
    $table->string('nama_pasien');
    $table->enum('jenis_kelamin', ['L', 'P']);
    $table->date('tanggal_lahir');
    $table->string('tempat_lahir');
    $table->text('alamat')->nullable();
    $table->string('no_telepon')->nullable();
    $table->enum('status_rm', ['Aktif', 'Inaktif'])->default('Aktif');
    $table->timestamps();
    // ❌ NO kasus_id foreign key
});
```

### 3.2 Kunjungan Table
```php
// database/migrations/2026_03_17_120637_create_kunjungan_table.php
Schema::create('kunjungan', function (Blueprint $table) {
    $table->id();
    $table->string('no_rm');  // ✅ Links to Pasien
    $table->date('tanggal_masuk');
    $table->date('tanggal_keluar')->nullable();  // ✅ Last visit available
    $table->text('diagnosis')->nullable();
    $table->text('keterangan')->nullable();
    $table->timestamps();
    
    $table->foreign('no_rm')->references('no_rm')->on('pasien')->onDelete('cascade');
    // ❌ NO reference to Kasus
});
```

### 3.3 Retensi Table
```php
// database/migrations/2026_03_17_120639_create_retensi_table.php
Schema::create('retensi', function (Blueprint $table) {
    $table->id();
    $table->string('no_rm')->unique();  // ✅ Links to Pasien
    $table->enum('status_retensi', ['Aktif', 'Inaktif', 'Siap Musnah'])->default('Aktif');
    $table->date('tanggal_mulai_retensi')->nullable();  // ✅ Start date available
    $table->date('tanggal_akhir_retensi')->nullable();  // ✅ End date available
    $table->text('keterangan')->nullable();
    $table->timestamps();
    
    $table->foreign('no_rm')->references('no_rm')->on('pasien')->onDelete('cascade');
    // ❌ NO reference to Kasus
});
```

### 3.4 Kasus Master Table
```php
// database/migrations/2026_03_17_120638_create_kasus_master_table.php
Schema::create('kasus_master', function (Blueprint $table) {
    $table->id();
    $table->string('kode_kasus')->unique();
    $table->string('nama_kasus');
    $table->text('deskripsi')->nullable();
    $table->string('kategori');
    $table->integer('masa_retensi_aktif')->default(5);     // ✅ In years
    $table->integer('masa_retensi_inaktif')->default(2);   // ✅ In years
    $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
    $table->timestamps();
    // ❌ NO reference back to Pasien or Retensi
});
```

**Database Linking Analysis:**
```
Pasien --------→ Kunjungan  (FK: no_rm) ✅
  ↓
  └────→ Retensi  (FK: no_rm) ✅

Kasus Master  (standalone table)
  ↓
  ❌ COMPLETELY DISCONNECTED
```

---

## 4. CONTROLLER LOGIC ANALYSIS

### 4.1 PasienController - What's Implemented
```php
- index()     ✅ Lists Pasien with filters
              ❌ Loads retensi but doesn't calculate
              
- show()      ✅ Shows detail
              ❌ No Kasus loading
              
- store()     ✅ Creates Pasien
              ❌ Creates default Retensi without Kasus logic
              
- update()    ✅ Updates Pasien
              ❌ No Kasus assignment
              
- destroy()   ✅ Deletes Pasien
              ✅ Cascades delete to Retensi
```

### 4.2 KasusController - What's Implemented
```php
- index()           ✅ Lists Kasus (can filter by kategori & status)
- show()            ✅ Shows Kasus detail
- store()           ✅ Creates Kasus
- update()          ✅ Updates Kasus
- destroy()         ✅ Deletes Kasus
- getKategori()     ✅ Lists unique categories

// ❌ ZERO integration with:
// - Loading Kasus when creating/editing Pasien
// - Using masa_retensi_aktif/inaktif in calculations
// - Calculating retensi status based on Kasus data
```

**Examples of Missing Implementation:**

❌ **Missing: Load Kasus when creating Pasien**
```php
// SHOULD BE (but isn't):
$kasus = Kasus::find($request->kasus_id);
Retensi::create([
    'no_rm' => $pasien->no_rm,
    'status_retensi' => 'Aktif',
    'tanggal_mulai_retensi' => Carbon::now(),
    'tanggal_batas_aktif' => Carbon::now()->addYears($kasus->masa_retensi_aktif),
    'tanggal_batas_musnah' => Carbon::now()
        ->addYears($kasus->masa_retensi_aktif + $kasus->masa_retensi_inaktif),
]);
```

❌ **Missing: Update retensi status based on date comparison**
```php
// SHOULD BE (but isn't):
$now = Carbon::now();
$retensi = $pasien->retensi;

if ($now->lessThan($retensi->tanggal_batas_aktif)) {
    $status = 'Aktif';
} elseif ($now->lessThan($retensi->tanggal_batas_musnah)) {
    $status = 'Inaktif';
} else {
    $status = 'Siap Musnah';
}
```

---

## 5. API RESPONSE ANALYSIS

### Current API Response Format
```json
GET /api/pasien
{
  "data": [
    {
      "no_rm": "RM00001001",
      "nama_pasien": "Ahmad Satriawan",
      "jenis_kelamin": "L",
      "tanggal_lahir": "05/12/1985",
      "tempat_lahir": "Jember",
      "alamat": "Jl. Jember No. 123",
      "no_telepon": "08124567890",
      "status_rm": "Aktif",
      "tgl_kunjungan_terakhir": "11/03/2026",
      "status_retensi": "Aktif",  ← ❌ STATIC (from database)
      "created_at": "2026-03-17T18:52:35.000000Z"
    }
  ],
  "current_page": 1,
  "total": 5,
  "per_page": 10
}
```

**Status_retensi Calculation:**
- ✅ **Data available for calculation:**
  - `tgl_kunjungan_terakhir` (from Kunjungan::tanggal_keluar)
  - Current date (Carbon::now())
  - Retensi table has: tanggal_mulai_retensi, tanggal_akhir_retensi

- ❌ **Logic missing:**
  - No comparison: `now() > tanggal_batas_aktif` → change to 'Inaktif'
  - No comparison: `now() > tanggal_batas_musnah` → change to 'Siap Musnah'
  - No reference to Kasus::masa_retensi_aktif/inaktif

---

## 6. SUMMARY OF FINDINGS

### What IS Working
| Component | Status | Notes |
|-----------|--------|-------|
| Pasien CRUD | ✅ Working | Can create/read/update/delete patients |
| Kunjungan Tracking | ✅ Working | Can track visit dates |
| Retensi Storage | ✅ Working | Can store retention records |
| Kasus CRUD | ✅ Working | Can define case types & retention periods |
| Kasus Field Data | ✅ Available | masa_retensi_aktif & masa_retensi_inaktif exist |

### What is NOT Connected/Working
| Component | Status | Issue |
|-----------|--------|-------|
| Pasien → Kasus Link | ❌ Missing | No foreign key, no relationship |
| Retensi → Kasus Link | ❌ Missing | No foreign key, no relationship |
| Automated Calc on Creation | ❌ Missing | No Kasus logic in store() |
| Dynamic Status Calculation | ❌ Missing | Status is static, never recalculated |
| Date-Based Logic | ❌ Missing | tanggal_batas fields exist but never calculated |
| Current Date vs Retention | ❌ Missing | No comparison logic implemented |

---

## 7. ARCHITECTURE DIAGRAM

### Current (Broken) Architecture
```
┌─────────────────────────────────────────────────────┐
│                    Frontend (Vue)                   │
│  DataPasien.vue → DataKasus.vue → DataRetensi.vue  │
└──────────────┬──────────────────────────────────────┘
               │
┌──────────────┴──────────────────────────────────────┐
│              Backend (Laravel)                      │
│                                                     │
│  PasienController          KasusController         │
│  ├─ index()               ├─ index()               │
│  ├─ store() ← NO LOGIC    ├─ store()               │
│  ├─ update()             ├─ update()               │
│  └─ destroy()            └─ destroy()              │
│                                                     │
└──────────┬─────────────────────────┬───────────────┘
           │                         │
┌──────────▼──────────────┐  ┌───────▼────────────────┐
│   Database: Pasien      │  │ Database: Kasus       │
│   - no_rm (PK)          │  │ - id (PK)              │
│   - nama_pasien         │  │ - kode_kasus           │
│   - tanggal_lahir       │  │ - masa_retensi_aktif   │
│   ❌ NO kasus_id        │  │ - masa_retensi_inaktif │
│                         │  │ ❌ NO link back        │
│  FK → Kunjungan ✅      │  └────────────────────────┘
│  FK → Retensi ✅        │
│  FK → Kasus ❌          │
└─────────────────────────┘

              ❌ DISCONNECTED
```

### What Should Exist
```
Pasien
  ├─ kasus_id (FK) → Kasus ← masa_retensi_aktif/inaktif
  │
  └─ Retensi
      ├─ tanggal_batas_aktif (calculated)
      ├─ tanggal_batas_musnah (calculated)
      └─ status_retensi (auto-updated based on dates)
```

---

## 8. CONCLUSION

### Direct Answers to Requirements

#### 1. **Relationships** ❌ NOT SATISFACTORY
- ❌ Pasien model has NO relationship to Kasus
- ❌ Retensi model has NO relationship to Kasus
- ❌ There is NO link between patient records and case types
- ❌ Each Pasien operates independently without case context

#### 2. **Retensi Logic** ❌ NOT IMPLEMENTED
- ❌ When Pasien is created, NO Kasus is assigned
- ❌ When calculating retensi status, NO masa_retensi_aktif is used
- ❌ When calculating retensi status, NO masa_retensi_inaktif is used
- ❌ There is NO automatic date calculation based on Kasus periods
- ❌ Status is static (stored in DB), not dynamically calculated
- ❌ **The Kasus retention periods are completely ignored**

#### 3. **Database Structure** ❌ NOT LINKED
- ❌ Pasien table has NO foreign key to kasus_master
- ❌ Retensi table has NO foreign key to kasus_master
- ❌ No database constraint ensures case type assignment

#### 4. **Controller Logic** ❌ NOT INTEGRATED
- ❌ PasienController::store() does NOT assign Kasus
- ❌ PasienController::formatPasienData() does NOT use Kasus data
- ❌ NO logic for: tanggal_batas_aktif calculation
- ❌ NO logic for: tanggal_batas_musnah calculation
- ❌ KasusController is completely isolated

#### 5. **API Response** ❌ NOT AUTO-CALCULATED
- ❌ status_retensi is static from database
- ❌ NOT calculated from current date
- ❌ NOT calculated from last visit date
- ❌ NOT using Kasus retention periods
- **The status is NEVER recalculated; it's just stored**

### Final Verdict

> **Kasus is NOT functioning as a primary source for automated retensi calculations.**

The system currently treats Kasus as a **reference table only**, completely disconnected from Pasien and Retensi management. The retensi status is **manually managed** and **statically stored**, rather than being automatically calculated based on:
- Patient case type (Kasus)
- Last visit date (tanggal_kunjungan_terakhir)
- Retention period rules (masa_retensi_aktif + masa_retensi_inaktif)

---

## 9. REQUIRED CHANGES TO MAKE KASUS PRIMARY SOURCE

To transform Kasus into a true primary source for automated retensi calculations, the following changes are needed:

1. **Database Migration** - Add foreign key
2. **Model Relationships** - Define Pasien ↔ Kasus relationship
3. **PasienController** - Implement automatic retensi calculation
4. **Retensi Observer** - Hook into Pasien/Kunjungan events
5. **Status Calculation Service** - Centralized logic for date-based status

(See separate implementation document for details)
