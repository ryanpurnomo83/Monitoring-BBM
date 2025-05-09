    <form id="file-manager" method="POST" action="{{ route('admin.file-manager') }}" style="display: none;">
        @csrf
        <input type="hidden" name="admin_id" value="{{ $admin->id }}">
    </form>
    
    <form id="products-manager" method="POST" action="{{ route('admin.products-manager') }}" style="display: none;">
        @csrf
        <input type="hidden" name="admin_id" value="{{ $admin->id }}">
    </form>
    
    <form id="admin-maintenances" method="POST" action="{{ route('admin.maintenances') }}" style="display: none;">
        @csrf
        <input type="hidden" name="admin_id" value="{{ $admin->id }}">
    </form>
    
    <form id="history-tracker" method="POST" action="{{ route('admin.history-tracker') }}" style="display: none;">
        @csrf
        <input type="hidden" name="admin_id" value="{{ $admin->id }}">
    </form>
    
    <form id="admin-setting" method="POST" action="{{ route('admin.settings') }}" style="display: none;">
        @csrf
        <input type="hidden" name="admin_id" value="{{ $admin->id }}">
    </form>