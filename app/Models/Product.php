<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    /**
     * fillable
     *
     * @var array
     */

    protected $fillable = [
        'nama_produk',
        'harga',
        'description',
        'image'
    ];

    /**
     * image
     *
     * @return Attribute
     */
    protected function image(): Attribute
{
        return Attribute::make(
            get: fn ($image) => url('/storage/products/' . $image),
        );
    }
}
