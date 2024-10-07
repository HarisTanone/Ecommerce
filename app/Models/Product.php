<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'originalPrice',
        'discountedPrice',
        'image',
        'category',
        'isFlashSale',
        'isDiscount',
        'inputDate',
        'transactionCount',
        'description',
    ];
}
