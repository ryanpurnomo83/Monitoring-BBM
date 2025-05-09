<!DOCTYPE html>
<html>
<head>
    <title>Monitoring BBM</title>
    <link rel="stylesheet" href="https://monitoring-bbm.my.id/public/USER/bootstrap/css/bootstrap.min.css">
    <link rel="icon" href="https://monitoring-bbm.my.id/public/Monitoring-BBM-logo.png" type="image/x-icon">
</head>
<body>
    <?php
        $navbarContent = file_get_contents(public_path('ADMIN/html/navbar.html'));
        $htmlContent = file_get_contents(public_path('ADMIN/html/file-content.html'));
        $htmlContent = str_replace('<!-- Navbar -->', $navbarContent, $htmlContent);
        $dataRows = '';
        $dataRows = '<h1>File: ' . htmlspecialchars($filename) . '</h1>
                     <form action="' . route('admin.file-save', ['filename' => $filename]) . '" method="POST">
                         ' . csrf_field() . '
                         <textarea name="content" style="width: 100%; height: 600px;">' . htmlspecialchars($content) . '</textarea>
                         <button type="submit" class="btn btn-success">Save</button>
                         <a href="' . route('admin.file-manager') . '" class="btn btn-primary">Back to File Manager</a>
                     </form>';
        $htmlContent = str_replace('<!-- File Content -->', "{$dataRows}", $htmlContent);
        echo $htmlContent;
    ?>
    
</body>