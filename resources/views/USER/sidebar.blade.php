    <form id="database" method="POST" action="{{ route('database') }}" style="display: none;">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">
    </form>
    
    <form id="monitoring" method="POST" action="{{ route('monitoring') }}" style="display: none;">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">
    </form>
    
    <form id="products" method="POST" action="{{ route('products') }}" style="display: none;">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">
    </form>
    
    <form id="maintenances" method="POST" action="{{ route('maintenances') }}" style="display: none;">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">
    </form>
    
    <form id="history" method="POST" action="{{ route('history') }}" style="display: none;">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">
    </form>
    
    <form id="setting" method="POST" action="{{ route('settings') }}" style="display: none;">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">
    </form>