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
    $htmlContent = file_get_contents(public_path('ADMIN/html/add-products.html'));
    $htmlContent = str_replace('<!-- Navbar -->', $navbarContent, $htmlContent); 
    $dataRows = '';
    $csrfField = '<input type="hidden" name="_token" value="' . csrf_token() . '">';
    $dataRows = "
        <form id=\"addProductForm\" class=\"modal-content\" method=\"POST\" action=\"/admin/save-product\" enctype=\"multipart/form-data\" novalidate>
            {$csrfField}
            <div class=\"container\">
                <h1>Tambah Produk</h1>
                <hr>
                <input type=\"hidden\" name=\"admin_id\" value=\"$admin->id\">
                <div class=\"form-group\">
                    <label for=\"productName\"><b>Nama Produk</b></label>
                    <input type=\"text\" class=\"form-control\" placeholder=\"Masukkan Nama Produk\" name=\"productName\" required>
                </div>
                <div class=\"form-group\">
                    <label for=\"productDescription\"><b>Deskripsi Produk</b></label>
                    <input type=\"text\" class=\"form-control\" placeholder=\"Masukkan Deskripsi Produk\" name=\"productDescription\" required>
                </div>
                <div class=\"form-group\">
                    <label for=\"productPrice\"><b>Harga Produk</b></label>
                    <input type=\"text\" class=\"form-control\" placeholder=\"Masukkan Harga Produk\" name=\"productPrice\" required>
                </div>
                <div class=\"form-group\">
                    <label for=\"productQuantity\"><b>Kuantitas Produk</b></label>
                    <input type=\"number\" class=\"form-control\" placeholder=\"Masukkan Jumlah Produk\" name=\"productQuantity\" required>
                </div>
                <div class=\"form-group\">
                    <label for=\"productImages\"><b>Gambar Produk</b></label>
                    <input type=\"file\" class=\"form-control\" placeholder=\"Masukkan Gambar Produk\" name=\"productImages\" required>
                </div>
                <br>
                <div class=\"clearfix\">
                    <button type=\"submit\" class=\"btn btn-primary signupbtn\">Submit</button>
                    <br><br>
                    <button type=\"button\" onclick=\"window.location.href='" . url('/admin/produk') . "'\" class=\"btn btn-danger cancelbtn\">Cancel</button>
                </div>
            </div>
        </form>
    ";
    $htmlContent = str_replace('<!-- add product form -->', $dataRows, $htmlContent);
    echo $htmlContent;
?>
</body>