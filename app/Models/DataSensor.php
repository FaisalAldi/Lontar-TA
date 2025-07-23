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
        'bahaya',
        'created_at',
    ];
}
