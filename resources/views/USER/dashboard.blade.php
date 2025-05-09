<!DOCTYPE html>
<html>
<head>
    <title>Monitoring BBM</title>
    <link rel="stylesheet" href="https://monitoring-bbm.my.id/public/USER/bootstrap/css/bootstrap.min.css">
    <link rel="icon" href="https://monitoring-bbm.my.id/public/Monitoring-BBM-logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@1.0.0"></script>
    <style>
        #map {
            height: 283px; 
            width: 100%;
        }
    </style>
</head>
<body>
    <?php
        $sidebarContent = file_get_contents(public_path('USER/html/sidebar.html'));
        $navbarContent = file_get_contents(public_path('USER/html/navbar.html'));
        $htmlContent = file_get_contents(public_path('USER/html/dashboard.html'));
        $activecompany = '<p id="toggleBtn" style="margin-top: 20px; font-size: 18px; text-align:center;">' . $user->companyname . '</p>';
        $sidebarContent = str_replace('<!-- Company Name -->', $activecompany, $sidebarContent);
        $htmlContent = str_replace('<!-- List Konten disini -->', $sidebarContent, $htmlContent); 
        $htmlContent = str_replace('<!-- Navbar -->', $navbarContent, $htmlContent); 
        $activeuser = '<span style="margin-left: 15px;">' . $user->name . '</span>';
        $htmlContent = str_replace('<span style="margin-left: 15px;">Unknown</span>', $activeuser, $htmlContent);
        
        $dataRows = []; 
        $totalMessagesS = '';
        $headerSuggest = '';
        $rekapRowsK1 = ''; 
        
        $lastCollectionName = null;
        $companyname = null;
        $countweek = 0;
        $deviceCheckMessageDisplayed = false;
        
        $nik = []; 
        foreach($dashboard as $dshbrd) {
            if (!in_array($dshbrd->nik, $nik)) { 
                $nik[] = $dshbrd->nik; 
            } 
        }
        
        $totalMessagesS .= "<div class=\"col-md-4\">
                                <a href=\"/message\">
                                <div class=\"message-card\">
                                    <div class=\"message-card-header bg-dark\">
                                        Messages
                                    </div>
                                    <div class=\"message-card-body\">
                                        <h3 class=\"card-title\"><i class=\"fa fa-comment\"></i> $totalMessages</h3>
                                    </div>
                                </div>
                                </a>
                            </div>";
                            
        $embeddedMaps = '<div id="map"></div>';
        $htmlContent = str_replace('<!-- Region IFrame -->', $embeddedMaps, $htmlContent);
        
        $nikList = '';
        foreach ($nik as $item) {
            $nikList .= '
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://cdn-icons-png.flaticon.com/512/2505/2505287.png" alt="Car Icon" class="me-3" style="width: 30px;">
                            <span class="me-3">' . $item . '</span>
                            <span class="badge bg-danger">Trouble</span>
                            <div style="margin-left: 25px;" class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="switch' . $item . '" name="input-chkbox" value="' . $item . '">
                                <label class="form-check-label" for="switch' . $item . '">Activate</label>
                            </div>
                        </div>';
        }
        
        $htmlContent = str_replace('<!-- Total Messages -->', $totalMessagesS, $htmlContent);
        $htmlContent = str_replace('<!--device list-->', $nikList, $htmlContent);

        $nikCounts = [];
        foreach($dashboard as $dshbrd){

            $nik = $dshbrd->nik; 
            if (isset($nikCounts[$nik])) {
                $nikCounts[$nik]++; 
            } else {
                $nikCounts[$nik] = 1;
            }
        }
        
        $nikLabels = json_encode(array_keys($nikCounts)); 
        $nikCountsData = json_encode(array_values($nikCounts)); 
        echo $htmlContent;
    ?>
    <div id="data-container" data-server-data="{{ json_encode($dashboard) }}"></div>
    <div id="data-container2" data-server-data2="{{ json_encode($nikLabels) }}"></div>
    <div id="data-container3" data-server-data2="{{ json_encode($nikCountsData) }}"></div>
    <div id="data-container4" data-server-data4="{{ json_encode($dashboard) }}"></div>

    @include('USER.sidebar')
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
    
        locations.forEach(function(location) {
            var marker = L.marker(location.coords).addTo(map);
            console.log(marker);
            getAddress(location.coords[0], location.coords[1], function(address) {
                marker.bindPopup(`<b>Address:</b><br>${address}`).openPopup();
            });
        });
    
    var checkboxes = document.getElementsByName('input-chkbox');
    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            if (checkbox.checked) {
                console.log('Checkbox dengan nilai: ' + checkbox.value + ' dicentang');
            } else {
                console.log('Checkbox dengan nilai: ' + checkbox.value + ' tidak dicentang');
            }
        });
    });
    
    document.addEventListener('DOMContentLoaded', function () {
        const dataContainer = document.getElementById('data-container');
        const dataContainer4 = document.getElementById('data-container4');
    
        async function fetchDashboardData() {
            try {
                const response = await fetch('/dashboard/data');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
    
                const newData = await response.json();
                dataContainer.setAttribute('data-server-data', JSON.stringify(newData));
                console.log('Updated data:', newData);
    
                dataContainer4.setAttribute('data-server-data4', JSON.stringify(newData));
    
                const dataFromServer = newData;
                console.log(dataFromServer);
    
                const xValues = dataFromServer.map(item => item.timestamp);
                const sortedXValues = Array.from(new Set(xValues)).sort((a, b) => new Date(a) - new Date(b));
    
                const groupedData = dataFromServer.reduce((acc, item) => {
                    const nik = item.nik;
                    if (!acc[nik]) {
                        acc[nik] = [];
                    }
                    acc[nik].push(item);
                    return acc;
                }, {});
    
                const colors = [
                    { backgroundColor: "rgba(0,0,255,0.2)", borderColor: "rgba(0,0,255,0.6)" }, // Biru
                    { backgroundColor: "rgba(255,165,0,0.2)", borderColor: "rgba(255,165,0,0.6)" }, // Oranye
                    { backgroundColor: "rgba(0,128,0,0.2)", borderColor: "rgba(0,128,0,0.6)" }, // Hijau
                    { backgroundColor: "rgba(255,0,0,0.2)", borderColor: "rgba(255,0,0,0.6)" } // Merah
                ];
    
                const datasets2 = Object.keys(groupedData).map((nik, index) => {
                    const color = colors[index % colors.length];
                    return {
                        label: `${nik}`,
                        fill: true,
                        lineTension: 0,
                        backgroundColor: color.backgroundColor,
                        borderColor: color.borderColor,
                        data: groupedData[nik]
                            .sort((a, b) => new Date(a.timestamp) - new Date(b.timestamp))
                            .map(item => ({
                                x: new Date(item.timestamp),
                                y: item.level
                            })),
                    };
                });
    
                if (datasets2.length === 0) {
                    datasets2.push({
                        label: "No Data",
                        fill: true,
                        lineTension: 0,
                        backgroundColor: "rgba(211,211,211,1.0)",
                        borderColor: "rgba(211,211,211,0.6)",
                        borderWidth: 1,
                        data: Array(sortedXValues.length || 1).fill(0)
                    });
                    sortedXValues.push("No Data");
                }
    
                const initialData = {
                    labels: sortedXValues,
                    datasets: datasets2
                };
    
                const config2 = {
                    type: 'line',
                    data: initialData,
                    options: {
                        responsive: true,
                        animation: false,
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Time'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Value'
                                }
                            }
                        }
                    }
                };
    
                let chartInstance = Chart.getChart("BbmCRT");
                if (chartInstance) {
                    chartInstance.destroy(); 
                }
    
                chartInstance = new Chart("BbmCRT", config2);
    
                function updateChart() {
                    const nikObject = groupedData; 
    
                    if (chartInstance) {
                        let dataUpdated = false;
    
                        for (let nik in nikObject) {
                            if (nikObject.hasOwnProperty(nik)) {
                                const data = nikObject[nik];
    
                                if (data.length > 0) {
                                    const { level, timestamp } = data[data.length - 1];
                                    const newTimestamp = new Date(timestamp).toLocaleTimeString(); 
                                    const newLevel = level;
    
                                    let dataset = chartInstance.data.datasets.find(ds => ds.label === nik);
                                    if (!dataset) {
                                        const colorIndex = chartInstance.data.datasets.length % colors.length;
                                        const color = colors[colorIndex];
                                        chartInstance.data.datasets.push({
                                            label: nik,
                                            fill: false,
                                            lineTension: 0,
                                            backgroundColor: color.backgroundColor,
                                            borderColor: color.borderColor,
                                            data: []
                                        });
                                        dataset = chartInstance.data.datasets[chartInstance.data.datasets.length - 1];
                                    }
    
                                    dataset.data.push({
                                        x: newTimestamp,
                                        y: newLevel
                                    });
    
                                    if (!chartInstance.data.labels.includes(newTimestamp)) {
                                        chartInstance.data.labels.push(newTimestamp);
                                    }
    
                                    dataUpdated = true;
                                }
                            }
                        }

                        if (dataUpdated) {
                            const maxDataPoints = 10;
                            if (chartInstance.data.labels.length > maxDataPoints) {
                                chartInstance.data.labels.shift();
                                chartInstance.data.datasets.forEach(ds => {
                                    ds.data.shift(); 
                                });
                            }
                            chartInstance.update();
                        } else {
                            console.log("No new data to update the chart.");
                        }
                    } else {
                        console.error("Chart instance is not defined correctly");
                    }
                }
                setInterval(updateChart, 1000);
    
            } catch (error) {
                console.error('Error fetching dashboard data:', error);
            }
        }
        fetchDashboardData();
        setInterval(fetchDashboardData, 2000); 
    });

    const labels = <?php echo $nikLabels; ?>;
    const dataCounts = <?php echo $nikCountsData; ?>;
    const ctx = document.getElementById('myHorizontalBarChart').getContext('2d');

    const data = {
        labels: labels,
        datasets: [{
            label: 'Resources Usage',
            data: dataCounts,
            backgroundColor: [
                'rgb(255, 99, 132)',
                'rgb(54, 162, 235)',
                'rgb(255, 206, 86)',
                'rgb(75, 192, 192)',
                'rgb(153, 102, 255)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)'
            ],
            borderWidth: 1
        }]
    };
    
    const config = {
        type: 'bar',
        data: data,
        options: {
            indexAxis: 'y',
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        }
    };
    new Chart(ctx, config);
    </script>
    <script src = "https://monitoring-bbm.my.id/public/USER/js/dashboard-function2.js"></script>
    <script src = "{{ asset('https://monitoring-bbm.my.id/public/USER/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src = "https://monitoring-bbm.my.id/public/USER/js/navigate-function.js"></script>
</body>
</html>