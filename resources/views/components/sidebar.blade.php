<!-- <div class="sidebar">
    <div class="menu-item {{ request()->is('farmer/dashboard') ? 'active' : '' }}">
        <i class="fa fa-tachometer-alt"></i> Dashboard
    </div>

    <a href="{{ route('farmer.soil') }}" style="text-decoration: none; color: inherit;">
        <div class="menu-item {{ request()->routeIs('farmer.soil*') ? 'active' : '' }}">
            <i class="fas fa-seedling"></i> Soil Management
        </div>
    </a>

    <div class="menu-item {{ request()->is('report*') ? '' : '' }}">
        <i class="fa fa-file-alt"></i> Report
    </div>
</div> -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Farmer Dashboard - Smart Crop Rotation')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #2c5530 0%, #4a7c4f 100%);
            color: white;
            transition: all 0.3s;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            margin: 2px 0;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }

        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
        }

        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .navbar-brand {
            font-weight: 700;
            color: #2c5530 !important;
        }

        .navbar {
            background: white !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .user-info {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            background: #4a7c4f;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }

        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 12px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2c5530 0%, #4a7c4f 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1e3a21 0%, #3a6440 100%);
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar p-3" style="width: 280px;">
            <!-- User Info -->
            <div class="user-info d-flex align-items-center">
                <div class="user-avatar">
                    <i class="fas fa-user fa-lg"></i>
                </div>
                <div>
                    <h6 class="mb-0">{{ Auth::user()->name ?? Auth::user()->username }}</h6>
                    <small class="text-white-50">{{ Auth::user()->role->name ?? 'Farmer' }}</small>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="nav flex-column">
                <a href="{{ route('farmer.dashboard') }}" class="nav-link {{ request()->routeIs('farmer.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>Dashboard
                </a>

                <a href="{{ route('farmer.soil') }}" class="nav-link {{ request()->routeIs('farmer.soil*') ? 'active' : '' }}">
                    <i class="fas fa-seedling"></i>Soil Management
                </a>

                <a href="#" class="nav-link">
                    <i class="fas fa-chart-bar"></i>My Reports
                </a>

                <a href="#" class="nav-link">
                    <i class="fas fa-map-marker-alt"></i>My Farms
                </a>

                <a href="#" class="nav-link">
                    <i class="fas fa-microchip"></i>My Devices
                </a>

                <a href="#" class="nav-link">
                    <i class="fas fa-lightbulb"></i>Recommendations
                </a>

                <a href="#" class="nav-link">
                    <i class="fas fa-cog"></i>Settings
                </a>

                <hr class="my-3">

                <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content flex-grow-1">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white px-4">
                <div class="container-fluid">
                    <h4 class="navbar-brand mb-0">@yield('title', 'Farmer Dashboard')</h4>

                    <div class="d-flex align-items-center">
                        <div class="dropdown">
                            <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle fa-lg"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Content Area -->
            <div class="p-4">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
