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
        'price' => 'float',
        'quantity' => 'integer',
    ];
}
