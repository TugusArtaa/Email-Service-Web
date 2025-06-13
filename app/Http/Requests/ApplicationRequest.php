<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApplicationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:applications'],
            'description' => ['required', 'string'],
            'pic_name' => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama aplikasi wajib diisi.',
            'name.unique' => 'Nama aplikasi sudah terdaftar.',
            'name.max' => 'Nama aplikasi maksimal 255 karakter.',
            'description.required' => 'Deskripsi aplikasi wajib diisi.',
            'description.string' => 'Deskripsi aplikasi harus berupa teks.',
            'pic_name.required' => 'Nama PIC wajib diisi.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}