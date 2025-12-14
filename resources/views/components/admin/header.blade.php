<style>
    .header {
        background-color: #0ac15e;
        color: white;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .header h1 {
        margin: 0;
        font-size: 1.8rem;
    }
    .user-info {
        display: flex;
        align-items: center;
    }
    .user-info span {
        margin-right: 15px;
    }
    .logout-form {
        display: inline;
    }
    .logout-btn {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        font-size: 1rem;
        padding: 0;
    }
</style>

<div class="header">
    <h1>Smart Crop Rotation Admin!</h1>
    <div class="user-info">
        <span>Welcome, {{ Auth::user()->name }} (System Admin)</span>
        <form class="logout-form" action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</div>
