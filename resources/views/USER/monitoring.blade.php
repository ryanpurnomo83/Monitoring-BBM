<!DOCTYPE html>
    <html>
    <head>
        <title>Monitoring BBM</title>
        <link rel="stylesheet" href="https://monitoring-bbm.my.id/public/USER/bootstrap/css/bootstrap.min.css">
        <link rel="icon" href="https://monitoring-bbm.my.id/public/Monitoring-BBM-logo.png" type="image/x-icon">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>
<body>
    <?php
    $sidebarContent = file_get_contents(public_path('USER/html/sidebar.html'));
    $navbarContent = file_get_contents(public_path('USER/html/navbar.html'));
    $htmlContent = file_get_contents(public_path('USER/html/monitoring.html'));
    $transportationDashboard = '';
    
    $dataRows = ''; 
    $headerSuggest = '';
    $rekapRowsK1 = ''; 
    
    $lastCollectionName = null;
    $companyname = null;
    $countweek = 0;
    $deviceCheckMessageDisplayed = false;
    
    $dataRows2 = '';
    
    if (!empty($monitoring)) {  
        foreach ($monitoring as $mntrg) {
                $dataRows .= "<tr>
                              <td id=\"" . htmlspecialchars($mntrg['nik']) . "\">" . $mntrg['nik'] . "</td>
                              <td>" . $mntrg['level'] . "</td>
                              <td>" . ($mntrg['jarak'] ?? 'N/A') . "</td>
                              <td>" . $mntrg['timestamp'] . "</td>
                              <td><button class='button' onclick=\"showDetails('" . htmlspecialchars($mntrg['nik']) . "', '" . htmlspecialchars($mntrg['companyname']) . "')\">Details</button></td>
                              </tr>";
                $dataRows2 = $mntrg['companyname'];
        }
    } else {
        $headerSuggest .= "<h4>Please Check if Your Device is Connected to Internet (Your Device has been registered, but there is no data pass through it)</h4>";
        $htmlContent = str_replace('<!-- Header Suggestion -->', $headerSuggest, $htmlContent);
    }
    
    $companyname = json_encode($dataRows2);
    
    //var_dump($companyname);
    
    $transportationDashboard .= 
    '<form style="text-align: center;" id="redirectForm" action="' . route('transportation.dashboard') . '" method="POST">
        ' . csrf_field() . '
        <input type="hidden" name="user_id" value="' . $user->id . '">
        <button class="button" style="border: none;" type="submit">Transportation Dashboard</button>
    </form>';
    
    $htmlContent = str_replace('<!-- Transportation Dashboard -->', $transportationDashboard, $htmlContent);
    $htmlContent = str_replace('<!-- Monitoring Data -->', $dataRows, $htmlContent);
    $activecompany = '<p id="toggleBtn" style="margin-top: 20px; font-size: 18px; text-align:center;">' . $user->companyname . '</p>';
    $sidebarContent = str_replace('<!-- Company Name -->', $activecompany, $sidebarContent);
    $htmlContent = str_replace('<!-- List Konten disini -->', $sidebarContent, $htmlContent); 
    $htmlContent = str_replace('<!-- Navbar -->', $navbarContent, $htmlContent);
    echo $htmlContent;
    ?>
    @include('USER.sidebar')
    <script>
        function showDetails(nik, companyname) {
                window.location.href = `/monitoring/detail/${nik}/${companyname}/`;
        }
        <?php
        
        $companyname = str_replace(' ', '_', $companyname);
        
        ?>
        const companyname = <?php echo $companyname; ?>;
        
        console.log(companyname);
        
      $(document).ready(function(){
    function refreshData(companyname) {
        $.ajax({
            url: `/monitoring/data/${companyname}`,
            method: 'GET',
            success: function(response) {
                if (response) {
                    const tableContainer = $('#table-container');
                    tableContainer.empty();
                    
                    Object.keys(response).forEach(nik => {
                        const dataList = response[nik];
                        let tableRows = '';
                        
                        console.log(dataList);

                        tableRows += `
                            <tr>
                                <td id="${nik}">${dataList.nik}</td>
                                <td>${dataList.level}</td>
                                <td>${dataList.jarak ?? 'N/A'}</td>
                                <td>${dataList.timestamp}</td>
                                <td><button class='button' onclick="showDetails('${nik}', '${companyname}')">Details</button></td>
                            </tr>`;

                        const tableHTML = `
                          <table data-category="${dataList.nik}">
                                <thead>
                                    <tr>
                                        <th>NIK</th>
                                        <th>Level</th>
                                        <th>Jarak</th>
                                        <th>Timestamp</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${tableRows}
                                </tbody>
                          </table>`;
                            
                        tableContainer.append(`
                            <div>
                                ${tableHTML}
                            </div>
                        `);
                    });
                } else {
                    console.error('Unexpected response:', response);
                }
            },
            error: function(response, xhr, status, error) {
                console.error("Error refreshing data:", response, status, error);
            }
        });
    }

    setInterval(() => refreshData(companyname), 1000);
});

    </script>
    <script src = "https://monitoring-bbm.my.id/public/USER/js/navigate-function.js"></script>
</body>
</html>