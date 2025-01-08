<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RetryEmailRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id' => 'required|integer|exists:email_logs,id',
            'mail' => 'required|array',
            'mail.*.to' => 'required|email',
            'mail.*.content' => 'nullable|string',
            'mail.*.subject' => 'nullable|string',
            'mail.*.priority' => 'required|string|in:low,medium,high',
            'mail.*.attachment' => 'nullable|array',
            'mail.*.attachment.*' => 'nullable|url'
        ];
    }

    protected function failedValidation(Validator $validator)
    { 
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}