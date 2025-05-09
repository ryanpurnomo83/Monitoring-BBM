<!DOCTYPE html>
<html>
<head>
    <title>Monitoring BBM</title>
    <link rel="stylesheet" href="https://monitoring-bbm.my.id/public/USER/bootstrap/css/bootstrap.min.css">
    <link rel="icon" href="https://monitoring-bbm.my.id/public/Monitoring-BBM-logo.png" type="image/x-icon">
</head>
<body>
    {!! 
        $data == 0 ? file_get_contents(public_path('USER/html/signup.html')) : file_get_contents(public_path('USER/html/signin.html'));
    !!}
    @include('USER.modal');
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                });
            }
        });

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