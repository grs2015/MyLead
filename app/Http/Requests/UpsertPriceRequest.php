<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class UpsertPriceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'group_description' => ['required', 'string'],
            'priceA' => ['required', 'integer'],
            'priceB' => ['required', 'integer'],
            'priceC' => ['required', 'integer'],
            'product_id' => ['required', 'string', 'exists:products,uuid']
        ];
    }

    public function getProduct(): Product
    {
        return Product::where('uuid', $this->product_id)->firstOrFail();
    }
}
