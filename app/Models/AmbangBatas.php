<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmbangBatas extends Model
{
    protected $table = 'ambang_batas';
    public $timestamps = false;

    protected $fillable = [
        'nama_sensor',
        'level_id',
        'min',
        'max',
    ];

    /**
     * Relasi ke model Level
     */
    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }
}
