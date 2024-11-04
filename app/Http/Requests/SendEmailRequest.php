<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SendEmailRequest extends FormRequest
{
    public function rules()
    {
        return [
            'secret' => 'required|string',
            'mail' => 'required|array',
            'mail.*.to' => 'required|email',
            'mail.*.content' => 'required|string',
            'mail.*.subject' => 'required|string',
            'mail.*.priority' => 'required|integer|between:1,20',
            'mail.*.attachment' => 'nullable|array',
            'mail.*.attachment.*' => 'nullable|url'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(validationError($validator->errors()->toArray()), 422));
    }  
}
