<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name'          => 'required|max:191',
            'category_id'   => 'required|numeric',
            'brand_id'      => 'required|numeric',
            'price'         => 'sometimes|required|numeric|min:1|max:10000000000',
            'quantity'      => 'sometimes|required|numeric|min:0|max:1000000',
            'sold_quantity' => 'sometimes|required|numeric|min:0|max:1000000',
            'active'        => 'required',
            'trending'      => 'required',
        ];

        return $rules;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'statusCode' => 400,
            'data'       => $validator->errors()
        ], 400));
    }
}
