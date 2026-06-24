<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidasiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'dokumen_id' => 'required|exists:dokumen_rekam_medis,id',
            'no_rm' => 'required|string|max:20',
            'nama_pasien' => 'required|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'tempat_lahir' => 'nullable|string|max:100',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string|max:20',
            'tanggal_masuk' => 'required|date',
            'tanggal_keluar' => 'nullable|date|after_or_equal:tanggal_masuk',
            'diagnosa' => 'required|string',
            'dokter' => 'nullable|string|max:100',
            'kasus_id' => 'nullable|exists:kasus_master,id',
        ];
    }
}
