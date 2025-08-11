<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * Attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'image',
        'sku',
        'price',
        'quantity',
    ];

    /**
     * Attributes that should be cast to specific types.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
    ];

    /**
     * Validation rules for the model.
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'sku' => 'required|string|max:100|unique:products,sku',
        'price' => 'required|numeric|min:0',
        'quantity' => 'required|integer|min:0',
    ];

    /**
     * Validation rules for updating (excluding uniqueness of SKU for the current record).
     *
     * @param int $id
     * @return array
     */
    public static function getUpdateRules($id)
    {
        $rules = self::$rules;
        $rules['sku'] = 'required|string|max:100|unique:products,sku,' . $id;
        return $rules;
    }
}
