<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Name of product is required.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name must be less than 255 characters.',
            'sku.required' => 'SKU is required.',
            'sku.string' => 'SKU must be a string.',
            'sku.max' => 'SKU must be less than 255 characters.',
            'sku.unique' => 'SKU must be unique.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a number.',
            'price.min' => 'Price must be greater than 0.',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be an integer.',
            'quantity.min' => 'Quantity must be greater than 0.',
            'image.image' => 'File must be an image.',
            'image.mimes' => 'Only jpeg, png, jpg, gif formats are supported.',
            'image.max' => 'Image size must be less than 2MB.',
        ];
    }
}
