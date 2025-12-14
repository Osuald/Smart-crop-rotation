@extends('layouts.farmer')

@section('title', 'Welcome to Smart Crop Rotation System')

@section('styles')
<style>
    /* Dashboard specific styles */
    .overview-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .section-title {
        font-size: 20px;
        font-weight: 600;
        color: #2c5530;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .soil-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .soil-stat-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        border: 1px solid #dee2e6;
    }

    .soil-stat-card h3 {
        margin: 0 0 10px 0;
        color: #495057;
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .soil-stat-card .value {
        font-size: 24px;
        font-weight: 700;
        margin: 0;
    }

    .ph-value {
        color: #dc3545;
    }

    .moisture-value {
        color: #007bff;
    }

    .temperature-value {
        color: #fd7e14;
    }

    .crop-value {
        color: #28a745;
    }

    .chart-container {
        display: flex;
        align-items: end;
        justify-content: space-between;
        height: 200px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 10px;
        gap: 10px;
    }

    .chart-bar {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
        flex: 1;
    }

    .chart-blue-bar {
        background: linear-gradient(to top, #007bff, #66b3ff);
        width: 20px;
        border-radius: 3px 3px 0 0;
        min-height: 10px;
    }

    .chart-green-bar {
        background: linear-gradient(to top, #28a745, #6fdc6f);
        width: 20px;
        border-radius: 3px 3px 0 0;
        min-height: 10px;
    }

    .alert {
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .alert-success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }

    .close {
        float: right;
        font-size: 20px;
        font-weight: bold;
        line-height: 1;
        color: #000;
        text-shadow: 0 1px 0 #fff;
        opacity: .5;
        cursor: pointer;
    }

    .close:hover {
        opacity: .75;
    }

    /* Welcome banner */
    .welcome-banner {
        background: linear-gradient(135deg, #2c5530 0%, #4a7c4f 100%);
        color: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 20px;
    }

    .welcome-banner h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 600;
    }

    .welcome-banner p {
        margin: 5px 0 0 0;
        opacity: 0.9;
    }
</style>
@endsection

@section('content')
<!-- Display success message if any -->
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
        <span class="close" onclick="this.parentElement.style.display='none';">&times;</span>
    </div>
@endif

<!-- Welcome Banner -->
<!-- <div class="welcome-banner">
    <h2>Welcome to Smart Crop Rotation System</h2>
    <p>Monitor your soil conditions and optimize your crop rotation schedule</p>
</div> -->

<!-- Main Dashboard Content -->
<div class="overview-card">
    <div class="section-title">
        <i class="fas fa-chart-line"></i>
        Your Soil Status
    </div>

    <div class="soil-stats">
        <div class="soil-stat-card">
            <h3>pH Level</h3>
            <div class="value ph-value">5.6</div>
        </div>

        <div class="soil-stat-card">
            <h3>Moisture (%)</h3>
            <div class="value moisture-value">21%</div>
        </div>

        <div class="soil-stat-card">
            <h3>Temperature (°F)</h3>
            <div class="value temperature-value">74°</div>
        </div>

        <div class="soil-stat-card">
            <h3>Current Crop</h3>
            <div class="value crop-value">Rice</div>
        </div>
    </div>

    <div class="chart-container">
        <!-- Chart bars representing soil data over time -->
        <div class="chart-bar">
            <div class="chart-blue-bar" style="height: 120px;"></div>
            <div class="chart-green-bar" style="height: 90px;"></div>
        </div>

        <div class="chart-bar">
            <div class="chart-blue-bar" style="height: 100px;"></div>
            <div class="chart-green-bar" style="height: 110px;"></div>
        </div>

        <div class="chart-bar">
            <div class="chart-blue-bar" style="height: 130px;"></div>
            <div class="chart-green-bar" style="height: 50px;"></div>
        </div>

        <div class="chart-bar">
            <div class="chart-blue-bar" style="height: 150px;"></div>
            <div class="chart-green-bar" style="height: 105px;"></div>
        </div>

        <div class="chart-bar">
            <div class="chart-blue-bar" style="height: 110px;"></div>
            <div class="chart-green-bar" style="height: 120px;"></div>
        </div>

        <div class="chart-bar">
            <div class="chart-blue-bar" style="height: 100px;"></div>
            <div class="chart-green-bar" style="height: 130px;"></div>
        </div>

        <div class="chart-bar">
            <div class="chart-blue-bar" style="height: 120px;"></div>
            <div class="chart-green-bar" style="height: 140px;"></div>
        </div>

        <div class="chart-bar">
            <div class="chart-blue-bar" style="height: 90px;"></div>
            <div class="chart-green-bar" style="height: 70px;"></div>
        </div>

        <div class="chart-bar">
            <div class="chart-blue-bar" style="height: 80px;"></div>
            <div class="chart-green-bar" style="height: 110px;"></div>
        </div>

        <div class="chart-bar">
            <div class="chart-blue-bar" style="height: 130px;"></div>
            <div class="chart-green-bar" style="height: 100px;"></div>
        </div>
    </div>
</div>

<!-- Additional Quick Actions -->
<div class="row mt-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-seedling fa-2x text-success mb-3"></i>
                <h5 class="card-title">Soil Analysis</h5>
                <p class="card-text">Get detailed soil composition and recommendations</p>
                <a href="{{ route('farmer.soil') }}" class="btn btn-primary">View Details</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-chart-bar fa-2x text-info mb-3"></i>
                <h5 class="card-title">Reports</h5>
                <p class="card-text">View your farming reports and analytics</p>
                <a href="#" class="btn btn-primary">View Reports</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-lightbulb fa-2x text-warning mb-3"></i>
                <h5 class="card-title">Recommendations</h5>
                <p class="card-text">Get AI-powered crop rotation suggestions</p>
                <a href="#" class="btn btn-primary">Get Recommendations</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Dashboard specific JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-hide success messages after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.display = 'none';
            }, 5000);
        });
    });
</script>
@endsection
