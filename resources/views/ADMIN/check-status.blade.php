<!DOCTYPE html>
<html>
<head>
    <title>Monitoring BBM</title>
    <!-- <link rel="stylesheet" href="{{ asset('ADMIN/css/signup.css') }}"> -->
    <link rel="icon" href="https://monitoring-bbm.my.id/public/Monitoring-BBM-logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://monitoring-bbm.my.id/public/USER/bootstrap/css/bootstrap.min.css">
</head>
<body>
    {!! 
        $data == 0 ? file_get_contents(public_path('ADMIN/html/signup.html')) : file_get_contents(public_path('ADMIN/html/signin.html'));
    !!}
    <script>
        // Menyisipkan token CSRF ke dalam form secara dinamis
        document.addEventListener("DOMContentLoaded", function() {
            var csrfToken = "{{ csrf_token() }}";
            var forms = document.querySelectorAll('form');
            forms.forEach(function(form) {
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', '_token');
                hiddenInput.setAttribute('value', csrfToken);
                form.appendChild(hiddenInput);
            });
        });
    </script>
</body>
</html>