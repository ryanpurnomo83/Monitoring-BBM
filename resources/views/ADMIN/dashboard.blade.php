<!DOCTYPE html>
<html>
<head>
    <title>Monitoring BBM</title>
    <link rel="stylesheet" href="https://monitoring-bbm.my.id/public/ADMIN/bootstrap/css/bootstrap.min.css">
    <link rel="icon" href="https://monitoring-bbm.my.id/public/Monitoring-BBM-logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!--<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>-->
    <!--<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@1.0.0"></script>-->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        #map {
            height: 283px; 
            width: 100%;
        }
    </style>
</head>
<body>
    <?php
        $sidebarContent = file_get_contents(public_path('ADMIN/html/sidebar.html'));
        $navbarContent = file_get_contents(public_path('ADMIN/html/navbar.html'));
        $htmlContent = file_get_contents(public_path('ADMIN/html/dashboard.html'));
        
        $htmlContent = str_replace('<!-- List Konten disini -->', $sidebarContent, $htmlContent); 
        $htmlContent = str_replace('<!-- Navbar -->', $navbarContent, $htmlContent); 
        $activeadmin = '<span style="margin-left: 15px;">' . $admin->name . '</span>';
        $htmlContent = str_replace('<span style="margin-left: 15px;">Unknown</span>', $activeadmin, $htmlContent);
        
        //var_dump($cities); 
        
        $dataRows = []; 
        $totalMessagesS = '';
        $activeUsersS = '';
        $AllUL = '';
        $combinedLocations = "";
        $headerSuggest = '';
        $rekapRowsK1 = ''; 
        
        $lastCollectionName = null;
        $companyname = null;
        $countweek = 0;
        $deviceCheckMessageDisplayed = false;
        
        $totalMessagesS .= "<div class=\"col-md-4\">
                                <a href=\"/admin/message\">
                                <div class=\"message-card\">
                                    <div class=\"message-card-header bg-dark\">
                                        Messages
                                    </div>
                                    <div class=\"message-card-body\">
                                        <h3 class=\"card-title\"><i class=\"fa fa-users\"></i> $totalMessages</h3>
                                    </div>
                                </div>
                                </a>
                            </div>
                            ";
        
        foreach($allUsersList as $index => $aUL){
            //$AllUL .= "<li>$aUL->name</li>";
            $userName = $aUL->name;
            //$userCity = $cities[$index] ?? 'Unknown';
            $AllUL .= "<li>$userName</li>";
            //- $userCity
        }
        
        $embeddedMaps = '<div id="map"></div>';
        //'<iframe src="https://www.google.com/maps/embed?pb=!1m16!1m12!1m3!1d22264678.02858319!2d106.59465116437157!3d2.5142767239405757!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!2m1!1spertambangan%20batu%20bara!5e1!3m2!1sid!2sid!4v1720250369953!5m2!1sid!2sid" width="100%" height="283px" style="border:0;" allowfullscreen="" loading="lazy"></iframe>';
        $htmlContent = str_replace('<!-- Region IFrame -->', $embeddedMaps, $htmlContent);
            
        $activeUsersS .= "<div class=\"col-md-6\">
                            <div class=\"card\">
                                <div class=\"card-header bg-primary text-white\">
                                    Active Users
                                </div>
                                <div class=\"card-body\">
                                    <h5 class=\"card-title\">Active Users Summary</h5>
                                    <p class=\"card-text\">
                                        Dalam 30 menit terakhir, ada <strong>$activeUsers</strong> pengguna yang aktif.
                                    </p>
                                </div>
                            </div>
                            
                            <br>
                            
                            <h5>Users List</h5>
                            <div class=\"card\">
                                <div class=\"card-header bg-primary text-white\">
                                    All Users
                                </div>
                                <div class=\"card-body\">
                                    <h5 class=\"card-title\">Users List Summary</h5>
                                    <!-- Users List -->
                                </div>
                            </div>
                            
                            <br>
                            
                            <h5>Users Management</h5>
                            <a href=\"/admin/user-management\">
                            <div class=\"card\">
                                <div class=\"card-header bg-primary text-white\">
                                    All Users
                                </div>
                                <div class=\"card-body\">
                                    <h5 class=\"card-title\">$totalUsers</h5>
                                </div>
                            </div>
                            </a>
                          </div>";
                          
        $htmlContent = str_replace('<!-- Total Messages -->', $totalMessagesS, $htmlContent);
        $activeUsersS = str_replace('<!-- Users List -->', $AllUL, $activeUsersS);
        $htmlContent = str_replace('<!-- Active User Section -->', $activeUsersS, $htmlContent);

        echo $htmlContent;
    ?>
    
    <div id="data-container" data-server-data="{{ json_encode($dataRows) }}"></div>
    @include('ADMIN.sidebar')
    <script src = "{{ asset('https://monitoring-bbm.my.id/public/ADMIN/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src = "https://monitoring-bbm.my.id/public/ADMIN/js/navigate-function.js"></script>
    <script>
        var map = L.map('map').setView([-6.9813134, 110.4094078], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var locations = [
            <?php foreach ($locations as $loc): ?>
            { 
                coords: [<?= $loc ?>], 
                popup: "<b>Loading address...</b>"
            },
            <?php endforeach; ?>
        ];

        function getAddress(lat, lng, callback) {
            var url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`;
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data && data.address) {
                        var address = data.display_name; // Alamat lengkap
                        callback(address);
                    } else {
                        callback("Address not found.");
                    }
                })
                .catch(error => {
                    console.error("Error fetching address:", error);
                    callback("Error fetching address.");
                });
        }
    
        // Tampilkan marker dengan alamat di popup
        locations.forEach(function(location) {
            var marker = L.marker(location.coords).addTo(map);
            
            // Gunakan reverse geocoding untuk mendapatkan alamat
            getAddress(location.coords[0], location.coords[1], function(address) {
                marker.bindPopup(`<b>Address:</b><br>${address}`).openPopup();
            });
        });
        
    
        var ctx = document.getElementById('activeUsersChart').getContext('2d');
        var activeUsersChart = new Chart(ctx, {
            type: 'pie', 
            data: {
                labels: ['Pengguna Aktif', 'Pengguna Tidak Aktif'], 
                datasets: [{
                    label: 'Pengguna Aktif',
                    data: [{{ $activeUsers }}, {{ $totalUsers - $activeUsers }}],
                    backgroundColor: ['#4CAF50', '#FF5733'],
                    borderColor: ['#4CAF50', '#FF5733'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw;
                            }
                        }
                    }
                }
            }
        });

        var ctx2 = document.getElementById('userStatsChart').getContext('2d');
        var userStatsChart = new Chart(ctx2, {
            type: 'bar', 
            data: {
                labels: ['Aktif', 'Tidak Aktif'], 
                datasets: [{
                    label: 'Jumlah Pengguna',
                    data: [{{ $activeUsers }}, {{ $totalUsers - $activeUsers }}], 
                    backgroundColor: ['#4CAF50', '#FF5733'],
                    borderColor: ['#4CAF50', '#FF5733'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
