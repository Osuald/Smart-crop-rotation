@extends('layouts.admin')

@section('title', 'Manual Soil Data Input - Admin Dashboard')

@section('styles')
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<!-- Custom styles -->
<style>
    .input-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border: 1px solid #e3f2fd;
        transition: all 0.3s ease;
    }

    .input-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .input-card .card-header {
        background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%);
        color: white;
        border-radius: 15px 15px 0 0;
    }

    .form-floating label {
        color: #666;
    }

    .form-floating .form-control:focus ~ label {
        color: #8B4513;
    }

    .form-control:focus {
        border-color: #8B4513;
        box-shadow: 0 0 0 0.2rem rgba(139, 69, 19, 0.25);
    }

    .btn-soil {
        background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 12px 30px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-soil:hover {
        background: linear-gradient(135deg, #7A3C0D 0%, #B8561A 100%);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(139, 69, 19, 0.3);
    }

    .crop-history-card {
        border: 2px dashed #ddd;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .crop-history-card.filled {
        border-color: #8B4513;
        background-color: #f8f9fa;
    }

    .season-badge {
        background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%);
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.9em;
        font-weight: 600;
    }

    .required-field::after {
        content: " *";
        color: #dc3545;
        font-weight: bold;
    }

    .info-tooltip {
        cursor: help;
        color: #6c757d;
    }

    .progress-indicator {
        height: 4px;
        background: linear-gradient(90deg, #8B4513, #D2691E);
        border-radius: 2px;
        margin-bottom: 20px;
    }

    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
    }

    .step {
        flex: 1;
        text-align: center;
        position: relative;
    }

    .step::before {
        content: "";
        position: absolute;
        top: 15px;
        left: 50%;
        width: 100%;
        height: 2px;
        background: #ddd;
        z-index: 1;
    }

    .step:last-child::before {
        display: none;
    }

    .step-circle {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #ddd;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        position: relative;
        z-index: 2;
        font-weight: bold;
    }

    .step.active .step-circle {
        background: #8B4513;
    }

    .step.completed .step-circle {
        background: #28a745;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1"><i class="fas fa-edit me-2"></i>Manual Soil Data Input</h3>
                        <small>Enter soil conditions and crop history for intelligent recommendations</small>
                    </div>
                    <a href="{{ route('admin.soil') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back to Soil Management
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Indicator -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="step-indicator">
                <div class="step active" id="step1">
                    <div class="step-circle">1</div>
                    <small>Basic Info</small>
                </div>
                <div class="step" id="step2">
                    <div class="step-circle">2</div>
                    <small>Soil Data</small>
                </div>
                <div class="step" id="step3">
                    <div class="step-circle">3</div>
                    <small>Crop History</small>
                </div>
                <div class="step" id="step4">
                    <div class="step-circle">4</div>
                    <small>Analysis</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <form id="soilDataForm" novalidate>
        @csrf

        <!-- Step 1: Basic Information -->
        <div class="step-content" id="stepContent1">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="input-card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="user_id" name="user_id" required>
                                            <option value="">Select User/Farmer</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                            @endforeach
                                        </select>
                                        <label class="required-field">User/Farmer</label>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="device_id" name="device_id">
                                            <option value="">Select Device (Optional)</option>
                                            @foreach($devices as $device)
                                                <option value="{{ $device->id }}">{{ $device->device_name }} - {{ $device->user->name ?? 'Unknown' }}</option>
                                            @endforeach
                                        </select>
                                        <label>Associated Device</label>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="farm_plot_identifier" name="farm_plot_identifier" required placeholder="e.g., Plot-A1, North Field, etc.">
                                        <label class="required-field">Farm Plot Identifier</label>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Unique identifier for the specific plot/field (e.g., Plot-A1, North-Field-2)
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end mt-4">
                                <button type="button" class="btn btn-soil" onclick="nextStep(2)">
                                    Next: Soil Data <i class="fas fa-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Soil Data -->
        <div class="step-content" id="stepContent2" style="display: none;">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="input-card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-flask me-2"></i>Soil Conditions</h5>
                        </div>
                        <div class="card-body p-4">
                            <!-- Required Parameters -->
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary border-bottom pb-2">
                                        <i class="fas fa-star text-warning me-1"></i>Required Parameters
                                    </h6>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="ph_level" name="ph_level" min="3" max="10" step="0.1" required placeholder="6.5">
                                        <label class="required-field">
                                            pH Level
                                            <i class="fas fa-question-circle info-tooltip ms-1" title="Optimal range: 6.0-7.0 for most crops"></i>
                                        </label>
                                        <div class="form-text">Range: 3.0 - 10.0 (Optimal: 6.0-7.0)</div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="moisture_level" name="moisture_level" min="0" max="100" step="1" required placeholder="45">
                                        <label class="required-field">
                                            Moisture Level (%)
                                            <i class="fas fa-question-circle info-tooltip ms-1" title="Percentage of water content in soil"></i>
                                        </label>
                                        <div class="form-text">Range: 0-100% (Optimal: 40-60%)</div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="temperature" name="temperature" min="-10" max="50" step="0.5" required placeholder="25">
                                        <label class="required-field">
                                            Temperature (°C)
                                            <i class="fas fa-question-circle info-tooltip ms-1" title="Soil temperature affects plant growth"></i>
                                        </label>
                                        <div class="form-text">Range: -10°C to 50°C</div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Optional Parameters -->
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <h6 class="text-secondary border-bottom pb-2">
                                        <i class="fas fa-plus-circle me-1"></i>Additional Parameters (Optional)
                                    </h6>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="nitrogen_level" name="nitrogen_level" min="0" step="1" placeholder="30">
                                        <label>
                                            Nitrogen (N) Level (ppm)
                                            <i class="fas fa-question-circle info-tooltip ms-1" title="Essential for plant growth and chlorophyll"></i>
                                        </label>
                                        <div class="form-text">Typical range: 10-60 ppm</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="phosphorus_level" name="phosphorus_level" min="0" step="1" placeholder="25">
                                        <label>
                                            Phosphorus (P) Level (ppm)
                                            <i class="fas fa-question-circle info-tooltip ms-1" title="Important for root development and flowering"></i>
                                        </label>
                                        <div class="form-text">Typical range: 5-50 ppm</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="potassium_level" name="potassium_level" min="0" step="1" placeholder="200">
                                        <label>
                                            Potassium (K) Level (ppm)
                                            <i class="fas fa-question-circle info-tooltip ms-1" title="Helps with disease resistance and fruit quality"></i>
                                        </label>
                                        <div class="form-text">Typical range: 80-300 ppm</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="conductivity" name="conductivity" min="0" step="10" placeholder="500">
                                        <label>
                                            Electrical Conductivity (μS/cm)
                                            <i class="fas fa-question-circle info-tooltip ms-1" title="Measures soil salinity"></i>
                                        </label>
                                        <div class="form-text">Normal range: 100-800 μS/cm</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="organic_matter" name="organic_matter" min="0" max="20" step="0.1" placeholder="3.5">
                                        <label>
                                            Organic Matter (%)
                                            <i class="fas fa-question-circle info-tooltip ms-1" title="Indicates soil fertility and health"></i>
                                        </label>
                                        <div class="form-text">Ideal range: 3-6%</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="notes" name="notes" style="height: 100px" placeholder="Additional observations..."></textarea>
                                        <label>Additional Notes</label>
                                        <div class="form-text">Any additional observations about soil conditions, weather, etc.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" onclick="previousStep(1)">
                                    <i class="fas fa-arrow-left me-1"></i> Previous
                                </button>
                                <button type="button" class="btn btn-soil" onclick="nextStep(3)">
                                    Next: Crop History <i class="fas fa-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Crop History -->
        <div class="step-content" id="stepContent3" style="display: none;">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="input-card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-history me-2"></i>Recent Crop History
                                <small class="ms-2 opacity-75">(Last 2-3 seasons)</small>
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Why crop history matters:</strong> Our recommendation system considers previous crops to suggest optimal rotation patterns,
                                prevent soil depletion, and reduce pest/disease buildup. This helps maintain soil health and maximize yields.
                            </div>

                            <div id="cropHistoryContainer">
                                <!-- Crop history entries will be added here -->
                            </div>

                            <div class="text-center mb-4">
                                <button type="button" class="btn btn-outline-primary" onclick="addCropHistory()" id="addCropBtn">
                                    <i class="fas fa-plus me-2"></i>Add Crop History
                                </button>
                                <p class="small text-muted mt-2">Add up to 3 recent crops grown in this plot</p>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary" onclick="previousStep(2)">
                                    <i class="fas fa-arrow-left me-1"></i> Previous
                                </button>
                                <button type="button" class="btn btn-soil" onclick="nextStep(4)">
                                    Analyze & Get Recommendations <i class="fas fa-search ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 4: Analysis Loading -->
        <div class="step-content" id="stepContent4" style="display: none;">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="input-card">
                        <div class="card-header text-center">
                            <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Analyzing Your Data</h5>
                        </div>
                        <div class="card-body p-5 text-center">
                            <div id="analysisLoader">
                                <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <h6 id="analysisStatus">Processing soil data...</h6>
                                <div class="progress mt-3" style="height: 8px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                                         style="width: 0%" id="analysisProgress"></div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted" id="analysisStep">Initializing analysis...</small>
                                </div>
                            </div>

                            <div id="analysisComplete" style="display: none;">
                                <div class="text-success mb-3">
                                    <i class="fas fa-check-circle fa-3x"></i>
                                </div>
                                <h5 class="text-success">Analysis Complete!</h5>
                                <p class="text-muted">Your soil data has been analyzed and recommendations are ready.</p>
                                <button type="button" class="btn btn-soil btn-lg" id="viewResultsBtn">
                                    <i class="fas fa-eye me-2"></i>View Results
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let currentStep = 1;
let cropHistoryCount = 0;
const maxCropHistory = 3;

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('.info-tooltip'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Auto-load crop history when plot identifier changes
    document.getElementById('farm_plot_identifier').addEventListener('blur', checkExistingCropHistory);
});

function nextStep(step) {
    if (validateCurrentStep()) {
        // Update step indicators
        document.getElementById(`step${currentStep}`).classList.remove('active');
        document.getElementById(`step${currentStep}`).classList.add('completed');

        // Hide current step content
        document.getElementById(`stepContent${currentStep}`).style.display = 'none';

        // Show next step content
        currentStep = step;
        document.getElementById(`step${currentStep}`).classList.add('active');
        document.getElementById(`stepContent${currentStep}`).style.display = 'block';

        // Special handling for step 4 (analysis)
        if (step === 4) {
            performAnalysis();
        }

        // Scroll to top
        window.scrollTo(0, 0);
    }
}

function previousStep(step) {
    // Update step indicators
    document.getElementById(`step${currentStep}`).classList.remove('active');
    if (currentStep > 1) {
        document.getElementById(`step${currentStep}`).classList.remove('completed');
    }

    // Hide current step content
    document.getElementById(`stepContent${currentStep}`).style.display = 'none';

    // Show previous step content
    currentStep = step;
    document.getElementById(`step${currentStep}`).classList.add('active');
    document.getElementById(`stepContent${currentStep}`).style.display = 'block';

    // Scroll to top
    window.scrollTo(0, 0);
}

function validateCurrentStep() {
    const currentStepElement = document.getElementById(`stepContent${currentStep}`);
    const requiredFields = currentStepElement.querySelectorAll('[required]');
    let isValid = true;

    // Clear previous validation states
    currentStepElement.querySelectorAll('.is-invalid').forEach(el => {
        el.classList.remove('is-invalid');
    });

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            const feedback = field.nextElementSibling?.nextElementSibling;
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.textContent = 'This field is required';
            }
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
        }
    });

    if (!isValid) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Information',
            text: 'Please fill in all required fields before continuing.',
            confirmButtonColor: '#8B4513'
        });
    }

    return isValid;
}

function addCropHistory() {
    if (cropHistoryCount >= maxCropHistory) {
        Swal.fire({
            icon: 'info',
            title: 'Maximum Reached',
            text: `You can add up to ${maxCropHistory} crop history entries.`,
            confirmButtonColor: '#8B4513'
        });
        return;
    }

    cropHistoryCount++;
    const container = document.getElementById('cropHistoryContainer');

    const cropHistoryHTML = `
        <div class="crop-history-card p-4 mb-3" id="cropHistory${cropHistoryCount}">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="text-primary mb-0">
                    <i class="fas fa-seedling me-2"></i>Crop ${cropHistoryCount}
                </h6>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeCropHistory(${cropHistoryCount})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" name="crop_history[${cropHistoryCount}][crop_name]"
                               placeholder="e.g., Maize, Beans, Tomatoes" required>
                        <label class="required-field">Crop Name</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" name="crop_history[${cropHistoryCount}][crop_variety]"
                               placeholder="e.g., Hybrid DK-8031">
                        <label>Variety (Optional)</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" name="crop_history[${cropHistoryCount}][season]" required>
                            <option value="">Select Season</option>
                            <option value="2024A">2024A (Mar-Jul)</option>
                            <option value="2024B">2024B (Sep-Jan)</option>
                            <option value="2023A">2023A (Mar-Jul)</option>
                            <option value="2023B">2023B (Sep-Jan)</option>
                            <option value="2022A">2022A (Mar-Jul)</option>
                            <option value="2022B">2022B (Sep-Jan)</option>
                        </select>
                        <label class="required-field">Season</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="date" class="form-control" name="crop_history[${cropHistoryCount}][planting_date]" required>
                        <label class="required-field">Planting Date</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="date" class="form-control" name="crop_history[${cropHistoryCount}][harvest_date]">
                        <label>Harvest Date</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" name="crop_history[${cropHistoryCount}][yield_rating]">
                            <option value="">Select Rating</option>
                            <option value="excellent">Excellent (>90% expected)</option>
                            <option value="good">Good (70-90% expected)</option>
                            <option value="fair">Fair (50-70% expected)</option>
                            <option value="poor">Poor (<50% expected)</option>
                        </select>
                        <label>Yield Rating</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="number" class="form-control" name="crop_history[${cropHistoryCount}][yield_quantity]"
                               min="0" step="0.1" placeholder="e.g., 2.5">
                        <label>Yield (tonnes/ha)</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <select class="form-select" name="crop_history[${cropHistoryCount}][status]" required>
                            <option value="">Select Status</option>
                            <option value="harvested">Harvested</option>
                            <option value="active">Currently Growing</option>
                            <option value="failed">Failed/Lost</option>
                        </select>
                        <label class="required-field">Status</label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-floating">
                        <textarea class="form-control" name="crop_history[${cropHistoryCount}][growth_notes]"
                                  style="height: 80px" placeholder="Any issues, diseases, or observations..."></textarea>
                        <label>Growth Notes</label>
                    </div>
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', cropHistoryHTML);

    // Update the card to filled state
    document.getElementById(`cropHistory${cropHistoryCount}`).classList.add('filled');

    // Update button state
    if (cropHistoryCount >= maxCropHistory) {
        document.getElementById('addCropBtn').disabled = true;
        document.getElementById('addCropBtn').innerHTML = '<i class="fas fa-check me-2"></i>Maximum Entries Added';
    }
}

function removeCropHistory(index) {
    Swal.fire({
        title: 'Remove Crop History?',
        text: 'Are you sure you want to remove this crop history entry?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, remove it'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`cropHistory${index}`).remove();
            cropHistoryCount--;

            // Re-enable add button if below maximum
            if (cropHistoryCount < maxCropHistory) {
                const addBtn = document.getElementById('addCropBtn');
                addBtn.disabled = false;
                addBtn.innerHTML = '<i class="fas fa-plus me-2"></i>Add Crop History';
            }
        }
    });
}

function checkExistingCropHistory() {
    const userId = document.getElementById('user_id').value;
    const farmPlotId = document.getElementById('farm_plot_identifier').value;

    if (!userId || !farmPlotId) return;

    fetch('/admin/soil/crop-history', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            user_id: userId,
            farm_plot_identifier: farmPlotId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.crop_history.length > 0) {
            Swal.fire({
                title: 'Existing Crop History Found',
                text: `Found ${data.crop_history.length} previous crop(s) for this plot. Would you like to load them?`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#8B4513',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, load history'
            }).then((result) => {
                if (result.isConfirmed) {
                    loadExistingCropHistory(data.crop_history);
                }
            });
        }
    })
    .catch(error => console.error('Error checking crop history:', error));
}

function loadExistingCropHistory(cropHistory) {
    // Clear existing entries
    document.getElementById('cropHistoryContainer').innerHTML = '';
    cropHistoryCount = 0;

    // Load existing crop history
    cropHistory.forEach(crop => {
        addCropHistory();
        const index = cropHistoryCount;

        // Populate fields
        document.querySelector(`[name="crop_history[${index}][crop_name]"]`).value = crop.crop_name || '';
        document.querySelector(`[name="crop_history[${index}][crop_variety]"]`).value = crop.crop_variety || '';
        document.querySelector(`[name="crop_history[${index}][season]"]`).value = crop.season || '';
        document.querySelector(`[name="crop_history[${index}][planting_date]"]`).value = crop.planting_date || '';
        document.querySelector(`[name="crop_history[${index}][harvest_date]"]`).value = crop.harvest_date || '';
        document.querySelector(`[name="crop_history[${index}][yield_rating]"]`).value = crop.yield_rating || '';
        document.querySelector(`[name="crop_history[${index}][yield_quantity]"]`).value = crop.yield_quantity || '';
        document.querySelector(`[name="crop_history[${index}][status]"]`).value = crop.status || '';
        document.querySelector(`[name="crop_history[${index}][growth_notes]"]`).value = crop.growth_notes || '';
    });
}

function performAnalysis() {
    // Simulate analysis progress
    const steps = [
        { text: 'Validating soil parameters...', progress: 20 },
        { text: 'Analyzing pH and nutrient levels...', progress: 40 },
        { text: 'Processing crop rotation history...', progress: 60 },
        { text: 'Generating crop recommendations...', progress: 80 },
        { text: 'Finalizing analysis...', progress: 100 }
    ];

    let currentStepIndex = 0;

    function updateProgress() {
        if (currentStepIndex < steps.length) {
            const step = steps[currentStepIndex];
            document.getElementById('analysisStatus').textContent = 'Analyzing your soil data...';
            document.getElementById('analysisStep').textContent = step.text;
            document.getElementById('analysisProgress').style.width = step.progress + '%';

            currentStepIndex++;
            setTimeout(updateProgress, 1500);
        } else {
            // Submit the form
            submitAnalysis();
        }
    }

    updateProgress();
}

function submitAnalysis() {
    const formData = new FormData(document.getElementById('soilDataForm'));

    fetch('/admin/soil/manual-input', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Hide loader and show completion
            document.getElementById('analysisLoader').style.display = 'none';
            document.getElementById('analysisComplete').style.display = 'block';

            // Set up results button
            document.getElementById('viewResultsBtn').onclick = function() {
                window.location.href = data.redirect_url || `/admin/soil/analysis-results/${data.soil_data.id}`;
            };
        } else {
            throw new Error(data.message || 'Analysis failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Analysis Failed',
            text: error.message || 'An error occurred during analysis. Please try again.',
            confirmButtonColor: '#8B4513'
        }).then(() => {
            // Go back to previous step
            previousStep(3);
        });
    });
}

// Add CSRF token to meta if not exists
if (!document.querySelector('meta[name="csrf-token"]')) {
    const meta = document.createElement('meta');
    meta.name = 'csrf-token';
    meta.content = '{{ csrf_token() }}';
    document.getElementsByTagName('head')[0].appendChild(meta);
}
</script>
@endsection
