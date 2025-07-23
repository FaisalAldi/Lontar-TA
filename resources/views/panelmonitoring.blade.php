@extends ('layout')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/panelmonitoring.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="content-container">
    <div class="judul">
        <h2>Dashboard Monitoring</h2>
    </div>
    <div class="grid-container">
        <!--KIRI-->
        <div class="left-container">
            <div class="sensor-container">
                <div class="sub1-container">
                    <h3>Sensor Kemiringan</h3>
                    <div class="content">
                        <div class="icon-value">
                            <i class="fas fa-compass icon"></i>
                            <div class="data-wrapper">
                                <div class="data-group">
                                    <div class="value"><span id="kemiringan">{{ $latestSensor->kemiringan ?? '-' }}</span></div>
                                    <div class="unit">Derajat</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sub2-container">
                    <h3>Sensor Getaran</h3>
                    <div class="content">
                       <div class="icon-value">
                            <i class="fas fa-rss icon"></i>
                            <div>
                                <div class="value"><span id="getaran">{{ $latestSensor->getaran ?? '-' }}</span></div>
                                <div class="unit">Hertz<br>(Hz)</div>
                            </div>
                       </div>
                    </div>
                </div>
                <div class="sub3-container">
                    <h3>Sensor Kelembapan</h3>
                    <div class="content">
                        <div class="icon-value">
                            <i class="fas fa-tint icon"></i>
                            <div>
                                <div class="value"><span id="kelembapan">{{ $latestSensor->kelembapan ?? '-' }}</span></div>
                                <div class="unit">Relative Humidity<br>(RH)</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sub4-container">
                <h3>Grafik</h3>
                <div class="grafik-box grafik">
                    <canvas id="chartGabungan"width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        
        <!--KANAN-->
        <div class="right-container">
            <div class="sub5-container">
                <h3>Trend Terkini</h3>
                <ul class="trend-list" id="trend-list">
                </ul>
            </div>

            <div class="sub6-container">
                <h3>Keterangan Bahaya</h3>

                @php
                    $bahaya = $bahaya ?? 'Normal';

                    $normalClass  = $bahaya === 'Normal' ? 'bahaya-aktif-normal' : '';
                    $siagaClass   = $bahaya === 'Siaga' ? 'bahaya-aktif-siaga' : '';
                    $waspadaClass = $bahaya === 'Waspada' ? 'bahaya-aktif-waspada' : '';
                    $awasClass    = $bahaya === 'Awas' ? 'bahaya-aktif-awas' : '';
                @endphp

                <div class="bahaya-container" id="bahayaContainer">
                    <div class="bahaya-box normal" id="boxNormal">Normal</div>
                    <div class="bahaya-box siaga" id="boxSiaga">Siaga</div>
                    <div class="bahaya-box waspada" id="boxWaspada">Waspada</div>
                    <div class="bahaya-box awas" id="boxAwas">Awas</div>
                </div>

                <div class="deskripsi-level">
                    <h4>Deskripsi Level</h4>
                    <p><strong>Normal</strong> : Kondisi aman dan stabil.</p>
                    <p><strong>Siaga</strong> : Kondisi mulai waspada, pantau terus perkembangan.</p>
                    <p><strong>Waspada</strong> : Risiko meningkat, siapkan langkah antisipasi.</p>
                    <p><strong>Awas</strong> : Bahaya tinggi, segera ambil tindakan.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctxGabungan = document.getElementById('chartGabungan').getContext('2d');

    const gabunganChart = new Chart(ctxGabungan, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Kemiringan (Hz)',
                    data: [],
                    borderColor: '#1976D2',
                    backgroundColor: 'rgba(25, 118, 210, 0.1)',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y2'
                },
                {
                    label: 'Sensor Getaran (mm/s)',
                    data: [],
                    borderColor: '#F44336',
                    backgroundColor: 'rgba(244, 67, 54, 0.1)',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y'
                },
                {
                    label: 'Sensor Kelembapan (%)',
                    data: [],
                    borderColor: '#4CAF50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                x: {
                    title: { display: true, text: 'Waktu' }
                },
                y: {
                    beginAtZero: true,
                    min: 0,
                    max: 100,
                    position: 'left',
                    title: { display: true, text: 'Getaran & Kelembapan' }
                },
                y2: {
                    beginAtZero: true,
                    min: 0,
                    max: 360,
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    title: { display: true, text: 'Kemiringan (°)' }
                }
            }
        }
    });
   
    let lastDataTimestamp = null;

    async function loadGabunganChartData() {
        try {
            const response = await fetch('/api/last10-sensor');
            const data = await response.json();

            if (data.latest_created_at !== lastDataTimestamp) {
                lastDataTimestamp = data.latest_created_at;

                gabunganChart.data.labels = data.labels;
                gabunganChart.data.datasets[0].data = data.kemiringan;
                gabunganChart.data.datasets[1].data = data.getaran;
                gabunganChart.data.datasets[2].data = data.kelembapan;

                gabunganChart.update();
            }
        } catch (error) {
            console.error("Gagal memuat data sensor:", error);
        }
    }

    setInterval(loadGabunganChartData, 1000); 

</script>
<script>
function fetchLatestSensorData() {
    fetch('/api/latest-sensor')
        .then(response => response.json())
        .then(data => {
            // Tampilkan data ke elemen HTML
            document.getElementById('getaran').innerText = data.getaran;
            document.getElementById('kelembapan').innerText = data.kelembapan;
            document.getElementById('kemiringan').innerText = data.kemiringan;

            // Hapus kelas aktif dari semua status
            document.querySelectorAll('.bahaya-box').forEach(el => el.classList.remove('aktif'));

            // Tambahkan kelas aktif ke status yang sesuai
            const status = data.bahaya?.toLowerCase(); // "Normal" -> "normal"
            if (status) {
                const el = document.querySelector(`.bahaya-box.${status}`);
                if (el) el.classList.add('aktif');
            }
        })
        .catch(error => console.error('Gagal fetch data sensor:', error));
}

setInterval(fetchLatestSensorData, 3000); // refresh tiap 3 detik
</script>
<script>
    function updateBahayaBox(bahaya) {
        const classes = {
            'Normal': 'bahaya-aktif-normal',
            'Siaga': 'bahaya-aktif-siaga',
            'Waspada': 'bahaya-aktif-waspada',
            'Awas': 'bahaya-aktif-awas'
        };

        ['Normal', 'Siaga', 'Waspada', 'Awas'].forEach(level => {
            const box = document.getElementById('box' + level);
            box.classList.remove(classes[level]);
            if (level === bahaya) {
                box.classList.add(classes[level]);
            }
        });
    }

    function fetchLatestBahaya() {
        fetch('{{ route('latest.sensor') }}')
            .then(response => response.json())
            .then(data => {
                if (data.bahaya) {
                    updateBahayaBox(data.bahaya);
                }
            })
            .catch(error => console.error('Error fetching bahaya:', error));
    }

    // Jalankan saat awal dan setiap 5 detik
    fetchLatestBahaya();
    setInterval(fetchLatestBahaya, 5000);
</script>

<script>
function fetchTrend() {
    fetch('/trend-terkini')
        .then(response => response.json())
        .then(data => {
            const list = document.getElementById('trend-list');
            list.innerHTML = ''; // kosongkan dulu

            data.trendTerkini.forEach((item, index) => {
                const li = document.createElement('li');
                li.textContent = `${index + 1}. ${item}`;
                list.appendChild(li);
            });
        })
        .catch(error => {
            console.error('Gagal memuat data realtime:', error);
        });
}

// Panggil saat pertama
fetchTrend();

// Refresh otomatis setiap 5 detik
setInterval(fetchTrend, 5000);
</script>


@stop