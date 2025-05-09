<!DOCTYPE html>
<html>
<head>
    <title>Monitoring BBM</title>
    <link rel="stylesheet" href="https://monitoring-bbm.my.id/public/USER/bootstrap/css/bootstrap.min.css">
    <link rel="icon" href="https://monitoring-bbm.my.id/public/Monitoring-BBM-logo.png" type="image/x-icon">
</head>
<body>
    <?php
        $sidebarContent = file_get_contents(public_path('USER/html/sidebar.html'));
        $navbarContent = file_get_contents(public_path('USER/html/navbar.html'));
        $htmlContent = file_get_contents(public_path('USER/html/request-maintenance.html'));
        $htmlContent = str_replace('<!-- List Konten disini -->', $sidebarContent, $htmlContent); 
        $htmlContent = str_replace('<!-- Navbar -->', $navbarContent, $htmlContent);
        //echo $user;
        $dataRows = '';
        $dataRows2 = '';
        $dataRows .= "<form action=\"/maintenances/request\" method=\"POST\" enctype=\"multipart/form-data\">
                            <input type=\"hidden\" name=\"_token\" value=\"" . csrf_token() . "\">
                            <input type=\"hidden\" name=\"user_id\" value=\"{$user->id}\">
                            <h6><input type=\"text\" placeholder=\"Enter Title\" name=\"title\"></h6>
                            <p><textarea placeholder=\"Enter Description\" name=\"description\" style=\"width:100%; height:250px;\"></textarea></p>
                            <p><input type=\"text\" placeholder=\"Additional Notes\" name=\"note\"></p>
                            <p>Attached Files</p>
                            <input type=\"file\" name=\"picture\">
                            <button type=\"submit\">Submit</button>
                      </form>";
                      
        if(!empty($messages)){
            foreach($messages as $msg){
            $dataRows2 .= "<tr class=\"table-row\" onclick=\"window.location.href='/maintenances/request/detail?msg_id={$msg->id}';\">
                            <td><i class=\"fa fa-user w3-text-blue w3-large\"></i></td>
                            <input type='hidden' name=\"msg_id\" value=\"{$msg->id}\">
                            <td>{$msg->id}</td>
                            <td>{$msg->name_sender}</td>
                            <td>{$msg->companyname_sender}</td>
                            <td><i>{$msg->time_sent}</i></td>
                            <td>{$msg->status}</td>
                           </tr>"; 
        }
        }else{
            $dataRows2 = "<tr>
                    <td colspan=\"7\" style=\"text-align: center; color: red; font-weight: bold;\">Maaf, belum ada permintaan perbaikan yang kamu tulis</td>
                 </tr>";
        }
        
        $htmlContent = str_replace('<!-- Messages -->', $dataRows, $htmlContent);
        $htmlContent = str_replace('<!-- Message Lists -->', $dataRows2, $htmlContent);
        echo $htmlContent; 
    ?>
</body>
</html>