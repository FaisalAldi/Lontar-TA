<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Tanah Longsor</title>
    <link rel="stylesheet" href="{{ asset('assets/css/layout.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <!-- SIDEBAR -->
        <div class="sidebar-section" id="sidebar">
            <div class="sidebar-top">
                <img src="{{ asset('assets/image/logo Dashboard.png') }}" alt="Logo" class="logo">
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
                <h2>LONTAR</h2>
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
</body>
</html>
        