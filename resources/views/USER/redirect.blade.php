<!-- resources/views/ADMIN/redirectToDashboard.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BBM TBB Monitoring</title>
</head>
<body>
    @if ($condition == 'dashboard')
    <form id="redirectForm" action="{{ route('transportation.dashboard') }}" method="POST">
        @csrf
        <input type="hidden" name="user_id" value="{{ $userId->id }}">
    </form>

    <script type="text/javascript">
        document.getElementById('redirectForm').submit();
    </script>
    @elseif ($condition == 'monitoring')
        <!-- Redirect ke Monitoring -->
        <form id="monitoringForm" method="POST" action="{{ route('monitoring') }}">
            @csrf
            <input type="hidden" name="user_id" value="{{ $user->id }}">
        </form>

        <script type="text/javascript">
            document.getElementById('monitoringForm').submit();
        </script>
    @elseif ($condition == 'database')
        <?php   
            
            /*
            if (strpos($userAgent, 'Windows NT 10.0') !== false && strpos($userAgent, 'Win64') !== false) {
                // Jika user agent mengandung 'Windows NT 10.0' dan 'Win64', blokir akses
                header('HTTP/1.0 403 Forbidden');
                echo "Akses dari Windows 10 (64-bit) diblokir.";
                exit; // Berhenti memproses halaman lebih lanjut
            }else{
                return redirect()->route('database');
            }*/
        ?>
        <form id="redirectForm" action="{{ route('database') }}">
            @csrf
            <input type="hidden" name="user_id" value="{{ $userId->id }}">
        </form>
    @endif
</body>
</html>
