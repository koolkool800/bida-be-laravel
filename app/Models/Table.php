<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $table = 'tables';
    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'is_available',
        'setting_table_id',
        'created_at',
        'updated_at',
    ];
}
