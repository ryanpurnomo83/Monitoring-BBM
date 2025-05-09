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

        $htmlContent = file_get_contents(public_path('ADMIN/html/message.html'));
        $htmlContent = str_replace('<!-- List Konten disini -->', $sidebarContent, $htmlContent); 
        $htmlContent = str_replace('<!-- Navbar -->', $navbarContent, $htmlContent);
        $dataRows2 = '';
        foreach($messages as $msg){
           $dataRows2 .= "<tr class=\"table-row\" onclick=\"window.location.href='/admin/message/detail?msg_id={$msg->id}';\">
                            <td><i class=\"fa fa-user w3-text-blue w3-large\"></i></td>
                            <td><input type='hidden' name=\"msg_id\" value=\"{$msg->id}\"></td>
                            <td>{$msg->id}</td>
                            <td>{$msg->name_sender}</td>
                            <td>{$msg->companyname_sender}</td>
                            <td><i>{$msg->time_sent}</i></td>
                            <td>{$msg->status}</td>
                         </tr>"; 
        }
        $htmlContent = str_replace('<!-- Message Lists -->', $dataRows2, $htmlContent);
        echo $htmlContent;
    ?>
    @include('ADMIN.sidebar')
    <script src = "https://monitoring-bbm.my.id/public/ADMIN/js/navigate-function.js"></script>
</body>
</html>