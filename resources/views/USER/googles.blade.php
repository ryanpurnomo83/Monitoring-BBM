<!DOCTYPE HTML>
<html>
    <head>
        <title>Monitoring BBM</title>
        <link rel="stylesheet" href="https://monitoring-bbm.my.id/public/USER/bootstrap/css/bootstrap.min.css">
        <link rel="icon" href="https://monitoring-bbm.my.id/public/Monitoring-BBM-logo.png" type="image/x-icon">
        <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@1.0.0"></script>
    </head>
    <body>
        <?php
            $sidebarContent = file_get_contents(public_path('USER/html/googles-sidebar.html'));
            $navbarContent = file_get_contents(public_path('USER/html/navbar.html'));
            $htmlContent = file_get_contents(public_path('USER/html/googles.html'));
            $htmlContent = str_replace('<!-- List Konten disini -->', $sidebarContent, $htmlContent); 
            $htmlContent = str_replace('<!-- Navbar -->', $navbarContent, $htmlContent); 
            
            echo $htmlContent;
        ?>
    </body>
</html>