<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadDokumenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // Max 10 MB per file
        ];
    }

    public function messages()
    {
        return [
            'file.*.required' => 'File dokumen wajib diunggah',
            'file.*.mimes' => 'Format file yang diterima: PDF, JPG, PNG',
            'file.*.max' => 'Ukuran file maksimal adalah 10 MB',
        ];
    }
}
