<!DOCTYPE html>
<html>
<head>
    <title>Monitoring BBM</title>
    <link rel="stylesheet" href="https://monitoring-bbm.my.id/public/ADMIN/bootstrap/css/bootstrap.min.css">
    <link rel="icon" href="https://monitoring-bbm.my.id/public/Monitoring-BBM-logo.png" type="image/x-icon">
</head>
<body>
    <?php
        $userId = $user->id;
        $username = $user->name;
        $email = $user->email;
        
        $sidebarContent = file_get_contents(public_path('USER/html/sidebar.html'));
        $pageContent = file_get_contents(public_path('USER/html/products.html'));
        $pageContent = str_replace('<!-- List Konten disini -->', $sidebarContent, $pageContent); 
        echo $pageContent;
    ?>
    @include('USER.sidebar')
</body>
<script src = "https://monitoring-bbm.my.id/public/ADMIN/js/navigate-function.js"></script>
</html>