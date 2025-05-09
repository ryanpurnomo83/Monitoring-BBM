<!DOCTYPE html>
<html>
<head>
    <title>Monitoring BBM</title>
    <link rel="stylesheet" href="https://monitoring-bbm.my.id/public/USER/bootstrap/css/bootstrap.min.css">
    <link rel="icon" href="https://monitoring-bbm.my.id/public/Monitoring-BBM-logo.png" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <?php
        $url = 'https://monitoring-bbm.my.id';
    
        $userId = $user->id;
        $username = $user->name;
        $email = $user->email;
        
        $sidebarContent = file_get_contents(public_path('USER/html/sidebar.html'));
        $navbarContent = file_get_contents(public_path('USER/html/navbar.html'));
        $htmlContent = file_get_contents(public_path('USER/html/products.html'));
        $activecompany = '<p id="toggleBtn" style="margin-top: 20px; font-size: 18px; text-align:center;">' . $user->companyname . '</p>';
        $sidebarContent = str_replace('<!-- Company Name -->', $activecompany, $sidebarContent);
        $htmlContent = str_replace('<!-- List Konten disini -->', $sidebarContent, $htmlContent); 
        $htmlContent = str_replace('<!-- Navbar -->', $navbarContent, $htmlContent);
        $dataRows2 = '';
        
        if ($data->isEmpty()) {
            $dataRows2 = "<br><br><h2 style=\"text-align: center;\">Maaf, tidak ada data yang tersedia.</h2>";
        } else {
            foreach($data as $product){
                $formattedPrice = "Rp " . number_format($product->price, 0, ',', '.');
                $imagePath = rtrim($url, '/') . '/' . ltrim($product->product_image, '/');
                $encodedProduct = htmlspecialchars(json_encode($product, JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');
                
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
                                            <button class=\"add-to-cart\" data-product=\"$encodedProduct\" style=\"padding: 8px 12px; color: white; border: none; border-radius: 4px; cursor: pointer;\">Tambah ke Keranjang</button>
                                            <button class=\"buy-item\" style=\"padding: 8px 12px; color: white; border: none; border-radius: 4px; cursor: pointer;\">Beli Item</button>
                                        </div>
                                    </div>
                              </div>";
            }
        }
        $htmlContent = str_replace('<!-- Products List -->', $dataRows2, $htmlContent);
        echo $htmlContent;
    ?>
    @include('USER.sidebar')
</body>
<script src = "https://monitoring-bbm.my.id/public/USER/js/navigate-function.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    /*
    document.addEventListener("DOMContentLoaded", function () {
        const addToCartButtons = document.querySelectorAll(".add-to-cart");
    
        addToCartButtons.forEach(button => {
            button.addEventListener("click", function () {
                const product = JSON.parse(this.getAttribute("data-product"));
                console.log('Product data:', product);
                
                fetch('/carts', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify({
                        product: product
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Produk berhasil ditambahkan ke keranjang!');
                    } else {
                        alert('Gagal menambahkan produk ke keranjang.');
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    });*/
    
    
    $(document).ready(function() {
        $('.add-to-cart').click(function() {
            
            /*
            var product = JSON.parse(this.getAttribute("data-product"));
            console.log('Product data:', product);
            */
            var productData = $(this).attr('data-product'); 
            var product = JSON.parse(decodeURIComponent(productData));
            var productId = product.id;
    
            var quantity = $('#quantityInput-' + productId).val();
            product.quantity = quantity;
            
            //var product = $(this).data('product'); 
            //console.log('Product data:', product);
            /*
            const quantityInput = document.getElementById(`quantityInput-{$product->id}`);
            const quantity = quantityInput.value;
            console.log(quantity);*/
            
            $.ajax({
                url: '/carts', 
                method: 'POST',
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                data: JSON.stringify({ product: product }), 
                success: function(response) {
                    alert(response.message);
                },
                error: function(xhr, status, error) {
                    console.log('Ajax Error:', xhr.responseText);
                    alert('Gagal menambahkan produk ke keranjang.');
                }
            });
        });
    });
    
</script>
</html>