<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;


    protected $table = 'san_pham';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'hinh_anh_url',
        'ten_san_pham',
        'gia_san_pham',
        'loai_san_pham',
    ];
}
