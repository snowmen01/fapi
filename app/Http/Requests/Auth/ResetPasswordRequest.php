<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class ResetPasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'otp' => ['required', 'numeric'],
            'password' => ['required', 'string', 'min:8', 'max:20'],
            'password_confirm' => ['required', 'same:password'],
        ];

        return $rules;
    }

    public function response(array $errors)
    {
        return response()->json([
            'success' => false,
            'errors' => $errors,
        ], 422);
    }
}
