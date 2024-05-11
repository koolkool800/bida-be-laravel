<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;


    protected $table = 'orders';
    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'start_time',
        'end_time',
        'current_price',
        'total_price',
        'created_at',
        'updated_at',
        "table_id",
        "user_id",
        'tong_gia_san_pham'
    ];
}
