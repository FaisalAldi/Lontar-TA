<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Level extends Model
{
    use SoftDeletes;

    protected $table = 'level';

    protected $fillable = [
        'nama',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Relasi: Level punya banyak ambang batas
     */
    public function ambangBatas()
    {
        return $this->hasMany(AmbangBatas::class);
    }

    /**
     * (Opsional) Relasi: Jika kamu simpan level_id di data_sensor juga
     */
    public function dataSensor()
    {
        return $this->hasMany(DataSensor::class);
    }
}
