<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\DataSensor;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class DataSensorController extends Controller
{
    public function index(Request $request)
    {
        $data = DataSensor::select('created_at', 'kemiringan', 'getaran', 'kelembapan', 'bahaya')
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);
        return response()->json($data);
    }

    public function filter(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');

        $data = DataSensor::whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])->get();

        return response()->json($data);
    }

    public function download(Request $request)
    {
        $startInput = $request->query('start');
        $endInput = $request->query('end');

        $query = DataSensor::select('created_at', 'kemiringan', 'getaran', 'kelembapan', 'bahaya');

        if ($startInput && $endInput) {
            try {
                $start = Carbon::createFromFormat('d/m/Y', $startInput)->startOfDay();
                $end = Carbon::createFromFormat('d/m/Y', $endInput)->endOfDay();
                $query->whereBetween('created_at', [$start, $end]);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Format tanggal salah. Gunakan format dd/mm/yyyy'], 400);
            }
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        if ($data->isEmpty()) {
            return response()->json(['error' => 'Tidak ada data untuk rentang tanggal yang dipilih.'], 404);
        }

        $csvContent = "Tanggal & Waktu,Kemiringan,Getaran,Kelembapan,Bahaya\n";
        foreach ($data as $row) {
            $formattedDate = Carbon::parse($row->created_at)->format('d/m/Y H:i:s');
            $csvContent .= "{$formattedDate},{$row->kemiringan},{$row->getaran},{$row->kelembapan}," . ($row->bahaya ?? 'Tidak Terdeteksi') . "\n";
        }

        return response($csvContent, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="data_sensor.csv"');
    }
}
