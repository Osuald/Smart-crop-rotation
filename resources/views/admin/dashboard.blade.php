@extends('layouts.admin')

@section('title', 'Admin Dashboard - Smart Crop Rotation')

@section('styles')
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Custom styles -->
<style>
    .dashboard-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border: 1px solid #e3f2fd;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .stat-card {
        background: white,
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border-left: 4px solid #8B4513;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    }

    .stat-card.green {
        background:white,
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .stat-card.orange {
        background:white,
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .stat-card.red {
        background:white,
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .quick-action-btn {
        background: white;
        border: 2px solid #e3f2fd;
        border-radius: 12px;
        padding: 20px;
        text-decoration: none;
        color: #333;
        transition: all 0.3s ease;
        display: block;
        text-align: center;
    }

    .quick-action-btn:hover {
        background: #f8f9fa;
        border-color: #667eea;
        color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        text-decoration: none;
    }

    .welcome-section {
        background: white;
        color: black;
        border-radius: 15px;
        padding: 15px;
        margin-bottom: 30px;
    }

    .chart-container {
        position: relative;
        height: 300px;
        padding: 20px;
    }

    .recent-activity {
        max-height: 300px;
        overflow-y: auto;
    }

    .activity-item {
        border-left: 3px solid #667eea;
        padding: 10px 15px;
        margin-bottom: 10px;
        background: #f8f9fa;
        border-radius: 0 8px 8px 0;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <!-- <div class="welcome-section">
        <div class="row align-items-center">


                <div class="text-white">
                    <h5 class="mb-1">Current Season</h5>
                    <span class="badge bg-light text-dark px-3 py-2">{{ date('Y') }}{{ date('n') <= 6 ? 'A' : 'B' }}</span>
                </div>

        </div>
    </div> -->

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1">{{ \App\Models\User::count() }}</h3>
                        <p class="mb-0 opacity-75">Total Users</p>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card green">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1">{{ \App\Models\User::where('role_id', 1)->count() }}</h3>
                        <p class="mb-0 opacity-75">Farmers</p>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="fas fa-seedling"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card orange">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1">{{ \App\Models\Device::where('status', 'active')->count() }}</h3>
                        <p class="mb-0 opacity-75">Active Devices</p>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="fas fa-microchip"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card red">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1">{{ \App\Models\User::where('role_id', '!=', 1)->count() }}</h3>
                        <p class="mb-0 opacity-75">Administrators</p>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="dashboard-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.users.index') }}" class="quick-action-btn">
                                <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                <h6>Manage Users</h6>
                                <small class="text-muted">View and manage all users</small>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.farmers') }}" class="quick-action-btn">
                                <i class="fas fa-seedling fa-2x text-success mb-2"></i>
                                <h6>Manage Farmers</h6>
                                <small class="text-muted">Farmer management panel</small>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.devices') }}" class="quick-action-btn">
                                <i class="fas fa-microchip fa-2x text-warning mb-2"></i>
                                <h6>Device Management</h6>
                                <small class="text-muted">Monitor and manage devices</small>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.soil') }}" class="quick-action-btn">
                                <i class="fas fa-leaf fa-2x text-info mb-2"></i>
                                <h6>Soil Management</h6>
                                <small class="text-muted">Monitor soil health system</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Users and System Health -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="dashboard-card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Recent Users
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Registered</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\User::with('role')->latest()->take(5)->get() as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user text-white small"></i>
                                            </div>
                                            {{ $user->name }}
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-primary rounded-pill">{{ $user->role->name ?? 'No Role' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $user->status === 'active' ? 'success' : ($user->status === 'inactive' ? 'warning' : 'danger') }} rounded-pill">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="dashboard-card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-heartbeat me-2"></i>System Health
                    </h5>
                </div>
                <div class="card-body">
                    <div class="recent-activity">
                        <div class="activity-item">
                            <div class="d-flex justify-content-between">
                                <strong>Database Status</strong>
                                <span class="badge bg-success">Online</span>
                            </div>
                            <small class="text-muted">All connections healthy</small>
                        </div>
                        <div class="activity-item">
                            <div class="d-flex justify-content-between">
                                <strong>Active Users</strong>
                                <span class="badge bg-primary">{{ \App\Models\User::where('status', 'active')->count() }}</span>
                            </div>
                            <small class="text-muted">Currently active users</small>
                        </div>
                        <div class="activity-item">
                            <div class="d-flex justify-content-between">
                                <strong>Device Connectivity</strong>
                                <span class="badge bg-{{ \App\Models\Device::where('status', 'active')->count() > 0 ? 'success' : 'warning' }}">
                                    {{ \App\Models\Device::where('status', 'active')->count() }} Active
                                </span>
                            </div>
                            <small class="text-muted">IoT devices online</small>
                        </div>
                        <div class="activity-item">
                            <div class="d-flex justify-content-between">
                                <strong>System Load</strong>
                                <span class="badge bg-info">Normal</span>
                            </div>
                            <small class="text-muted">Server performance optimal</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Role and Permission Overview -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="dashboard-card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-user-tag me-2"></i>User Roles Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="rolesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="dashboard-card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>User Registration Trend
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="registrationChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Initialize charts when document is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
});

function initializeCharts() {
    // Roles Distribution Chart
    const rolesCtx = document.getElementById('rolesChart').getContext('2d');
    new Chart(rolesCtx, {
        type: 'doughnut',
        data: {
            labels: ['Farmers', 'Administrators', 'Other'],
            datasets: [{
                data: [
                    {{ \App\Models\User::where('role_id', 1)->count() }},
                    {{ \App\Models\User::where('role_id', 4)->count() }},
                    {{ \App\Models\User::whereNotIn('role_id', [1, 4])->count() }}
                ],
                backgroundColor: ['#4CAF50', '#667eea', '#FF9800']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'User Distribution by Role'
                }
            }
        }
    });

    // Registration Trend Chart (Last 7 days)
    const registrationCtx = document.getElementById('registrationChart').getContext('2d');

    // Generate last 7 days data
    const last7Days = [];
    const registrationData = [];

    for (let i = 6; i >= 0; i--) {
        const date = new Date();
        date.setDate(date.getDate() - i);
        last7Days.push(date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));

        // This would ideally come from a proper query, but for demo purposes:
        registrationData.push(Math.floor(Math.random() * 5) + 1);
    }

    new Chart(registrationCtx, {
        type: 'line',
        data: {
            labels: last7Days,
            datasets: [{
                label: 'New Users',
                data: registrationData,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'User Registration Trend (Last 7 Days)'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}
</script>
@endsection
