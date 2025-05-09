<style>
    /* Animasi fade-in */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.5);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* Animasi fade-out */
    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: scale(1);
        }
        to {
            opacity: 0;
            transform: scale(0.8);
        }
    }

    /* Tambahkan efek fade-in pada modal */
    .modal.fade-in {
        animation: fadeIn 0.5s ease-out;
    }

    /* Tambahkan efek fade-out pada modal */
    .modal.fade-out {
        animation: fadeOut 0.5s ease-in;
    }

    /* Pastikan modal tetap di posisi tengah */
    .modal {
        display: block; /* Tampilkan secara eksplisit */
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1050;
    }
</style>

@if(session('status'))
<div id="statusModal" class="modal" tabindex="-1" role="dialog" style="display: block;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="margin:auto;">
                <h5 class="modal-title">
                    {{ session('status') === 'success' ? 'Success' : 'Error' }}
                </h5>
            </div>
            <div class="modal-body text-center">
                <p style="text-align:center;">{{ session('message') }}</p>
                @if(session('status') === 'success')
                    <img src="https://monitoring-bbm.my.id/public/success.png" alt="Success" style="width: 100%; height: auto;">
                @elseif(session('status') === 'error')
                    <img src="https://monitoring-bbm.my.id/public/error.png" alt="Error" style="width: 100%; height: auto;">
                @endif
            </div>
            <div class="modal-footer">
                @if(session('status') === 'success')
                    <button hidden id="redirectButton" type="button" class="btn btn-primary">Go to Dashboard</button>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('statusModal');
        if (modal) {
            // Auto-close modal after 3 seconds
            setTimeout(() => {
                modal.style.display = 'none';
                @if(session('status') === 'success')
                window.location.href = "/dashboard";
                @endif
            }, 3000);

            // Redirect button functionality
            const redirectButton = document.getElementById('redirectButton');
            if (redirectButton) {
                redirectButton.addEventListener('click', function () {
                    window.location.href = "/dashboard";
                });
            }
        }
    });
</script>
