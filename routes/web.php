<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('panelmonitoring');
});

Route::get('/DataSensor', function () {
    return view('datasensor');
});

Route::get('/PanelGrafik', function () {
    return view('panelgrafik');
});

Route::get('/api/last10-sensor', [DashboardController::class, 'get10DataSensor']);

Route::get('/api/latest-sensor', [DashboardController::class, 'getLatestSensor'])->name('latest.sensor');

Route::get('/trend-terkini', [DashboardController::class, 'getTrendTerkini']);