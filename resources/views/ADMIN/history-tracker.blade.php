<!DOCTYPE html>
<html>
<head>
    <title>Monitoring BBM</title>
    <link rel="stylesheet" href="{{ asset('https://monitoring-bbm.my.id/public/ADMIN/bootstrap/css/bootstrap.min.css') }}">
    <link rel="icon" href="https://monitoring-bbm.my.id/public/Monitoring-BBM-logo.png" type="image/x-icon">
</head>
<body>
    <?php
        $adminId = $admin->id;
        $adminname = $admin->name;
        $email = $admin->email;
        $historyDeletion = '';
        
        $htmlContent = file_get_contents(public_path('ADMIN/html/history-tracker.html'));
        $sidebarContent = file_get_contents(public_path('ADMIN/html/sidebar.html'));
        $navbarContent = file_get_contents(public_path('ADMIN/html/navbar.html'));
        $pageHistory = session('page_history', []);
        $dataRows = ' ';

        foreach ($pageHistory as $page) {
            $dataRows .= "<tr>";
            $dataRows .= "<td><a href='" . $page . "'>" . htmlspecialchars($page) . "</a></td>";
            $dataRows .= "</tr>";
        }
        $historyDeletion .= 
        '<form style="text-align: center;" id="redirectForm" action="' . route('history.delete') . '" method="POST">
            ' . csrf_field() . '
            <input type="hidden" name="user_id" value="' . $admin->id . '">
            <button class="button" style="border: none;">Delete History</button>
        </form><br><br>';
        $htmlContent = str_replace('<!-- List Konten disini -->', $sidebarContent, $htmlContent);
        $htmlContent = str_replace('<!-- Navbar -->', $navbarContent, $htmlContent); 
        $htmlContent = str_replace('<!-- history delete -->', $historyDeletion, $htmlContent);
        $htmlContent = str_replace('<p>makan</p>', $dataRows, $htmlContent);    
        echo $htmlContent;
    ?>
    @include('ADMIN.sidebar')
</body>
<script src = "https://monitoring-bbm.my.id/public/ADMIN/js/navigate-function.js"></script>
<script src="https://monitoring-bbm.my.id/public/ADMIN/js/history-function.js"></script>
</html>