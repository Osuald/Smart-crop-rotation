@extends('layouts.admin')

@section('title', 'Soil Management - Admin Dashboard')

@section('styles')
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Custom styles -->
<style>
    .soil-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9998;
        display: none;
    }

    .soil-dropdown-modal {
        position: fixed;
        bottom: 20px;
        left: 20px;
        width: 350px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        z-index: 9999;
        display: none;
        animation: slideUp 0.3s ease-out;
    }

    @keyframes slideUp {
        from {
            transform: translateY(100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .soil-modal-header {
        background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%);
        color: white;
        padding: 15px 20px;
        border-radius: 12px 12px 0 0;
        display: flex;
        justify-content: between;
        align-items: center;
    }

    .soil-modal-body {
        padding: 20px;
    }

    .soil-option {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        margin-bottom: 8px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
    }

    .soil-option:hover {
        background-color: #f8f9fa;
        border-color: #8B4513;
        transform: translateX(5px);
    }

    .soil-option i {
        margin-right: 12px;
        width: 20px;
        text-align: center;
        color: #8B4513;
    }

    .soil-option-content h6 {
        margin: 0 0 4px 0;
        color: #333;
        font-weight: 600;
    }

    .soil-option-content small {
        color: #666;
        margin: 0;
    }

    .content-section {
        display: none;
    }

    .content-section.active {
        display: block;
    }

    .season-badge {
        background:#0ac15e;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85em;
        font-weight: 600;
    }

    .health-excellent { color: #28a745; }
    .health-good { color: #17a2b8; }
    .health-fair { color: #ffc107; }
    .health-poor { color: #dc3545; }

    .stat-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border-left: 4px solid #8B4513;
    }

    .filter-section {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header text-black d-flex justify-content-between align-items-center" >
                    <h3 class="mb-0">Soil Management Dashboard</h3>
                    <div>
                        <span class="season-badge me-2" id="currentSeason">Loading...</span>
                        <button type="button" class="btn btn-light btn-sm" onclick="showSoilModal()">
                            <i class="fas fa-chart-line me-2"></i>View Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4" id="quickStats">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="mb-1 text-muted">Active Devices</p>
                        <h4 class="mb-0" id="totalDevices">-</h4>
                    </div>
                    <div class="text-primary">
                        <i class="fas fa-microchip fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="mb-1 text-muted">Active Users</p>
                        <h4 class="mb-0" id="activeUsers">-</h4>
                    </div>
                    <div class="text-success">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="mb-1 text-muted">Total Readings</p>
                        <h4 class="mb-0" id="totalReadings">-</h4>
                    </div>
                    <div class="text-info">
                        <i class="fas fa-chart-bar fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Content Sections -->
    <div id="mainContent">
        <!-- Live Data Section -->
        <div id="liveDataSection" class="content-section">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-satellite-dish me-2"></i>Live Soil Data</h5>
                </div>
                <div class="card-body">
                    <div class="filter-section">
                        <div class="row">
                            <div class="col-md-4">
                                <select class="form-select" id="deviceFilter">
                                    <option value="">All Devices</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" id="userFilter">
                                    <option value="">All Users</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary" onclick="loadLiveData()">
                                    <i class="fas fa-refresh me-1"></i>Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="liveDataContent">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading live data...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommendations Section -->
        <div id="recommendationsSection" class="content-section">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Soil Recommendations</h5>
                </div>
                <div class="card-body">
                    <div id="recommendationsContent">
                        <div class="text-center py-4">
                            <div class="spinner-border text-info" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading recommendations...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Section -->
        <div id="historySection" class="content-section">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Soil History</h5>
                </div>
                <div class="card-body">
                    <div class="filter-section">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="startDate">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" id="endDate">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Season</label>
                                <select class="form-select" id="seasonFilter">
                                    <option value="">All Seasons</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <button class="btn btn-primary d-block" onclick="loadHistory()">
                                    <i class="fas fa-search me-1"></i>Search
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="historyContent">
                        <div class="text-center py-4">
                            <div class="spinner-border text-warning" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading history...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics Section -->
        <div id="analyticsSection" class="content-section">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Soil Analytics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="healthChart" width="400" height="200"></canvas>
                        </div>
                        <div class="col-md-6">
                            <canvas id="trendsChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Soil Modal Overlay -->
<div class="soil-modal-overlay" id="soilModalOverlay" onclick="hideSoilModal()"></div>

<!-- Soil Dropdown Modal -->
<div class="soil-dropdown-modal" id="soilDropdownModal">
    <div class="soil-modal-header">
        <h6 class="mb-0"><i class="fas fa-seedling me-2"></i>Soil Data Options</h6>
        <button type="button" class="btn-close btn-close-white" onclick="hideSoilModal()"></button>
    </div>
    <div class="soil-modal-body">
        <div class="soil-option" onclick="showSection('liveData')">
            <i class="fas fa-satellite-dish"></i>
            <div class="soil-option-content">
                <h6>Live/Current Data</h6>
                <small>Real-time soil conditions and measurements</small>
            </div>
        </div>

        <div class="soil-option" onclick="showSection('recommendations')">
            <i class="fas fa-lightbulb"></i>
            <div class="soil-option-content">
                <h6>Recommendations</h6>
                <small>AI-powered crop and treatment suggestions</small>
            </div>
        </div>

        <div class="soil-option" onclick="showSection('history')">
            <i class="fas fa-history"></i>
            <div class="soil-option-content">
                <h6>Soil History</h6>
                <small>Historical data and trends analysis</small>
            </div>
        </div>

        <div class="soil-option" onclick="showSection('analytics')">
            <i class="fas fa-chart-pie"></i>
            <div class="soil-option-content">
                <h6>Analytics & Insights</h6>
                <small>Advanced analytics and data visualization</small>
            </div>
        </div>

        <div class="soil-option" onclick="window.location.href='{{ route('admin.soil.manual-input') }}'">
            <i class="fas fa-edit"></i>
            <div class="soil-option-content">
                <h6>Manual Data Input</h6>
                <small>Enter soil data manually with crop history</small>
            </div>
        </div>


    </div>
</div>
@endsection

@section('scripts')
<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let currentSection = null;
let filters = {};

// Add CSRF token function
function getCSRFToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    if (token) {
        return token.getAttribute('content');
    }
    // Fallback - try to get from Laravel global if available
    if (typeof window.Laravel !== 'undefined' && window.Laravel.csrfToken) {
        return window.Laravel.csrfToken;
    }
    // Final fallback - return empty string
    return '';
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadInitialData();
    loadFilters();
    setDefaultDates();
});

function loadInitialData() {
    // Load quick stats
    fetch('/admin/soil/analytics', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCSRFToken()
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            document.getElementById('totalDevices').textContent = data.stats.total_devices || 0;
            document.getElementById('activeUsers').textContent = data.stats.active_users || 0;
            document.getElementById('totalReadings').textContent = data.stats.total_readings || 0;
            document.getElementById('currentSeason').textContent = data.season || 'Unknown';
        } else {
            console.error('Analytics API returned error:', data.message);
        }
    })
    .catch(error => {
        console.error('Error loading stats:', error);
        // Set default values on error
        document.getElementById('totalDevices').textContent = '0';
        document.getElementById('activeUsers').textContent = '0';
        document.getElementById('totalReadings').textContent = '0';
        document.getElementById('currentSeason').textContent = 'Current Season';
    });
}

function loadFilters() {
    fetch('/admin/soil/filters', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCSRFToken()
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            filters = data;
            populateFilters();
        } else {
            console.error('Filters API returned error:', data.message);
        }
    })
    .catch(error => {
        console.error('Error loading filters:', error);
        // Populate with empty filters on error
        filters = { devices: [], users: [], seasons: ['Season A', 'Season B'] };
        populateFilters();
    });
}

function populateFilters() {
    // Populate device filter
    const deviceSelect = document.getElementById('deviceFilter');
    if (deviceSelect) {
        deviceSelect.innerHTML = '<option value="">All Devices</option>';
        if (filters.devices && filters.devices.length > 0) {
            filters.devices.forEach(device => {
                const option = document.createElement('option');
                option.value = device.id;
                option.textContent = `${device.device_name} (${device.user ? device.user.name : 'Unknown'})`;
                deviceSelect.appendChild(option);
            });
        }
    }

    // Populate user filter
    const userSelect = document.getElementById('userFilter');
    if (userSelect) {
        userSelect.innerHTML = '<option value="">All Users</option>';
        if (filters.users && filters.users.length > 0) {
            filters.users.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = user.name;
                userSelect.appendChild(option);
            });
        }
    }

    // Populate season filter
    const seasonSelect = document.getElementById('seasonFilter');
    if (seasonSelect) {
        seasonSelect.innerHTML = '<option value="">All Seasons</option>';
        if (filters.seasons && filters.seasons.length > 0) {
            filters.seasons.forEach(season => {
                const option = document.createElement('option');
                option.value = season;
                option.textContent = season;
                seasonSelect.appendChild(option);
            });
        }
    }
}

function setDefaultDates() {
    const endDate = new Date();
    const startDate = new Date();
    startDate.setMonth(startDate.getMonth() - 6);

    document.getElementById('startDate').value = startDate.toISOString().split('T')[0];
    document.getElementById('endDate').value = endDate.toISOString().split('T')[0];
}

function showSoilModal() {
    document.getElementById('soilModalOverlay').style.display = 'block';
    document.getElementById('soilDropdownModal').style.display = 'block';
}

function hideSoilModal() {
    document.getElementById('soilModalOverlay').style.display = 'none';
    document.getElementById('soilDropdownModal').style.display = 'none';
}

function showSection(section) {
    hideSoilModal();

    // Hide all sections
    document.querySelectorAll('.content-section').forEach(el => {
        el.classList.remove('active');
    });

    // Show selected section
    const sectionElement = document.getElementById(section + 'Section');
    if (sectionElement) {
        sectionElement.classList.add('active');
        currentSection = section;

        // Load section data
        switch(section) {
            case 'liveData':
                loadLiveData();
                break;
            case 'recommendations':
                loadRecommendations();
                break;
            case 'history':
                loadHistory();
                break;
            case 'analytics':
                loadAnalytics();
                break;
        }
    }
}

function loadLiveData() {
    const deviceId = document.getElementById('deviceFilter').value;
    const userId = document.getElementById('userFilter').value;

    const params = new URLSearchParams();
    if (deviceId) params.append('device_id', deviceId);
    if (userId) params.append('user_id', userId);

    // Show loading state
    document.getElementById('liveDataContent').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Fetching live soil data...</p>
        </div>
    `;

    fetch(`/admin/soil/live-data?${params}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCSRFToken()
        }
    })
    .then(response => {
        console.log('Live data response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Live data received:', data);
        if (data.success) {
            renderLiveData(data.data, data.season);
        } else {
            throw new Error(data.message || 'Failed to load live data');
        }
    })
    .catch(error => {
        console.error('Error loading live data:', error);
        document.getElementById('liveDataContent').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Error loading live data:</strong> ${error.message}
                <br><small>Please check the console for more details.</small>
            </div>
        `;
    });
}

function renderLiveData(data, season) {
    console.log('Rendering live data:', data);

    let html = `
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6>Current Season: <span class="season-badge">${season || 'Unknown'}</span></h6>
            <small class="text-muted">${data ? data.length : 0} readings found</small>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Device</th>
                        <th>User</th>
                        <th>Farm</th>
                        <th>pH Level</th>
                        <th>Moisture</th>
                        <th>Temperature</th>
                        <th>Health Score</th>
                        <th>Recorded</th>
                    </tr>
                </thead>
                <tbody>
    `;

    if (!data || data.length === 0) {
        html += `
            <tr>
                <td colspan="8" class="text-center text-muted py-4">
                    <i class="fas fa-info-circle me-2"></i>
                    No live data available. Click
                    <a href="#" onclick="generateDemoData()" class="btn btn-sm btn-outline-primary ms-1">Generate Demo Data</a>
                    to create sample readings.
                </td>
            </tr>
        `;
    } else {
        data.forEach(item => {
            const health = getHealthStatus(item.soil_health_score);
            html += `
                <tr>
                    <td>
                        <div class="fw-bold">${item.device ? item.device.device_name : 'Unknown'}</div>
                        <small class="text-muted">${item.device ? item.device.device_serial_number : 'N/A'}</small>
                    </td>
                    <td>${item.user ? item.user.name : (item.device && item.device.user ? item.device.user.name : 'Unknown')}</td>
                    <td>${item.farm ? item.farm.name : 'N/A'}</td>
                    <td><span class="badge bg-info">${item.ph_level || 'N/A'}</span></td>
                    <td><span class="badge bg-primary">${item.moisture_level ? item.moisture_level + '%' : 'N/A'}</span></td>
                    <td><span class="badge bg-warning">${item.temperature ? item.temperature + '°C' : 'N/A'}</span></td>
                    <td>
                        <span class="badge bg-${getHealthColor(health)}">${health}</span>
                        <small class="d-block text-muted">${item.soil_health_score || 0}%</small>
                    </td>
                    <td><small>${new Date(item.recorded_at).toLocaleString()}</small></td>
                </tr>
            `;
        });
    }

    html += '</tbody></table></div>';
    document.getElementById('liveDataContent').innerHTML = html;
}

function getHealthStatus(score) {
    if (!score && score !== 0) return 'unknown';

    score = parseFloat(score);
    if (score >= 80) return 'excellent';
    if (score >= 60) return 'good';
    if (score >= 40) return 'fair';
    return 'poor';
}

function getHealthColor(health) {
    switch(health) {
        case 'excellent': return 'success';
        case 'good': return 'info';
        case 'fair': return 'warning';
        case 'poor': return 'danger';
        default: return 'secondary';
    }
}

function loadRecommendations() {
    document.getElementById('recommendationsContent').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-info" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading recommendations...</p>
        </div>
    `;

    fetch('/admin/soil/recommendations', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCSRFToken()
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            renderRecommendations(data.data, data.season);
        } else {
            throw new Error(data.message || 'Failed to load recommendations');
        }
    })
    .catch(error => {
        console.error('Error loading recommendations:', error);
        document.getElementById('recommendationsContent').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Error loading recommendations: ${error.message}
            </div>
        `;
    });
}

function renderRecommendations(data, season) {
    let html = `
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6>Recommendations for Season: <span class="season-badge">${season || 'Unknown'}</span></h6>
            <small class="text-muted">${data ? data.length : 0} recommendations found</small>
        </div>
    `;

    if (!data || data.length === 0) {
        html += `
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                No recommendations available for current season.
                <br><small>Recommendations will be generated automatically as soil data is collected.</small>
            </div>
        `;
    } else {
        data.forEach(item => {
            html += `
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="card-title">
                                    <i class="fas fa-seedling me-2"></i>${item.recommended_crop}
                                    <span class="badge bg-${getPriorityColor(item.priority)} ms-2">${item.priority}</span>
                                </h6>
                                <p class="card-text">${item.recommendation_details}</p>
                                <small class="text-muted">
                                    User: ${item.user ? item.user.name : 'Unknown'} |
                                    Confidence: ${item.confidence_score}% |
                                    Created: ${new Date(item.created_at).toLocaleDateString()}
                                </small>
                            </div>
                            <div class="text-end">
                                <div class="progress" style="width: 100px; height: 6px;">
                                    <div class="progress-bar bg-success" style="width: ${item.confidence_score}%"></div>
                                </div>
                                <small class="text-muted">${item.confidence_score}%</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }

    document.getElementById('recommendationsContent').innerHTML = html;
}

function getPriorityColor(priority) {
    switch(priority) {
        case 'high': return 'danger';
        case 'medium': return 'warning';
        case 'low': return 'info';
        default: return 'secondary';
    }
}

function loadHistory() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const season = document.getElementById('seasonFilter').value;

    const params = new URLSearchParams();
    if (startDate) params.append('start_date', startDate);
    if (endDate) params.append('end_date', endDate);
    if (season) params.append('season', season);

    document.getElementById('historyContent').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-warning" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading historical data...</p>
        </div>
    `;

    fetch(`/admin/soil/history?${params}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCSRFToken()
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            renderHistory(data.data);
        } else {
            throw new Error(data.message || 'Failed to load history');
        }
    })
    .catch(error => {
        console.error('Error loading history:', error);
        document.getElementById('historyContent').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Error loading history: ${error.message}
            </div>
        `;
    });
}

function renderHistory(data) {
    let html = `
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Device</th>
                        <th>User</th>
                        <th>Farm</th>
                        <th>pH</th>
                        <th>Moisture</th>
                        <th>Temperature</th>
                        <th>NPK</th>
                        <th>Health Score</th>
                    </tr>
                </thead>
                <tbody>
    `;

    if (!data || data.length === 0) {
        html += `
            <tr>
                <td colspan="9" class="text-center text-muted py-4">
                    <i class="fas fa-info-circle me-2"></i>
                    No historical data found for selected criteria
                </td>
            </tr>
        `;
    } else {
        data.forEach(item => {
            html += `
                <tr>
                    <td><small>${new Date(item.created_at).toLocaleDateString()}</small></td>
                    <td>${item.device ? item.device.device_name : 'Unknown'}</td>
                    <td>${item.device && item.device.user ? item.device.user.name : 'Unknown'}</td>
                    <td>${item.farm ? item.farm.name : 'N/A'}</td>
                    <td><span class="badge bg-info">${item.ph || 'N/A'}</span></td>
                    <td><span class="badge bg-primary">${item.moisture ? item.moisture + '%' : 'N/A'}</span></td>
                    <td><span class="badge bg-warning">${item.temperature ? item.temperature + '°C' : 'N/A'}</span></td>
                    <td>
                        <small>
                            N:${item.nitrogen || '-'}<br>
                            P:${item.phosphorus || '-'}<br>
                            K:${item.potassium || '-'}
                        </small>
                    </td>
                    <td>
                        <span class="badge bg-${getHealthColorFromScore(item.soil_health_score)}">${item.soil_health_score || 0}%</span>
                    </td>
                </tr>
            `;
        });
    }

    html += '</tbody></table></div>';
    document.getElementById('historyContent').innerHTML = html;
}

function getHealthColorFromScore(score) {
    if (!score && score !== 0) return 'secondary';

    score = parseFloat(score);
    if (score >= 80) return 'success';
    if (score >= 60) return 'info';
    if (score >= 40) return 'warning';
    return 'danger';
}

function loadAnalytics() {
    fetch('/admin/soil/analytics', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCSRFToken()
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            renderCharts(data);
        } else {
            console.error('Analytics failed:', data.message);
        }
    })
    .catch(error => {
        console.error('Error loading analytics:', error);
    });
}

function renderCharts(data) {
    try {
        // Health Distribution Chart
        const healthCtx = document.getElementById('healthChart').getContext('2d');

        // Destroy existing chart if it exists
        if (window.healthChart instanceof Chart) {
            window.healthChart.destroy();
        }

        window.healthChart = new Chart(healthCtx, {
            type: 'doughnut',
            data: {
                labels: data.health_distribution ? data.health_distribution.map(item => item.health_status) : [],
                datasets: [{
                    data: data.health_distribution ? data.health_distribution.map(item => item.count) : [],
                    backgroundColor: ['#28a745', '#17a2b8', '#ffc107', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Soil Health Distribution'
                    }
                }
            }
        });

        // Trends Chart
        const trendsCtx = document.getElementById('trendsChart').getContext('2d');

        // Destroy existing chart if it exists
        if (window.trendsChart instanceof Chart) {
            window.trendsChart.destroy();
        }

        window.trendsChart = new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: data.trends ? data.trends.map(item => item.date) : [],
                datasets: [
                    {
                        label: 'pH Level',
                        data: data.trends ? data.trends.map(item => item.avg_ph) : [],
                        borderColor: '#8B4513',
                        backgroundColor: 'rgba(139, 69, 19, 0.1)',
                        tension: 0.1
                    },
                    {
                        label: 'Moisture %',
                        data: data.trends ? data.trends.map(item => item.avg_moisture) : [],
                        borderColor: '#17a2b8',
                        backgroundColor: 'rgba(23, 162, 184, 0.1)',
                        tension: 0.1
                    },
                    {
                        label: 'Temperature °C',
                        data: data.trends ? data.trends.map(item => item.avg_temperature) : [],
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Soil Trends (Last 30 Days)'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error rendering charts:', error);
    }
}

// Generate demo data function
function generateDemoData() {
    Swal.fire({
        title: 'Generate Demo Data?',
        text: 'This will create sample soil readings for testing purposes.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, generate data',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Generating...',
                text: 'Creating demo soil data...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => Swal.showLoading()
            });

            // Make request to generate demo data endpoint
            fetch('/admin/soil/generate-demo-data', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCSRFToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message || 'Demo data generated successfully!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Reload current section
                        if (currentSection === 'liveData') {
                            loadLiveData();
                        } else {
                            loadInitialData();
                        }
                    });
                } else {
                    Swal.fire('Error', data.message || 'Failed to generate demo data', 'error');
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Error:', error);
                Swal.fire('Error', 'Failed to generate demo data: ' + error.message, 'error');
            });
        }
    });
}
</script>
@endsection
