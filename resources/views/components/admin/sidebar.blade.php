<style>
    .sidebar {
        width: 250px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        padding: 20px;
    }
    .menu-item {
        padding: 10px 15px;
        border-radius: 4px;
        margin-bottom: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    .menu-item:hover {
        background-color: #f0f0f0;
    }
    .menu-item.active {
        background-color: #e8eaf6;
        color: #303f9f;
        font-weight: bold;
    }
</style>

<div class="sidebar">
    <a href="{{ route('admin.dashboard') }}" style="text-decoration: none; color: inherit;">
        <div class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</div>
    </a>

    <a href="{{ route('admin.users.index') }}" style="text-decoration: none; color: inherit;">
        <div class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">User Management</div>
    </a>

    <a href="{{ route('admin.devices') }}" style="text-decoration: none; color: inherit;">
        <div class="menu-item {{ request()->routeIs('admin.devices') ? 'active' : '' }}">Devices Management</div>
    </a>

    <a href="{{ route('admin.farmers') }}" style="text-decoration: none; color: inherit;">
        <div class="menu-item {{ request()->routeIs('admin.farmers') ? 'active' : '' }}">Farmer Management</div>
    </a>

    <a href="{{ route('admin.roles.index') }}" style="text-decoration: none; color: inherit;">
        <div class="menu-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">Role Management</div>
    </a>

    <a href="{{ route('admin.soil') }}" style="text-decoration: none; color: inherit;">
        <div class="menu-item {{ request()->routeIs('admin.soil*') ? 'active' : '' }}">Soil Management</div>
    </a>

    <a href="{{ route('admin.reports') }}" style="text-decoration: none; color: inherit;">
        <div class="menu-item {{ request()->routeIs('admin.reports') ? 'active' : '' }}">Reports</div>
    </a>

    <a href="{{ route('admin.settings') }}" style="text-decoration: none; color: inherit;">
        <div class="menu-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">System Settings</div>
    </a>
</div>
