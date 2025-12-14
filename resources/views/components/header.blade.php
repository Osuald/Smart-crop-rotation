<div class="header">
    <h1>Smart Crop Rotation System!</h1>
    <div class="user-info">
        <span>Welcome, {{ Auth::user()->name }} ({{ Auth::user()->role_id == 4 ? 'Admin' : 'Farmer' }})</span>
        <form class="logout-form" action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</div>
