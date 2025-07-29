<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Tanah Longsor</title>
    <link rel="stylesheet" href="{{ asset('assets/css/layout.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <!-- SIDEBAR -->
        <div class="sidebar-section" id="sidebar">
            <div class="sidebar-top">
                <img src="{{ asset('assets/image/Logo_Lontar.png') }}" alt="Logo" class="logo">
            </div>
            <div class="sidebar" id="sidebarContent">
                <p>Dashboard</p>
                <a href="/"><i class="fas fa-tachometer-alt"></i> Dashboard Monitoring</a>
                <a href="/PanelGrafik"><i class="fas fa-chart-line"></i> Grafik Sensor</a>
                <a href="/DataSensor"><i class="fas fa-database"></i> Data Sensor</a>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="main-section">
            <div class="header">
                <button class="toggle-sidebar mobile-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h2>Halo, Sobat LONTAR!</h2>
            </div>
            <div id="emergencyPopup" class="emergency-popup hidden">
                <strong>⚠️ Peringatan Bahaya!</strong><br>
                Call Center BNPB:<br>
                <a><i class="fas fa-phone"></i> (024) 3519904</a>
            </div>

            <div class="content">
                @yield('content')
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const sidebarContent = document.getElementById('sidebarContent');
            sidebar.classList.toggle('collapsed');
            sidebarContent.style.display = sidebar.classList.contains('collapsed') ? 'none' : 'block';
        }

        function toggleNotifikasi() {
            const popup = document.getElementById('popupNotifikasi');
            popup.style.display = popup.style.display === 'none' ? 'block' : 'none';
        }

        window.addEventListener('click', function(e) {
            const notifikasiBtn = document.querySelector('.icon-notifikasi');
            const popup = document.getElementById('popupNotifikasi');
            if (!notifikasiBtn?.contains(e.target) && !popup?.contains(e.target)) {
                popup.style.display = 'none';
            }
        });
    </script>
    <script>
    // Toggle tampil atau sembunyi popup
    function toggleEmergencyPopup(show) {
        const popup = document.getElementById('emergencyPopup');
        if (!popup) return;

        if (show) {
            popup.classList.remove('hidden');
        } else {
            popup.classList.add('hidden');
        }
    }

    // Cek status darurat dari server
    async function checkEmergencyStatus() {
        try {
            const response = await fetch('{{ route("latest.sensor") }}');
            const data = await response.json();

            const isEmergency = data.level_id > 1;
            toggleEmergencyPopup(isEmergency);  // Tampil jika darurat
        } catch (error) {
            console.error('Gagal mengambil status darurat:', error);
        }
    }

    // Jalankan saat load dan setiap 5 detik
    checkEmergencyStatus();
    setInterval(checkEmergencyStatus, 5000);
</script>

</body>
</html>
        