<title>Monitoring BBM</title>

<?php

    
    $userAgent = $_SERVER['HTTP_USER_AGENT'];

    if (strpos($userAgent, 'Windows NT 10.0') !== false && strpos($userAgent, 'Win64') !== false) {
        header('HTTP/1.0 403 Forbidden');
        echo "Akses dari Windows 10 (64-bit) diblokir.";
        exit;
    }else{
        return redirect()->route('database');
    }

    //phpinfo();
?>