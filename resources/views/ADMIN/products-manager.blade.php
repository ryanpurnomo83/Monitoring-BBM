<!DOCTYPE html>
<html>
<head>
    <title>Monitoring BBM</title>
    <link rel="stylesheet" href="https://monitoring-bbm.my.id/public/USER/bootstrap/css/bootstrap.min.css">
    <link rel="icon" href="https://monitoring-bbm.my.id/public/Monitoring-BBM-logo.png" type="image/x-icon">
</head>
<body>
    <?php
        //echo $admin;
        $url = 'https://monitoring-bbm.my.id';

        $sidebarContent = file_get_contents(public_path('ADMIN/html/sidebar.html'));
        $navbarContent = file_get_contents(public_path('ADMIN/html/navbar.html'));
        $htmlContent = file_get_contents(public_path('ADMIN/html/products-manager.html'));
        $htmlContent = str_replace('<!-- List Konten disini -->', $sidebarContent, $htmlContent); 
        $htmlContent = str_replace('<!-- Navbar -->', $navbarContent, $htmlContent);
        $dataRows = '';
        $dataRows2 = '';
        $dataRows = '
                    <form action="/admin/add-products" method="POST" style="display: inline;">
                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                        <input type="hidden" name="admin_id" value="' . $admin->id . '">
                        <button type="submit" class="cart" style="border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer;">
                            Tambah Produk
                        </button>
                    </form>';
        if ($data->isEmpty()) {
            $dataRows2 = "<br><br><h2 style=\"text-align: center;\">Maaf, tidak ada data yang tersedia.</h2>";
        } else {
            foreach($data as $product){
                $formattedPrice = "Rp " . number_format($product->price, 0, ',', '.');
                $imagePath = rtrim($url, '/') . '/' . ltrim($product->product_image, '/');
                $dataRows2 .= "<hr>
                              <div class=\"item\" style=\"display: flex; flex-wrap: wrap; border: 1px solid #ccc; border-radius: 8px; padding: 16px; margin-bottom: 16px; width: 100%;\">
                                    <div class=\"product\" style=\"flex: 1; text-align: center;\">
                                        <img src=\"$imagePath\" alt=\"Sensor Fuel Level\" style=\"max-width: 100%; border-radius: 8px;\">
                                    </div>
                                    <div class=\"information\" style=\"flex: 2; padding-left: 16px;\">
                                        <span style=\"display: block; font-weight: bold; margin-bottom: 8px;\">Product: $product->product_name</span>
                                        <span style=\"display: block; color: #28a745; font-weight: bold; margin-bottom: 8px;\">Price: $formattedPrice</span>
                                        <span style=\"display: block; color: #555; margin-bottom: 16px;\">Stock: $product->quantity</span>
                                        <div class=\"button-group\" style=\"display: flex; gap: 8px;\">
                                            <input type=\"number\" class=\"responsive-input\" value=\"1\" min=\"1\" id=\"quantityInput-{$product->id}\" style=\"width: 60px; padding: 4px; border: 1px solid #ccc; border-radius: 4px;\">
                                            <form action=\"/admin/edit-products\" method=\"POST\" style=\"display: inline;\">
                                                <input type=\"hidden\" value=\"$product->id\" name=\"product_id\">
                                                <input type=\"hidden\" value=\"$admin->id\" name=\"admin_id\">
                                                <input type=\"hidden\" name=\"_token\" value=\"" . csrf_token() . "\">
                                                <button type=\"submit\" class=\"cart\" style=\"border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer;\">
                                                    Edit Produk
                                                </button>
                                            </form>
                                            <button class=\"remove-item\">Hapus Produk</button>
                                        </div>
                                    </div>
                              </div>";
            }
        }
        $htmlContent = str_replace('<!-- add product button -->', $dataRows, $htmlContent);
        $htmlContent = str_replace('<!-- Products List -->', $dataRows2, $htmlContent);
        echo $htmlContent;
    ?>
    @include('ADMIN.sidebar')
    <!--
        <input style=\"margin-left:10%;\" type=\"text\" value=\"$product->product_name\">
        <input class=\"product-price\" style=\"margin-left: 5%;\" type=\"text\" value=\"$formattedPrice\">
        <button class=\"save-item\">Simpan Item</button>
        <span style=\"margin-left:10%;\">$product->product_name</span>
        <hr>
                              <div class=\"item\">
                                    <div class=\"product\">
                                        <img src=\"$imagePath\" alt=\"Sensor Fuel Level\">
                                    </div>
                                    <div class=\"information\">
                                        <span style=\"margin-left:10%;\">$product->product_name</span>
                                        <span class=\"product-price\" style=\"margin-left: 5%;\">$formattedPrice</span>
                                        <div class=\"button-group\" style=\"margin-left:10%\">
                                            <input type=\"number\" class=\"responsive-input\" value=\"$product->quantity\" min=\"1\">
                                            <form action=\"/admin/edit-products\" method=\"POST\" style=\"display: inline;\">
                                                <input type=\"hidden\" value=\"$product->id\" name=\"product_id\">
                                                <input type=\"hidden\" value=\"$admin->id\" name=\"admin_id\">
                                                <input type=\"hidden\" name=\"_token\" value=\"" . csrf_token() . "\">
                                                <button type=\"submit\" class=\"cart\" style=\"border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer;\">
                                                    Edit Produk
                                                </button>
                                            </form>
                                            <button class=\"remove-item\">Hapus Produk</button>
                                        </div>
                                    </div>
                              </div>"
    -->
</body>
<script src = "https://monitoring-bbm.my.id/public/ADMIN/js/navigate-function.js"></script>
</html>