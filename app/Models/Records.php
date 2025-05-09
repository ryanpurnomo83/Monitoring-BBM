<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Records extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'records';
    protected $fillable = [
        'nik',
        'level',
        'jarak',
        'timestamp'
    ];
}
