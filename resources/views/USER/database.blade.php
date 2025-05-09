<!DOCTYPE html>
<html>
<title>Monitoring BBM</title>
<link rel="stylesheet" href="https://monitoring-bbm.my.id/public/USER/bootstrap/css/bootstrap.min.css">
<link rel="icon" href="https://monitoring-bbm.my.id/public/Monitoring-BBM-logo.png" type="image/x-icon">
    <body>
        <?php
            $sidebarContent = file_get_contents(public_path('USER/html/sidebar.html'));
            $navbarContent = file_get_contents(public_path('USER/html/navbar.html'));
            $htmlContent = file_get_contents(public_path('USER/html/database.html'));
            $activecompany = '<p id="toggleBtn" style="margin-top: 20px; font-size: 18px; text-align:center;">' . $user->companyname . '</p>';
            $sidebarContent = str_replace('<!-- Company Name -->', $activecompany, $sidebarContent);
            $htmlContent = str_replace('<!-- List Konten disini -->', $sidebarContent, $htmlContent); 
            $htmlContent = str_replace('<!-- Navbar -->', $navbarContent, $htmlContent); 
            echo $htmlContent;
        ?>
        @include('USER.sidebar')
        <!--<script src = "https://monitoring-bbm.my.id/public/USER/js/database-function.js"></script>-->
        <script>
            $(document).ready(function () {
                $('#sqlForm').on('submit', function (e) {
                    e.preventDefault(); 
                    var sqlQuery = $('#sqlQuery').val();
                    var user_id = <?php echo $user->id; ?>;
                    
                    $.ajax({
                        url: '/submit-sql', 
                        method: 'POST',
                        data: {
                            sqlQuery: sqlQuery,
                            user_id: user_id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('tbody').empty();
                            console.log(response.data);
                            if (response.data && response.data.length > 0) {
                                var rows = '';
                                
                                // Iterasi setiap objek dalam response.data dan tampilkan properti tertentu
                                response.data.forEach(function(item) {
                                    // Misalnya, jika setiap item memiliki id, name, dan email
                                    rows += `<tr>
                                                <td>${item.id}</td>
                                                <td>${item.nik}</td>
                                                <td>${item.companyname}</td>
                                                <td>${item.level}</td>
                                                <td>${item.timestamp}</td>
                                              </tr>`;
                                });
                                $('tbody').html(rows);
                            } else if(response.data){
                                if (response.data && Object.values(response.data).length > 0) {
                                    var rows = '';
                                    Object.values(response.data).forEach(function(value) {
                                        rows += `<tr>
                                                    <td>${value}</td>
                                                  </tr>`;
                                    });
                        
                                    $('tbody').html(rows);
                                    
                                }
                            } else{
                                $('tbody').html('<tr><td colspan="4">No data available</td></tr>');
                            }
                            
                            // Menampilkan pesan sukses
                            $('#responseMessage').html('<div class="alert alert-success">' + response.message + '</div>');
                        },
                        error: function (xhr) {
                            $('#responseMessage').html('<div class="alert alert-danger">Error: ' + xhr.responseText + '</div>');
                        }
                    });
                });
            });
        </script>
        <script src = "https://monitoring-bbm.my.id/public/USER/js/navigate-function.js"></script>
    </body>
</html>


