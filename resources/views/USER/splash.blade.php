<!DOCTYPE html>
<html>
<head>
    <title>Monitoring BBM</title>
    <link rel="stylesheet" href="https://monitoring-bbm.my.id/public/USER/bootstrap/css/bootstrap.min.css">
    <link rel="icon" href="https://monitoring-bbm.my.id/public/Monitoring-BBM-logo.png" type="image/x-icon">
    <style>
        .image-container {
            position: relative;
        }
        .responsive-img{
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <?php
          echo file_get_contents(public_path('USER/html/splash.html')); 
          //echo $_SERVER['HTTP_USER_AGENT'];
          //phpinfo();
    ?>
    <!--<img class="img-fluid responsive-img" src="{{ asset('slide1.jpg') }}" alt="image">-->
</body>
</html>