<!DOCTYPE html>
<html>
<head>
    <title>Monitoring BBM</title>
    <link rel="stylesheet" href="https://monitoring-bbm.my.id/public/USER/bootstrap/css/bootstrap.min.css">
    <link rel="icon" href="https://monitoring-bbm.my.id/public/Monitoring-BBM-logo.png" type="image/x-icon">
</head>
<body>
    <?php 
        $companyname = $user->companyname;
        $username = $user->name;
        $email = $user->email;
        $userId = $user->id;
        $sidebarContent = file_get_contents(public_path('USER/html/sidebar.html'));
        $navbarContent = file_get_contents(public_path('USER/html/navbar.html'));
        $htmlContent = file_get_contents(public_path('USER/html/settings.html')); 
        $inputElement1 = '<input type="text" class="form-control" id="companyname" placeholder="Enter your Companyname" name="companyname" value="' . htmlspecialchars($companyname, ENT_QUOTES, 'UTF-8') . '">';
        $inputElement2 = '<input type="text" class="form-control" id="username" placeholder="Enter your Username" name="username" value="' . htmlspecialchars($username, ENT_QUOTES, 'UTF-8') . '">';
        $inputElement3 = '<input type="text" class="form-control" id="email" placeholder="Enter your Email" name="email" value="' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '">';
        $inputElement4 = '<input type="hidden" name="user_id" value="' . htmlspecialchars($user->id, ENT_QUOTES, 'UTF-8') . '">';
        $htmlContent = str_replace('<!--companyname-->', $inputElement1, $htmlContent);
        $htmlContent = str_replace('<!--username-->', $inputElement2, $htmlContent);
        $htmlContent = str_replace('<!--email-->', $inputElement3, $htmlContent);
        $htmlContent = str_replace('<!--userid-->', $inputElement4, $htmlContent);
        $csrfToken = csrf_field();
        $htmlContent = str_replace('<form method="POST" action="/settings">', '<form method="POST" action="/settings">' . $csrfToken, $htmlContent);
        $activecompany = '<p id="toggleBtn" style="margin-top: 20px; font-size: 18px; text-align:center;">' . $user->companyname . '</p>';
        $sidebarContent = str_replace('<!-- Company Name -->', $activecompany, $sidebarContent);
        $htmlContent = str_replace('<!-- List Konten disini -->', $sidebarContent, $htmlContent);
        $htmlContent = str_replace('<!-- Navbar -->', $navbarContent, $htmlContent);
        echo $htmlContent;
    ?>
    @include('USER.sidebar')
</body>
<script src = "https://monitoring-bbm.my.id/public/USER/js/navigate-function.js"></script>
</html>