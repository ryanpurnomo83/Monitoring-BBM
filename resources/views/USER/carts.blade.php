<!DOCTYPE html>
<html>
<head>
    <title>Monitoring BBM</title>
    <link rel="stylesheet" href="https://monitoring-bbm.my.id/public/USER/bootstrap/css/bootstrap.min.css">
    <link rel="icon" href="https://monitoring-bbm.my.id/public/Monitoring-BBM-logo.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@1.0.0"></script>
</head>
<body>
    <?php
        $sidebarContent = file_get_contents(public_path('USER/html/sidebar.html'));
        $navbarContent = file_get_contents(public_path('USER/html/navbar.html'));
        $htmlContent = file_get_contents(public_path('USER/html/carts.html'));
        $htmlContent = str_replace('<!-- List Konten disini -->', $sidebarContent, $htmlContent); 
        $htmlContent = str_replace('<!-- Navbar -->', $navbarContent, $htmlContent); 
        $checkoutList = '';
        $cart = session()->get('cart', []);
        if (!empty($cart)){
            foreach ($cart as $item){

                $formattedPrice = "Rp " . number_format($item['price'], 0, ',', '.');
                $PriceTotal = $item['price'] * $item['quantity']; 
                $formattedPriceTotal = "Rp " . number_format($PriceTotal, 0, ',', '.');
                $checkoutList .= "<tr>
                                    <td>
                                        <img src=\"" . htmlspecialchars($item['product_image']) . "\" width=\"10%\">
                                        " . htmlspecialchars($item['product_name']) . "
                                    </td>
                                    <td class=\"price-per-unit\">" . $formattedPrice . "</td>
                                    <td>
                                        <input type=\"number\" class=\"responsive-input\" value=\"" . $item['quantity'] . "\" min=\"1\" oninput=\"updateTotal(this)\">
                                    </td>
                                    <td class=\"total-price\">" . $formattedPriceTotal . "</td>
                                    <td class=\"process-button\">
                                        <form action=\"checkout\" method=\"POST\">
                                            <input type=\"hidden\" name=\"_token\" value=\"" . csrf_token() . "\">
                                            <input type=\"hidden\" name=\"price_total\" value=\"$PriceTotal\">
                                            <input type=\"hidden\" name=\"product_id\" value=\"" . $item['id'] . "\">
                                            <button type=\"submit\" class=\"buy-button\">Buy</button>
                                        </form>
                                        <br>
                                        <button class=\"delete-from-cart\">Delete</button>
                                    </td>
                                  </tr>
                                  <br>";

                                /*
                                  <div class=\"cart-items\">
                                    <div class=\"cart-item\">
                                        <p>Product ID: " . $item['id'] . "</p>
                                        <p>Product Name: " . $item['product_name'] . "</p>
                                        <p>Product Price: " . $formattedPrice . "</p>
                                        <p>Quantity: " . $item['quantity'] . "</p>
                                    </div>
                                  </div>
                                */        
            }
        }else{
                $checkoutList = "<p>Keranjang kosong.</p>";
        }
        $htmlContent = str_replace('<!-- checkout list -->', $checkoutList, $htmlContent);
        echo $htmlContent;
    ?>
</body>
<script>
    $(document).ready(function(){
    $('.delete-from-cart').click(function(){
      $.ajax({
        url: '/carts', 
        method: 'POST',
        data: {
          _token: '{{ csrf_token() }}'
        },
        success: function(response) {
          alert(response.message);
          location.reload();
        },
        error: function(response) {
          alert('Gagal menghapus keranjang.');
        }
      });
    });
  });
</script>
</html>