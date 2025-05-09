<!DOCTYPE html>
<html>
<head>
    <title>Monitoring BBM</title>
    <link rel="stylesheet" href="https://monitoring-bbm.my.id/public/ADMIN/bootstrap/css/bootstrap.min.css">
    <link rel="icon" href="https://monitoring-bbm.my.id/public/Monitoring-BBM-logo.png" type="image/x-icon">
</head>
<body>
    <?php 
        $username = $admin->name;
        $email = $admin->email;
        $adminId = $admin->id;
        $sidebarContent = file_get_contents(public_path('ADMIN/html/sidebar.html'));
        $navbarContent = file_get_contents(public_path('ADMIN/html/navbar.html'));
        $htmlContent = file_get_contents(public_path('ADMIN/html/settings.html')); 
        $inputElement2 = '<input type="text" class="form-control" id="username" placeholder="Enter your Username" name="username" value="' . htmlspecialchars($username, ENT_QUOTES, 'UTF-8') . '">';
        $inputElement3 = '<input type="text" class="form-control" id="email" placeholder="Enter your Email" name="email" value="' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '">';
        $inputElement4 = '<input type="hidden" name="admin_id" value="' . htmlspecialchars($admin->id, ENT_QUOTES, 'UTF-8') . '">';
        $htmlContent = str_replace('<!--username-->', $inputElement2, $htmlContent);
        $htmlContent = str_replace('<!--email-->', $inputElement3, $htmlContent);
        $htmlContent = str_replace('<!--userid-->', $inputElement4, $htmlContent);
        $csrfToken = csrf_field();
        $htmlContent = str_replace('<form method="POST" action="/settings">', '<form method="POST" action="/settings">' . $csrfToken, $htmlContent);
        $htmlContent = str_replace('<!-- List Konten disini -->', $sidebarContent, $htmlContent);
        $htmlContent = str_replace('<!-- Navbar -->', $navbarContent, $htmlContent);
        echo $htmlContent;
    ?>
    @include('ADMIN.sidebar')
</body>
<script src = "https://monitoring-bbm.my.id/public/ADMIN/js/navigate-function.js"></script>
</html>