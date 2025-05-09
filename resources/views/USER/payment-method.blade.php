<!DOCTYPE html>
<html>
<head>
    <title>Monitoring BBM</title>
    <link rel="stylesheet" href="https://monitoring-bbm.my.id/public/USER/bootstrap/css/bootstrap.min.css">
    <link rel="icon" href="https://monitoring-bbm.my.id/public/Monitoring-BBM-logo.png" type="image/x-icon">
    <style>
        /* Styling untuk memastikan iframe terintegrasi dengan halaman */
        #payment-container {
            margin-top: 50px;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        iframe {
            width: 100%;
            height: 600px;
            border: none;
        }
    </style>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
</head>
<body>
    <?php
        $navbarContent = file_get_contents(public_path('USER/html/navbar.html'));
        $htmlContent = file_get_contents(public_path('USER/html/payment-method.html'));
        $htmlContent = str_replace('<!-- Navbar -->', $navbarContent, $htmlContent);
        echo $htmlContent;
    ?>
    
    <div class="container mt-5">
        <h1 class="mb-4">Pilih Metode Pembayaran</h1>
        @if(isset($redirectUrl))
            <div id="payment-container">
                <h4>Metode Pembayaran</h4>
                <!--<iframe id="payment-iframe"></iframe>-->
                <!--<iframe id="payment-iframe" src="https://app.sandbox.midtrans.com/snap/v1/payment-links/{{ $snapToken }}" allow="payment"></iframe>-->
                <!--<iframe id="payment-iframe" src="https://app.sandbox.midtrans.com/snap/v1/payment-links/{{ $snapToken }}" allow="payment"></iframe>-->
                <iframe id="payment-iframe" src="{{ $redirectUrl }}" allow="payment"></iframe>
            </div>
        @else
            <div class="alert alert-danger">
                Tidak dapat memuat metode pembayaran. Silakan coba lagi nanti.
            </div>
        @endif
    </div>
    
    <script>
    /*
        @if(isset($snapToken))
        snap.pay("{{ $snapToken }}", {
            onSuccess: function(result) {
                alert("Pembayaran berhasil!");
            },
            onPending: function(result) {
                alert("Pembayaran Anda dalam status pending.");
                console.log(result);
            },
            onError: function(result) {
                alert("Terjadi kesalahan dalam proses pembayaran.");
                console.log(result);
            },
            onClose: function() {
                alert("Anda menutup halaman pembayaran tanpa menyelesaikan transaksi.");
            }
        });
        @endif
        
        function selectPayment(paymentType) {
            alert('Metode pembayaran yang dipilih: ' + paymentType);
            // Anda dapat mengarahkan pengguna ke langkah selanjutnya di sini
        }
        */
        document.getElementById('payment-iframe').addEventListener('load', function() {
            const iframe = document.getElementById('payment-iframe');
            iframe.contentWindow.postMessage('requestSize', '*');
            //console.log(iframe);
            window.addEventListener('message', function(event) {
                if (event.data.type === 'setHeight' && event.data.height) {
                    iframe.style.height = event.data.height + 'px';
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
