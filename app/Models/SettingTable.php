<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingTable extends Model
{
    use HasFactory;

    protected $table = 'setting_table';
    protected $primaryKey = 'id';

    public $timestamps = true;
}
