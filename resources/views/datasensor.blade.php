@extends('layout')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/datasensor.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="content-container">
    <div class="judul">
        <h2>Data Sensor</h2>
    </div>

    <div class="filter-section">
        <label for="start">Pilih Tanggal</label>
        <input type="text" id="start" placeholder="dd/mm/yyyy">

        <label for="end">Sampai Tanggal</label>
        <input type="text" id="end" placeholder="dd/mm/yyyy">

        <button class="filter-button" onclick="filterData()">Filter Data</button>
        <button class="reset-button" onclick="resetFilter()">Reset Filter</button>
        <button class="download-button">Download</button>
        <div id="download-popup" style="
    display: none;
    position: fixed;
    bottom: 30px;
    right: 30px;
    background-color: #007bff;
    color: white;
    padding: 12px 20px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    z-index: 1000;
">
    Berhasil mengunduh file!
</div>
    </div>

    <div class="tabel-data">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal & Waktu</th>
                    <th>Kemiringan</th>
                    <th>Getaran</th>
                    <th>Kelembapan</th>
                    <th>Potensi Bahaya</th>
                </tr>
            </thead>
            <tbody id="data-table-body">
                <!-- Data ditampilkan di sini -->
            </tbody>
        </table>

        <div class="pagination" style="text-align:center; margin-top: 10px;">
            <button onclick="prevPage()">Sebelumnya</button>
            <span id="page-indicator">Halaman 1</span>
            <button onclick="nextPage()">Berikutnya</button>
        </div>
    </div>
</div>

<script>
    let currentPage = 1;
    let lastPage = 1;
    let intervalId = null;
    let isFilterActive = false;

    function loadSensorData(page = 1) {
        fetch(`/api/data-sensor?page=${page}`)
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('data-table-body');
                tbody.innerHTML = '';
                let nomor = (data.current_page - 1) * data.per_page + 1;

                data.data.forEach(item => {
                    const row = `
                        <tr>
                            <td>${nomor++}</td>
                            <td>${formatTanggal(item.created_at)}</td>
                            <td>${item.kemiringan}</td>
                            <td>${item.getaran}</td>
                            <td>${item.kelembapan}</td>
                            <td>${item.bahaya ?? 'Tidak Terdeteksi'}</td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });

                currentPage = data.current_page;
                lastPage = data.last_page;
                document.getElementById('page-indicator').innerText = `Halaman ${currentPage}`;
            })
            .catch(error => console.error('Gagal mengambil data sensor:', error));
    }

    function convertToYMD(dateStr) {
        const [day, month, year] = dateStr.split('/');
        return `${year}-${month}-${day}`;
    }

    function filterData() {
        const startRaw = document.getElementById('start').value;
        const endRaw = document.getElementById('end').value;

        if (!startRaw || !endRaw) {
            alert('Tanggal harus diisi!');
            return;
        }

        const start = convertToYMD(startRaw);
        const end = convertToYMD(endRaw);

        clearInterval(intervalId);
        isFilterActive = true;

        fetch(`/api/data-sensor/filter?start=${start}&end=${end}`)
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('data-table-body');
                tbody.innerHTML = '';
                let nomor = 1;

                data.forEach(item => {
                    const row = `
                        <tr>
                            <td>${nomor++}</td>
                            <td>${formatTanggal(item.created_at)}</td>
                            <td>${item.kemiringan}</td>
                            <td>${item.getaran}</td>
                            <td>${item.kelembapan}</td>
                            <td>${item.bahaya ?? 'Tidak Terdeteksi'}</td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });

                document.getElementById('page-indicator').innerText = `Hasil Filter`;
            })
            .catch(error => console.error('Gagal memfilter data:', error));
    }

    document.querySelector('.download-button').addEventListener('click', () => {
        const start = document.getElementById('start').value;
        const end = document.getElementById('end').value;

        let url = '/api/data-sensor/download';

        if (start && end) {
            url += `?start=${encodeURIComponent(start)}&end=${encodeURIComponent(end)}`;
        }

        showDownloadPopup(); // tampilkan popup

        // mulai download
        window.location.href = url;
    });

    function nextPage() {
        if (isFilterActive) return;
        if (currentPage < lastPage) {
            loadSensorData(currentPage + 1);
        }
    }

    function prevPage() {
        if (isFilterActive) return;
        if (currentPage > 1) {
            loadSensorData(currentPage - 1);
        }
    }

    function formatTanggal(tanggal) {
        const date = new Date(tanggal);
        return date.toLocaleString('id-ID', {
            day: '2-digit', month: '2-digit', year: 'numeric',
            hour: '2-digit', minute: '2-digit', second: '2-digit'
        });
    }

    // Inisialisasi Flatpickr
    flatpickr("#start", { dateFormat: "d/m/Y" });
    flatpickr("#end", { dateFormat: "d/m/Y" });

    // Load pertama
    loadSensorData();
    intervalId = setInterval(() => {
        if (!isFilterActive) loadSensorData(currentPage);
    }, 3000);
    function resetFilter() {
    // Kosongkan input tanggal
    document.getElementById('start').value = '';
    document.getElementById('end').value = '';

    // Reset flag filter
    isFilterActive = false;

    // Set halaman ke 1
    currentPage = 1;

    // Perbarui indikator halaman
    document.getElementById('page-indicator').innerText = `Halaman 1`;

    // Muat ulang data tanpa filter
    loadSensorData(1);

    // Aktifkan kembali auto-refresh
    clearInterval(intervalId);
    intervalId = setInterval(() => {
        if (!isFilterActive) loadSensorData(currentPage);
    }, 3000);
}
function showDownloadPopup() {
    const popup = document.getElementById('download-popup');
    popup.style.display = 'block';

    setTimeout(() => {
        popup.style.display = 'none';
    }, 3000); // popup akan hilang setelah 3 detik
}

</script>
@stop
