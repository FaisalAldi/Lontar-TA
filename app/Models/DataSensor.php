<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataSensor extends Model
{
    // Nama tabel jika tidak mengikuti konvensi Laravel
    protected $table = 'data_sensor';

    // Kolom yang boleh diisi
    protected $fillable = [
        'tanggal_dan_waktu',
        'kemiringan',
        'getaran',
        'kelembapan',
        'level_id',
        'created_at',
    ];
    public function level()
{
    return $this->belongsTo(Level::class, 'level_id');
}

}
