<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;


    protected $table = 'don_hang_chi_tiet';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'so_luong_san_pham',
        'gia_san_pham',
        'thoi_gian_tao',
        'thoi_gian_cap_nhat',
        'don_hang_id',
        'san_pham_id'
    ];
}
