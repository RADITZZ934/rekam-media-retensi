<?php

$dir = __DIR__ . '/app/Models/';

$models = [
    'User' => '
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {
    protected $guarded = [];
}',
    'Pasien' => '
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Pasien extends Model {
    protected $table = "pasien";
    protected $primaryKey = "no_rm";
    public $incrementing = false;
    protected $keyType = "string";
    protected $guarded = [];

    public function kunjungan() { return $this->hasMany(Kunjungan::class, "no_rm", "no_rm"); }
    public function retensi() { return $this->hasOne(Retensi::class, "no_rm", "no_rm"); }
    public function dokumen() { return $this->hasMany(DokumenRekamMedis::class, "no_rm", "no_rm"); }
    public function pemusnahan() { return $this->hasMany(Pemusnahan::class, "no_rm", "no_rm"); }
}',
    'Kunjungan' => '
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Model {
    protected $table = "kunjungan";
    protected $primaryKey = "id_kunjungan";
    protected $guarded = [];

    public function pasien() { return $this->belongsTo(Pasien::class, "no_rm", "no_rm"); }
}',
    'DokumenRekamMedis' => '
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DokumenRekamMedis extends Model {
    protected $table = "dokumen_rekam_medis";
    protected $guarded = [];

    public function pasien() { return $this->belongsTo(Pasien::class, "no_rm", "no_rm"); }
    public function ocrResult() { return $this->hasOne(OCRResult::class, "dokumen_id", "id"); }
}',
    'OCRResult' => '
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OCRResult extends Model {
    protected $table = "ocr_result";
    protected $guarded = [];

    public function dokumen() { return $this->belongsTo(DokumenRekamMedis::class, "dokumen_id", "id"); }
}',
    'ValidasiData' => '
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ValidasiData extends Model {
    protected $table = "validasi_data";
    protected $guarded = [];

    public function dokumen() { return $this->belongsTo(DokumenRekamMedis::class, "dokumen_id", "id"); }
    public function user() { return $this->belongsTo(User::class, "verified_by", "id"); }
}',
    'KasusMaster' => '
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class KasusMaster extends Model {
    protected $table = "kasus_master";
    protected $guarded = [];
}',
    'Retensi' => '
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Retensi extends Model {
    protected $table = "retensi";
    protected $guarded = [];

    public function pasien() { return $this->belongsTo(Pasien::class, "no_rm", "no_rm"); }
    public function kasusMaster() { return $this->belongsTo(KasusMaster::class, "jenis_kasus_id", "id"); }
}',
    'Pemusnahan' => '
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Pemusnahan extends Model {
    protected $table = "daftar_pemusnahan";
    protected $guarded = [];

    public function pasien() { return $this->belongsTo(Pasien::class, "no_rm", "no_rm"); }
}',
    'BeritaAcara' => '
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BeritaAcara extends Model {
    protected $table = "berita_acara_pemusnahan";
    protected $guarded = [];

    public function pemusnahan() { return $this->belongsTo(Pemusnahan::class, "id_pemusnahan", "id"); }
}',
    'ActivityLog' => '
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model {
    protected $table = "activity_logs";
    protected $guarded = [];

    public function user() { return $this->belongsTo(User::class); }
}'
];

foreach ($models as $name => $content) {
    file_put_contents($dir . $name . '.php', "<?php\n" . $content);
}

echo "Created Models Successfully!";
