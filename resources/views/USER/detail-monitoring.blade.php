<!DOCTYPE html>
<html lang="en">
<head>
    <title>Monitoring BBM</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans">
    <link rel="stylesheet" href="https://monitoring-bbm.my.id/public/ADMIN/bootstrap/css/bootstrap.min.css">
    <link rel="icon" href="https://monitoring-bbm.my.id/public/Monitoring-BBM-logo.png" type="image/x-icon">
    <style>
        h1, h2, h3, h4, h5, h6 {font-family: 'Poppins', sans-serif;}
        body, table, button {
            margin: 0;
            padding: 0;
            font-family: "Open Sans", sans-serif;
        }
        body {
            background-color: #ffffff;
            color: #2c3e50;
            line-height: 1.6;
        }
        header {
            background: #333;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        /* Centered button container */
        .button-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 20px 0;
        }
        /* Style for buttons */
        .toggle-btn {
            margin-top:2px;
            background-color: #333;
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s;
        }
        .toggle-btn:hover {
            background-color: #1a252f;
        }
        /* Style the table */
        table {
            display: none;
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        table thead {
            background-color: #2c3e50;
            color: #ffffff;
        }
        table th, table td {
            padding: 12px 15px;
            text-align: center;
            border: 1px solid #ddd;
        }
        table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        table tbody tr:hover {
            background-color: #e6e6e6;
        }
        @media (max-width: 768px) {
            .table-container {
                width: 100%;
                overflow-x: auto;
            }

            table {
                width: 100%; 
            }

            th, td {
                font-size: 0.9em; 
                padding: 8px;
            }
            button {
                font-size: 1em;
                padding: 8px 15px;
            }
            .button-group {
                flex-direction: row;
                gap: 10px;
            }
            .navbar-container h2{
                font-size: 1em;
            }
        }
        .navbar-container{
            padding-left: 5%;
        }
        canvas {
            display: block;
            width: 90%;
            max-width: 800px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        footer {
            text-align: center;
            padding: 20px;
            background-color: #2c3e50;
            color: #ffffff;
            position: absolute;
            bottom: 0;
            width: 100%;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="navbar-container">
            <h2 style="color:white;">Monitoring BBM</h2>
        </div>
    </nav>
    
    
    <br>
    <div style="text-align: center;">
        <button id="toggleTableBtn" class="toggle-btn" onclick="toggleTable()">Show Tabel</button>
        <button id="toggleDownloadTableBtn" class="toggle-btn" onclick="toggleDownloadTable()">Download Tabel</button>
        <button id="toggleDailyChartBtn" class="toggle-btn" onclick="toggleChart('dailyChart', this)">Hide Diagram Per Hari</button>
        <button id="toggleMonthlyChartBtn" class="toggle-btn" onclick="toggleChart('weeklyChart', this)">Hide Diagram Per Minggu</button>
        <button id="toggleYearlyChartBtn" class="toggle-btn" onclick="toggleChart('monthlyChart', this)">Hide Diagram Per Bulan</button>
    </div>
    
    <div class="container-fluid">
    <div class="row justify-content-center">
        <main class="col-md-9 col-lg-10 ml-sm-auto px-4">
            <div class="table-container">
                <table id="data-table">
                    <thead>
                        <tr>
                            <th>NIK</th>
                            <th>Level</th>
                            
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($dataForChart as $data): ?>
                        <tr>
                            <td><?php echo $data['nik']; ?></td>
                            <td><?php echo $data['level']; ?></td>
                            <td><?php echo $data['timestamp']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    </div>
    <?php
    
        $savedData = [];

        foreach($dataForChart as $data) {
            $savedData[] = [
                'nik' => $data['nik'],
                'level' => $data['level'],
                
                'timestamp' => $data['timestamp']
            ];
        }
        
    ?>
    <!-- Hourly Chart -->
    <canvas id="dailyChart"></canvas>

    <!-- Daily Chart -->
    <canvas id="weeklyChart"></canvas>

    <!-- Monthly Chart -->
    <canvas id="monthlyChart"></canvas>

    <script>
       var chartData = <?php echo json_encode($savedData); ?>;
       console.log(chartData); 
       
       /*
       dataForChart.forEach(data => {
        console.log("NIK:", data.nik);
        console.log("Level:", data.level);
        console.log("Jarak:", data.jarak);
       });*/
       
       /*
       for (let key in dataForChart) {
            console.log(`${key}: ${dataForChart[key]}`);
       }*/
       
       /*
       Object.entries(dataForChart).forEach(([key, value]) => {
            console.log(`${key}: ${value}`);
        });*/
       function toggleTable() {
            var table = document.getElementById('data-table');
            var button = document.getElementById('toggleTableBtn');
            if (table.style.display === 'none' || table.style.display === '') {
                table.style.display = 'table';
                button.textContent = 'Hide Tabel';
            } else {
                table.style.display = 'none';
                button.textContent = 'Show Tabel';
            }
        }
        
        /*
        function toggleDownloadTable(){
            var table = document.getElementById('data-table');
            var rows = table.querySelectorAll('tr'); // Ambil semua baris dari tabel
            var csvContent = "";
            
            rows.forEach(row => {
            var cols = row.querySelectorAll('th, td'); // Ambil semua kolom dari baris
            var rowData = [];
            cols.forEach(col => {
                    rowData.push('"' + col.textContent.trim() + '"'); // Escape dengan tanda kutip
                });
                csvContent += rowData.join(",") + "\n"; // Gabungkan kolom dengan koma dan tambahkan baris baru
            });
    
            // Buat blob untuk file CSV
            var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            var url = URL.createObjectURL(blob);
    
            // Buat link download
            var a = document.createElement('a');
            a.href = url;
            a.download = "data-table.csv";
            a.style.display = 'none';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }*/
        
        function toggleDownloadTable() {
            var table = document.getElementById('data-table');
            var rows = table.querySelectorAll('tr'); // Ambil semua baris dari tabel
            var csvContent = "";

            rows.forEach(row => {
                var cols = row.querySelectorAll('th, td'); // Ambil semua kolom dari baris
                var rowData = [];
                cols.forEach(col => {
                    rowData.push('"' + col.textContent.trim() + '"'); // Escape dengan tanda kutip
                });
                csvContent += rowData.join(",") + "\n"; // Gabungkan kolom dengan koma dan tambahkan baris baru
            });

            // Buat blob untuk file CSV
            var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            var url = URL.createObjectURL(blob);

            // Buat link download
            var a = document.createElement('a');
            a.href = url;
            a.download = "data-table.csv";
            a.style.display = 'none';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
        
        function toggleChart(chartId, btn) {
            var chartCanvas = document.getElementById(chartId);
            if (chartCanvas.style.display === 'none') {
                chartCanvas.style.display = 'block';
                btn.textContent = 'Hide ' + btn.textContent.split(' ')[1];
            } else {
                chartCanvas.style.display = 'none';
                btn.textContent = 'Show ' + btn.textContent.split(' ')[1];
            }
        }
        
        function getDailyData() {
    const today = new Date();
    const todayDateString = today.toISOString().split('T')[0];
    const hourlyData = Array(24).fill(0).map(() => []);
    
    // Memasukkan level ke dalam hourlyData sesuai jamnya
    chartData.forEach(item => {
        const date = new Date(item.timestamp);
        const level = parseFloat(item.level);
        const dateString = date.toISOString().split('T')[0];
        if (dateString === todayDateString && !isNaN(level)) {
            const hour = date.getHours();
            hourlyData[hour].push(level);
        }
    });

    // Membuat label jam (00:00, 01:00, dst)
    const labels = hourlyData.map((_, hour) => `${hour}:00`);
    
    // Mengambil data terakhir untuk setiap jam
    const data = hourlyData.map(hourData => {
        if (hourData.length > 0) {
            return hourData[hourData.length - 1]; // Data terakhir
        } else {
            return 0; // Tidak ada data di jam tersebut
        }
    });

    return { labels, data };
}


        function getWeeklyData() {
            const groupedData = {};
            const today = new Date();
            const currentMonth = today.getMonth();
            const currentYear = today.getFullYear();

            chartData.forEach(item => {
                const date = new Date(item.timestamp);
                const level = parseFloat(item.level);
                if (date.getMonth() === currentMonth && date.getFullYear() === currentYear && !isNaN(level)) {
                    const day = date.getDate();
                    if (!groupedData[day]) {
                        groupedData[day] = [];
                    }
                    groupedData[day].push(level);
                }
            });

            const labels = Object.keys(groupedData).map(day => `Tanggal ${day}`);
            const data = labels.map(day => {
                const levels = groupedData[parseInt(day.split(' ')[1])];
                const sum = levels.reduce((acc, val) => acc + val, 0);
                return sum / levels.length;
            });

            return { labels, data };
        }

        function getMonthlyData() {
            const groupedData = Array(12).fill(0).map(() => []);

            chartData.forEach(item => {
                const date = new Date(item.timestamp);
                const level = parseFloat(item.level);
                if (date.getFullYear() === new Date().getFullYear() && !isNaN(level)) {
                    const month = date.getMonth();
                    groupedData[month].push(level);
                }
            });

            const labels = groupedData.map((_, month) => `Bulan ${month + 1}`);
            const data = groupedData.map(monthData => {
                if (monthData.length > 0) {
                    const sum = monthData.reduce((acc, val) => acc + val, 0);
                    return sum / monthData.length;
                } else {
                    return 0; 
                }
            });

            return { labels, data };
        }
        

        function createChart(chartId, dataFunction, titleText) {
            const { labels, data } = dataFunction();

            new Chart(chartId, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Level",
                        data: data,
                        fill: true,
                        backgroundColor: 'rgba(52, 152, 219, 0.2)',
                        borderColor: 'rgba(52, 152, 219, 1)',
                        borderWidth: 2,
                        pointRadius: 5,
                        pointBackgroundColor: 'rgba(52, 152, 219, 1)',
                        tension: 0.3
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)',
                            },
                            ticks: {
                                color: '#333'
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)',
                            },
                            ticks: {
                                color: '#333'
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: titleText,
                            font: {
                                size: 20,
                                weight: 'bold',
                                family: 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif'
                            },
                            color: '#333'
                        },
                        legend: {
                            labels: {
                                font: {
                                    size: 16,
                                    family: 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif'
                                },
                                color: '#333'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: { size: 16 },
                            bodyFont: { size: 14 },
                            padding: 12,
                        }
                    }
                }
            });
        }

        window.onload = function() {
            createChart('dailyChart', getDailyData, 'Diagram Per Hari');
            createChart('weeklyChart', getWeeklyData, 'Diagram Per Minggu');
            createChart('monthlyChart', getMonthlyData, 'Diagram Per Bulan');
        };
    </script>
</body>
</html>
