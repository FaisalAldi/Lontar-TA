<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DataSensor;
use App\Models\AmbangBatas;
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
                $ambangList = AmbangBatas::where('nama_sensor', $sensorName)->get();

                foreach ($ambangList as $ambang) {
                    if ($value >= $ambang->min && $value <= $ambang->max) {
                        return ucfirst(strtolower($ambang->level)); // pastikan huruf besar di awal
                    }
                }

                return 'Tidak diketahui';
            };

            // Hitung level masing-masing sensor
            $level_kemiringan  = $getLevel('kemiringan', $validated['kemiringan']);
            $level_kelembapan  = $getLevel('kelembapan', $validated['kelembapan']);

            // Getaran (khusus 0 = Normal, 1 = Waspada)
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

            // Simpan ke database
            $sensor = DataSensor::create([
                'kemiringan' => $validated['kemiringan'],
                'getaran'    => $validated['getaran'],
                'kelembapan' => $validated['kelembapan'],
                'bahaya'     => $bahaya
            ]);

            return response()->json([
                'message' => 'Data berhasil disimpan',
                'data' => $sensor,
                'tingkat' => [
                    'kemiringan' => $level_kemiringan,
                    'getaran' => $level_getaran,
                    'kelembapan' => $level_kelembapan,
                    'bahaya_keseluruhan' => $bahaya
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
