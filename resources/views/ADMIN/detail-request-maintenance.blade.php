<!DOCTYPE html>
<html>
<head>
    <title>Monitoring BBM</title>
    <link rel="stylesheet" href="https://monitoring-bbm.my.id/public/ADMIN/bootstrap/css/bootstrap.min.css">
    <link rel="icon" href="https://monitoring-bbm.my.id/public/Monitoring-BBM-logo.png" type="image/x-icon">
</head>
<body>
    <?php
        $url = 'https://monitoring-bbm.my.id';
    
        $navbarContent = file_get_contents(public_path('ADMIN/html/navbar.html'));
        $htmlContent = file_get_contents(public_path('ADMIN/html/detail-request-maintenance.html'));
        $htmlContent = str_replace('<!-- Navbar -->', $navbarContent, $htmlContent);
        $dataRows = '';
        foreach($messages as $msg){
            $splitMessages = explode('<br>', $msg->messages);
            $title = $splitMessages[0];
            $Messages = isset($splitMessages[1]) ? $splitMessages[1] : '';
            $additionalMessages = count($splitMessages) > 2 ? implode('<br>', array_slice($splitMessages, 2)) : ''; 
            $imagePath = rtrim($url, '/') . '/' . ltrim($msg->picture, '/');
            $dataRows .= "<form style=\"margin:auto;\" action=\"/maintenances/request\" method=\"POST\">
                            <h4>$title</h4>
                            <p>$Messages</p>
                            <p>$additionalMessages</p>
                            <p>Attached Files</p>
                            <img src=\"$imagePath\">
                          </form>";
        }
        
        $htmlContent = str_replace('<!-- Messages -->', $dataRows, $htmlContent);
        echo $htmlContent; 
    ?>
</body>
<script src = "https://monitoring-bbm.my.id/public/ADMIN/js/navigate-function.js"></script>
</html>