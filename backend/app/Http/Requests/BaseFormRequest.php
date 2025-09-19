<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseFormRequest extends FormRequest
{
    /**
     * Override failedValidation to return JSON response.
     * @param  Validator $validator
     * @return void
     */
    protected function failedValidation(Validator $validator): void
    {
        $data = [
            'message' => 'The provided data is invalid.',
            'errors' => $validator->errors(),
        ];

        throw new HttpResponseException(
            response()->json($data, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
