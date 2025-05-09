<!DOCTYPE html>
<html>
    <head>
    <title>Monitoring BBM</title>
    <link rel="stylesheet" href="https://monitoring-bbm.my.id/public/USER/bootstrap/css/bootstrap.min.css">
    <link rel="icon" href="https://monitoring-bbm.my.id/public/Monitoring-BBM-logo.png" type="image/x-icon">
    </head>
    <body>
        <?php
            $htmlContent = file_get_contents(public_path('USER/html/transportation-data-dashboard.html'));
            $navbarContent = file_get_contents(public_path('USER/html/navbar.html'));
            $formHtml = '<form id="dataForm" action="/transportation/dashboard" method="POST">'.
                        csrf_field().
                        '<input type="hidden" id="nik" name="nik">'.
                        '<input type="hidden" id="userId" name="userId" value="'. $user->id .'">'.
                        '</form>';
            $htmlContent = str_replace('<h1>makan</h1>',$formHtml,$htmlContent);
            $htmlContent = str_replace('<!-- Navbar -->', $navbarContent, $htmlContent);
            echo $htmlContent;
        ?>
    <script src="https://monitoring-bbm.my.id/public/USER/js/transportation-data-dashboard-function.js"></script>
    </body>
</html>