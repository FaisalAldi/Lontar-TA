<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DataSensor;
use App\Models\AmbangBatas;
use App\Models\Level; 
use Illuminate\Support\Facades\Log;

class SensorController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validasi data dari request (ESP32)
            $validated = $request->validate([
                'kemiringan' => 'required|numeric',
                'getaran'    => 'required|numeric',
                'kelembapan' => 'required|numeric',
                
            ]);

            // Fungsi mencari level berdasarkan nilai ambang batas
            $getLevel = function ($sensorName, $value) {
                $ambangList = AmbangBatas::where('nama_sensor', $sensorName)
                    ->with('level') // eager load relasi level
                    ->get();

                foreach ($ambangList as $ambang) {
                    if ($value >= $ambang->min && $value <= $ambang->max) {
                        return $ambang->level->nama ?? 'Tidak diketahui';
                    }
                }

                return 'Tidak diketahui';
            };

            // Hitung level masing-masing sensor
            $level_kemiringan  = $getLevel('kemiringan', $validated['kemiringan']);
            $level_kelembapan  = $getLevel('kelembapan', $validated['kelembapan']);

            // Getaran (0 = Normal, 1 = Waspada)
            $level_getaran = match ((int) $validated['getaran']) {
                0 => 'Normal',
                1 => 'Waspada',
                default => 'Tidak diketahui'
            };

            // Urutan level bahaya
            $level_map = [
                'Normal' => 0,
                'Siaga' => 1,
                'Waspada' => 2,
                'Awas' => 3
            ];

            // Konversi ke angka untuk mencari level tertinggi
            $maxLevel = max(
                $level_map[$level_kemiringan] ?? 0,
                $level_map[$level_getaran] ?? 0,
                $level_map[$level_kelembapan] ?? 0
            );

            $bahaya = array_search($maxLevel, $level_map);
            // Ambil level_id dari nama level bahaya tertinggi
$level = Level::where('nama', $bahaya)->first();
$levelId = $level ? $level->id : null;


            // Simpan ke database
            $sensor = DataSensor::create([
            'kemiringan' => $validated['kemiringan'],
            'getaran'    => $validated['getaran'],
            'kelembapan' => $validated['kelembapan'],
            'level_id'   => $levelId, // simpan level_id
        ]);

           return response()->json([
    'message' => 'Data berhasil disimpan',
    'data' => $sensor,
    'tingkat' => [
        'kemiringan' => $level_kemiringan,
        'getaran' => $level_getaran,
        'kelembapan' => $level_kelembapan,
    ]
], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validasi gagal', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan data sensor: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat menyimpan data'], 500);
        }
    }
}
