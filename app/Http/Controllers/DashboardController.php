<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataSensor;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getLatestSensor()
    {
        $latestSensor = DataSensor::latest()->first();

        return response()->json([
            'kemiringan' => $latestSensor->kemiringan,
            'getaran' => $latestSensor->getaran,
            'kelembapan' => $latestSensor->kelembapan,
            'bahaya' => $latestSensor->bahaya,
            'created_at' => $latestSensor->created_at->toDateTimeString(),
        ]);
    }

    public function get10DataSensor()
    {
        $data = \App\Models\DataSensor::orderBy('created_at','desc')->take(10)->get()->reverse();

        $result = [
            'labels' => [],
            'kemiringan' => [],
            'getaran' => [],
            'kelembapan' => [],
        ];

        foreach ($data as $item) {
            $result['labels'][] = \Carbon\Carbon::parse($item->created_at)->format('H:i:s');
            $result['kemiringan'][] = $item->kemiringan;
            $result['getaran'][] = $item->getaran;
            $result['kelembapan'][] = $item->kelembapan;
        }

        // Tambahkan waktu data terakhir
        $result['latest_created_at'] = $data->last()->created_at->toIso8601String();

        return response()->json($result);
    }

    public function getTrendTerkini()
    {
        $trend = DataSensor::orderBy('created_at', 'desc')->take(20)->get();
        $trendTerkini = [];

        // Ambil level bahaya terbaru
        $latestData = DataSensor::latest()->first();
        $bahaya = $latestData->bahaya ?? 'Normal';
        $tanggalTerbaru = Carbon::parse($latestData->created_at)->format('d/m/Y');

        // Tambahkan status bahaya terbaru langsung
        $trendTerkini[] = "Pada tgl $tanggalTerbaru terdeteksi status bahaya: {$bahaya}.";

        // Status pengecekan tiap jenis trend
        $sudahKemiringan = false;
        $sudahGetaran = false;
        $sudahKelembapan = false;
        

        foreach ($trend as $data) {
            $tanggal = Carbon::parse($data->created_at)->format('d/m/Y');

            // Kemiringan
            if (!$sudahKemiringan && $data->kemiringan >= 15) {
                $trendTerkini[] = "Pada tgl $tanggal kemiringan mencapai {$data->kemiringan}°.";
                $sudahKemiringan = true;
            }

            // Getaran
            if (!$sudahGetaran && $data->getaran == 1) {
                $trendTerkini[] = "Pada tgl $tanggal terdeteksi getaran abnormal.";
                $sudahGetaran = true;
            }

            // Kelembapan
            if (!$sudahKelembapan && $data->kelembapan >= 20) {
                $trendTerkini[] = "Pada tgl $tanggal kelembapan mencapai {$data->kelembapan}%.";
                $sudahKelembapan = true;
            }
        }

        return response()->json([
        'trendTerkini' => $trendTerkini,
        'bahaya' => $bahaya,
    ]);
    }
}
