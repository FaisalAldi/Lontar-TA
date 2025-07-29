@extends('layout')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/panelgrafik.css') }}">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="content-container">
    <div class="judul">
        <h2>Grafik Sensor</h2>
    </div>

    <div class="grafik-container">
        <div class="grafik-header" style="text-align: center; margin-bottom: 20px;">
            <h3>Pilih Grafik Sensor</h3>
        </div>

        <div class="grafik-buttons">
            <button class="grafik-button" onclick="showGrafik('kemiringan')">Sensor Kemiringan</button>
            <button class="grafik-button" onclick="showGrafik('getaran')">Sensor Getaran</button>
            <button class="grafik-button" onclick="showGrafik('kelembapan')">Sensor Kelembapan</button>
        </div>

        <div class="grafik-content">
            <div id="grafik-kemiringan" class="grafik-box grafik" style="display: block;">
                <h4 style="text-align: center; margin-bottom: 15px;">Grafik Sensor Kemiringan</h4>
                <canvas id="chartKemiringan" width="400" height="200"></canvas>
            </div>

            <div id="grafik-getaran" class="grafik-box grafik" style="display: none;">
                <h4 style="text-align: center; margin-bottom: 15px;">Grafik Sensor Getaran</h4>
                <canvas id="chartGetaran" width="400" height="200"></canvas>
            </div>

            <div id="grafik-kelembapan" class="grafik-box grafik" style="display: none;">
                <h4 style="text-align: center; margin-bottom: 15px;">Grafik Sensor Kelembapan</h4>
                <canvas id="chartKelembapan" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    function showGrafik(sensor) {
        const grafikList = ['kemiringan', 'getaran', 'kelembapan'];
        grafikList.forEach(g => {
            document.getElementById('grafik-' + g).style.display = (g === sensor) ? 'block' : 'none';
        });
    }

    async function fetchChartData() {
        const response = await fetch('/last10-sensor'); // Pastikan ini sesuai dengan web.php
        const data = await response.json();
        return data;
    }

    const kemiringanCtx = document.getElementById('chartKemiringan').getContext('2d');
    const getaranCtx = document.getElementById('chartGetaran').getContext('2d');
    const kelembapanCtx = document.getElementById('chartKelembapan').getContext('2d');

    const kemiringanChart = new Chart(kemiringanCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Kemiringan (°)',
                data: [],
                borderColor: '#1976D2',
                backgroundColor: 'rgba(25, 118, 210, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { title: { display: true, text: 'Waktu' } },
                y: { beginAtZero: true }
            }
        }
    });

    const getaranChart = new Chart(getaranCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Getaran (mm/s)',
                data: [],
                borderColor: '#F44336',
                backgroundColor: 'rgba(244, 67, 54, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { title: { display: true, text: 'Waktu' } },
                y: { beginAtZero: true }
            }
        }
    });

    const kelembapanChart = new Chart(kelembapanCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Kelembapan (%RH)',
                data: [],
                borderColor: '#4CAF50',
                backgroundColor: 'rgba(76, 175, 80, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { title: { display: true, text: 'Waktu' } },
                y: { beginAtZero: true }
            }
        }
    });

    async function updateCharts() {
        const data = await fetchChartData();

        kemiringanChart.data.labels = data.labels;
        kemiringanChart.data.datasets[0].data = data.kemiringan;
        kemiringanChart.update();

        getaranChart.data.labels = data.labels;
        getaranChart.data.datasets[0].data = data.getaran;
        getaranChart.update();

        kelembapanChart.data.labels = data.labels;
        kelembapanChart.data.datasets[0].data = data.kelembapan;
        kelembapanChart.update();
    }

    setInterval(updateCharts, 5000);
    updateCharts();
</script>

@stop
