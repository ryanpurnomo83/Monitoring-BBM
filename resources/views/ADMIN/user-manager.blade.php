<!DOCTYPE html>
<html>
<head>
    <title>Monitoring BBM</title>
    <link rel="stylesheet" href="https://monitoring-bbm.my.id/public/USER/bootstrap/css/bootstrap.min.css">
    <link rel="icon" href="https://monitoring-bbm.my.id/public/Monitoring-BBM-logo.png" type="image/x-icon">
</head>
<body>
    <?php
        $sidebarContent = file_get_contents(public_path('ADMIN/html/sidebar.html'));
        $navbarContent = file_get_contents(public_path('ADMIN/html/navbar.html'));
        $htmlContent = file_get_contents(public_path('ADMIN/html/user-manager.html'));
        $htmlContent = str_replace('<!-- List Konten disini -->', $sidebarContent, $htmlContent); 
        $htmlContent = str_replace('<!-- Navbar -->', $navbarContent, $htmlContent);
        $dataRows = '';
        
        foreach ($allUsersList as $aUL){
            $isRunning = session()->get('execution_status.' . $aUL, '') === 'running';
            $dataRows .= "
                        <li class=\"list-group-item\">
                            <p><strong>User : {$aUL->name}</p>
                            <button class=\"btn btn-primary btn-sm\">
                                SUSPEND
                            </button>
                            <button class=\"btn btn-primary btn-sm\">
                                DELETE
                            </button>
                        </li>";
        }
        $htmlContent = str_replace('<!-- User Manager -->', "<ul>{$dataRows}</ul>", $htmlContent);
        echo $htmlContent;
    ?>
    <div id="output" style="margin-top: 20px; background: #f8f9fa; padding: 10px; border: 1px solid #ddd;">
        <h5>Output File PHP:</h5>
    </div>
    @include('ADMIN.sidebar')
    <script src = "https://monitoring-bbm.my.id/public/ADMIN/js/navigate-function.js"></script>
    <script>
        const executionStatus = {}; 
    
        function toggleExecution(filename) {
            const isRunning = executionStatus[filename] || false;
            const action = isRunning ? 'stop' : 'start';
    
            fetch(`/toggle-file`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ filename, action })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    executionStatus[filename] = !isRunning;
                    const button = document.getElementById(`btn-${filename}`);
                    button.textContent = isRunning ? 'Start' : 'Stop';
                    
                    if (data.output) {
                        const outputArea = document.getElementById('output');
                        outputArea.innerHTML = `<pre>${data.output}</pre>`;
                    }
    
                    console.log(data.message);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error communicating with the server.');
            });
        }
    </script>
</body>
</html>



