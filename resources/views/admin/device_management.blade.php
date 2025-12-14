@extends('layouts.admin')

@section('title', 'Device Management - Admin Dashboard')

@section('styles')
<!-- Add CSRF token meta tag -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<!-- Custom styles -->
<style>
    .modal-header {
        background: #0ac15e;
        color: white;
    }
    .modal-header .btn-close {
        filter: invert(1);
    }
    .btn-group .btn {
        margin: 0 2px;
    }
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    .status-active { background-color: #198754 !important; }
    .status-inactive { background-color: #6c757d !important; }
    .status-maintenance { background-color: #ffc107 !important; }
    .status-offline { background-color: #dc3545 !important; }
    .device-avatar {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);
    }
    .battery-indicator {
        width: 100px;
        height: 6px;
    }
    .online-indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }
    .online { background-color: #28a745; }
    .offline { background-color: #dc3545; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-purple text-white d-flex justify-content-between align-items-center" style="background:white">
                    <h3 class="mb-0" style="color:black">Device Management</h3>
                    <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#deviceModal" onclick="openCreateModal()">
                        <i class="fas fa-plus me-2"></i>Add New Device
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Devices Table Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="devicesTable">
<thead style="background-color: #343a40; color: white;">
                                <tr>
                                    <th>Device</th>
                                    <th>Serial Number</th>
                                    <th>Assigned User</th>
                                    <th>Location</th>
                                    <!-- <th><i class="fas fa-seedling me-1"></i>Farm UPI</th> -->
                                    <!-- <th><i class="fas fa-circle me-1"></i>Status</th> -->

                                    <!-- <th><i class="fas fa-battery-half me-1"></i>Battery</th> -->
                                    <!-- <th><i class="fas fa-wifi me-1"></i>Online</th> -->

                                    <!-- <th><i class="fas fa-calendar me-1"></i>Installed</th> -->
                                    <th><i class="fas fa-cogs me-1"></i>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="devicesTableBody">
                                <!-- Dynamic content will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Device Create/Edit Modal -->
<div class="modal fade" id="deviceModal" tabindex="-1" aria-labelledby="deviceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deviceModalLabel">
                    <i class="fas fa-microchip me-2"></i>Add New Device
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deviceForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="device_serial_number" class="form-label">
                                <i class="fas fa-barcode me-1"></i>Device Serial Number
                            </label>
                            <input type="text" class="form-control" id="device_serial_number" name="device_serial_number" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="device_name" class="form-label">
                                <i class="fas fa-tag me-1"></i>Device Name
                            </label>
                            <input type="text" class="form-control" id="device_name" name="device_name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="device_type" class="form-label">
                                <i class="fas fa-microchip me-1"></i>Device Type
                            </label>
                            <select class="form-select" id="device_type" name="device_type" required>
                                <option value="">Select Device Type</option>
                                <option value="IoT Sensor">IoT Sensor</option>
                                <option value="Weather Station">Weather Station</option>
                                <option value="Soil Monitor">Soil Monitor</option>
                                <option value="Irrigation Controller">Irrigation Controller</option>
                                <option value="Camera System">Camera System</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="user_id" class="form-label">
                                <i class="fas fa-user me-1"></i>Assigned User
                            </label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="">Select User</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="installation_location" class="form-label">
                                <i class="fas fa-map-marker-alt me-1"></i>Installation Location
                            </label>
                            <input type="text" class="form-control" id="installation_location" name="installation_location" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="farm_upi" class="form-label">
                                <i class="fas fa-seedling me-1"></i>Farm UPI (Unique Plot Identifier)
                            </label>
                            <input type="text" class="form-control" id="farm_upi" name="farm_upi" placeholder="e.g., UPI-2024-FARM-001">
                            <div class="form-text">Optional: Enter the unique identifier for the farm plot</div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">
                                <i class="fas fa-circle me-1"></i>Status
                            </label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="inactive">Inactive</option>
                                <option value="active">Active</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="offline">Offline</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="latitude" class="form-label">
                                <i class="fas fa-map-pin me-1"></i>Latitude
                            </label>
                            <input type="number" class="form-control" id="latitude" name="latitude" step="0.00000001" min="-90" max="90">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="longitude" class="form-label">
                                <i class="fas fa-map-pin me-1"></i>Longitude
                            </label>
                            <input type="number" class="form-control" id="longitude" name="longitude" step="0.00000001" min="-180" max="180">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="firmware_version" class="form-label">
                                <i class="fas fa-code me-1"></i>Firmware Version
                            </label>
                            <input type="text" class="form-control" id="firmware_version" name="firmware_version">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="battery_level" class="form-label">
                                <i class="fas fa-battery-half me-1"></i>Battery Level (%)
                            </label>
                            <input type="number" class="form-control" id="battery_level" name="battery_level" min="0" max="100">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">
                                <i class="fas fa-sensors me-1"></i>Sensor Types
                            </label>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="temperature" id="sensor_temperature" name="sensor_types[]">
                                        <label class="form-check-label" for="sensor_temperature">Temperature</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="humidity" id="sensor_humidity" name="sensor_types[]">
                                        <label class="form-check-label" for="sensor_humidity">Humidity</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="soil_ph" id="sensor_soil_ph" name="sensor_types[]">
                                        <label class="form-check-label" for="sensor_soil_ph">Soil pH</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="soil_moisture" id="sensor_soil_moisture" name="sensor_types[]">
                                        <label class="form-check-label" for="sensor_soil_moisture">Soil Moisture</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="light" id="sensor_light" name="sensor_types[]">
                                        <label class="form-check-label" for="sensor_light">Light Level</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="rain" id="sensor_rain" name="sensor_types[]">
                                        <label class="form-check-label" for="sensor_rain">Rain Gauge</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="wind" id="sensor_wind" name="sensor_types[]">
                                        <label class="form-check-label" for="sensor_wind">Wind Speed</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="pressure" id="sensor_pressure" name="sensor_types[]">
                                        <label class="form-check-label" for="sensor_pressure">Air Pressure</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="notes" class="form-label">
                                <i class="fas fa-sticky-note me-1"></i>Notes
                            </label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save me-1"></i>Save Device
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Device View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">
                    <i class="fas fa-microchip me-2"></i>Device Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewModalBody">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading device details...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-microchip fa-3x text-danger mb-3"></i>
                    <h5>Are you sure you want to delete this device?</h5>
                    <p class="text-muted">This action cannot be undone and will also delete all associated readings.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-1"></i>Delete Device
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 for better alerts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let isEditing = false;
let currentDeviceId = null;
let users = [];

// Add CSRF token to all requests
function getCSRFToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.getAttribute('content') : '{{ csrf_token() }}';
}

// Load devices on page load
document.addEventListener('DOMContentLoaded', function() {
    loadDevices();
});

function loadDevices() {
    fetch('/admin/devices', {
        method: 'GET',
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
        console.log('Loaded data:', data);

        if (data.devices) {
            renderDevicesTable(data.devices);
        }
        if (data.users) {
            users = data.users;
            populateUserSelect();
        }
    })
    .catch(error => {
        console.error('Error loading devices:', error);
        Swal.fire('Error', 'Failed to load devices: ' + error.message, 'error');
    });
}

function populateUserSelect() {
    const userSelect = document.getElementById('user_id');
    if (!userSelect) {
        console.error('User select element not found');
        return;
    }

    userSelect.innerHTML = '<option value="">Select User</option>';

    if (!users || users.length === 0) {
        userSelect.innerHTML += '<option value="" disabled>No users available</option>';
        console.warn('No users available for selection');
        return;
    }

    users.forEach(user => {
        const option = document.createElement('option');
        option.value = user.id;
        option.textContent = `${user.name || user.username} (${user.email})`;

        // Add role information if available
        if (user.role && user.role.name) {
            option.textContent += ` - ${user.role.name}`;
        }

        userSelect.appendChild(option);
    });

    console.log('User select populated with', users.length, 'users');
}

function renderDevicesTable(devices) {
    const tbody = document.getElementById('devicesTableBody');
    tbody.innerHTML = '';

    if (devices.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center text-muted py-4">
                    <i class="fas fa-microchip fa-2x mb-2"></i>
                    <br>No devices found. Add your first device to get started.
                </td>
            </tr>
        `;
        return;
    }

    devices.forEach(device => {
        const row = document.createElement('tr');
        const isOnline = device.last_communication &&
            new Date(device.last_communication) > new Date(Date.now() - 30 * 60 * 1000);

        // const batteryColor = device.battery_level ?
        //     (device.battery_level >= 70 ? 'success' : device.battery_level >= 30 ? 'warning' : 'danger') : 'secondary';

        row.innerHTML = `
            <td>
                <div class="d-flex align-items-center">

                    <div>
                        <div class="fw-bold">${device.device_name}</div>
                        <small class="text-muted">${device.device_type}</small>
                    </div>
                </div>
            </td>
            <td><code>${device.device_serial_number}</code></td>
            <td>
                <div>
                    <div class="fw-bold">${device.user ? device.user.name : 'N/A'}</div>
                    <small class="text-muted">${device.user ? device.user.email : 'Unknown User'}</small>
                </div>
            </td>
            <td><span class="badge bg-info">${device.installation_location}</span></td>


            <td>
                <div class="btn-group" role="group" aria-label="Device actions">
                    <button type="button" class="btn btn-outline-info btn-sm"
                            onclick="viewDevice(${device.id})"
                            data-bs-toggle="modal"
                            data-bs-target="#viewModal"
                            title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-outline-warning btn-sm"
                            onclick="editDevice(${device.id})"
                            data-bs-toggle="modal"
                            data-bs-target="#deviceModal"
                            title="Edit Device">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm"
                            onclick="confirmDelete(${device.id})"
                            title="Delete Device">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function openCreateModal() {
    isEditing = false;
    currentDeviceId = null;

    // Reset modal
    document.getElementById('deviceModalLabel').innerHTML = '<i class="fas fa-microchip me-2"></i>Add New Device';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save me-1"></i>Save Device';
    document.getElementById('deviceForm').reset();

    // Reset validation states
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

    // Uncheck all sensor types
    document.querySelectorAll('input[name="sensor_types[]"]').forEach(cb => cb.checked = false);

    // Repopulate user select to ensure it's loaded
    populateUserSelect();
}

function editDevice(deviceId) {
    if (!deviceId) {
        Swal.fire('Error', 'Invalid device ID', 'error');
        return;
    }

    isEditing = true;
    currentDeviceId = deviceId;

    // Update modal title
    document.getElementById('deviceModalLabel').innerHTML = '<i class="fas fa-microchip me-2"></i>Edit Device';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save me-1"></i>Update Device';

    // Show loading
    Swal.fire({
        title: 'Loading...',
        text: 'Fetching device data',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => Swal.showLoading()
    });

    // Fetch device data
    fetch(`/admin/devices/${deviceId}`, {
        method: 'GET',
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
        Swal.close();

        if (data) {
            document.getElementById('device_serial_number').value = data.device_serial_number || '';
            document.getElementById('device_name').value = data.device_name || '';
            document.getElementById('device_type').value = data.device_type || '';
            document.getElementById('user_id').value = data.user_id || '';
            document.getElementById('installation_location').value = data.installation_location || '';
            document.getElementById('farm_upi').value = data.farm_upi || '';
            document.getElementById('status').value = data.status || '';
            document.getElementById('latitude').value = data.latitude || '';
            document.getElementById('longitude').value = data.longitude || '';
            document.getElementById('firmware_version').value = data.firmware_version || '';
            document.getElementById('battery_level').value = data.battery_level || '';
            document.getElementById('notes').value = data.notes || '';

            // Set sensor types
            document.querySelectorAll('input[name="sensor_types[]"]').forEach(cb => cb.checked = false);
            if (data.sensor_types && Array.isArray(data.sensor_types)) {
                data.sensor_types.forEach(sensor => {
                    const checkbox = document.querySelector(`input[value="${sensor}"]`);
                    if (checkbox) checkbox.checked = true;
                });
            }
        } else {
            throw new Error('Device data not found');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Failed to load device data: ' + error.message, 'error');
    });
}

function viewDevice(deviceId) {
    if (!deviceId) {
        Swal.fire('Error', 'Invalid device ID', 'error');
        return;
    }

    // Reset modal content with loading state
    document.getElementById('viewModalBody').innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading device details...</p>
        </div>
    `;

    fetch(`/admin/devices/${deviceId}`, {
        method: 'GET',
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
        if (data) {
            const isOnline = data.last_communication &&
                new Date(data.last_communication) > new Date(Date.now() - 30 * 60 * 1000);

            document.getElementById('viewModalBody').innerHTML = `
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary">Device Information</h6>
                                <p><strong>Serial Number:</strong> <code>${data.device_serial_number || 'N/A'}</code></p>
                                <p><strong>Name:</strong> ${data.device_name || 'N/A'}</p>
                                <p><strong>Type:</strong> ${data.device_type || 'N/A'}</p>
                                <p><strong>Status:</strong>
                                    <span class="badge bg-${data.status === 'active' ? 'success' : (data.status === 'inactive' ? 'secondary' : data.status === 'maintenance' ? 'warning' : 'danger')}">
                                        ${data.status ? data.status.charAt(0).toUpperCase() + data.status.slice(1) : 'N/A'}
                                    </span>
                                </p>
                                <p><strong>Firmware:</strong> ${data.firmware_version || 'N/A'}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary">Installation Information</h6>
                                <p><strong>Assigned User:</strong> ${data.user ? data.user.name : 'N/A'}</p>
                                <p><strong>User Email:</strong> ${data.user ? data.user.email : 'Unknown'}</p>
                                <p><strong>Installation Location:</strong> <span class="badge bg-info">${data.installation_location || 'N/A'}</span></p>
                                <p><strong>Farm UPI:</strong>
                                    ${data.farm_upi ?
                                        `<span class="badge bg-success">${data.farm_upi}</span>` :
                                        '<span class="text-muted">Not set</span>'
                                    }
                                </p>
                                <p><strong>Coordinates:</strong> ${data.latitude && data.longitude ? `${data.latitude}, ${data.longitude}` : 'Not set'}</p>
                                <p><strong>Installed:</strong> ${data.installed_at ? new Date(data.installed_at).toLocaleDateString() : 'Not set'}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary">Status Information</h6>
                                <p><strong>Online Status:</strong>
                                    <span class="badge bg-${isOnline ? 'success' : 'danger'}">
                                        <i class="fas fa-circle me-1" style="font-size: 0.6em;"></i>
                                        ${isOnline ? 'Online' : 'Offline'}
                                    </span>
                                </p>
                                <p><strong>Battery Level:</strong> ${data.battery_level !== null ? data.battery_level + '%' : 'N/A'}</p>
                                <p><strong>Last Communication:</strong> ${data.last_communication ? new Date(data.last_communication).toLocaleString() : 'Never'}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary">Sensor Types</h6>
                                <div class="mt-2">
                                    ${data.sensor_types && data.sensor_types.length > 0 ?
                                        data.sensor_types.map(sensor => `<span class="badge bg-secondary me-1 mb-1">${sensor}</span>`).join('') :
                                        '<span class="text-muted">No sensors configured</span>'
                                    }
                                </div>
                            </div>
                        </div>
                        ${data.notes ? `
                        <hr>
                        <div>
                            <h6 class="text-primary">Notes</h6>
                            <p class="text-muted">${data.notes}</p>
                        </div>
                        ` : ''}
                    </div>
                </div>
            `;
        } else {
            throw new Error('Device data not found');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('viewModalBody').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Error loading device data: ${error.message}
            </div>
        `;
    });
}

function confirmDelete(deviceId) {
    if (!deviceId) {
        Swal.fire('Error', 'Invalid device ID', 'error');
        return;
    }

    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();

    document.getElementById('confirmDeleteBtn').onclick = function() {
        deleteDevice(deviceId);
        deleteModal.hide();
    };
}

function deleteDevice(deviceId) {
    Swal.fire({
        title: 'Deleting...',
        text: 'Please wait while we delete the device.',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => Swal.showLoading()
    });

    fetch(`/admin/devices/${deviceId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': getCSRFToken(),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success !== false) {
            Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                text: 'Device has been deleted successfully.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                loadDevices();
            });
        } else {
            throw new Error(data.message || 'Failed to delete device');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: error.message || 'An unexpected error occurred while deleting the device.'
        });
    });
}

// Form submission - FIXED VERSION
document.getElementById('deviceForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Clear previous validation states
    this.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

    // Show loading state
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving...';
    submitBtn.disabled = true;

    // Create FormData object
    const formData = new FormData();

    // Add all form fields
    const formFields = ['device_serial_number', 'device_name', 'device_type', 'user_id',
                       'installation_location', 'farm_upi', 'status', 'latitude', 'longitude',
                       'firmware_version', 'battery_level', 'notes'];

    formFields.forEach(field => {
        const element = document.getElementById(field);
        if (element && element.value) {
            formData.append(field, element.value);
        }
    });

    // Handle sensor types array
    const sensorTypes = [];
    document.querySelectorAll('input[name="sensor_types[]"]:checked').forEach(cb => {
        sensorTypes.push(cb.value);
    });

    // Add sensor types as JSON string for better handling
    if (sensorTypes.length > 0) {
        formData.append('sensor_types', JSON.stringify(sensorTypes));
    }

    // Add CSRF token
    formData.append('_token', getCSRFToken());

    // Add method for updates
    if (isEditing) {
        formData.append('_method', 'PUT');
    }

    const url = isEditing ? `/admin/devices/${currentDeviceId}` : '/admin/devices';

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': getCSRFToken(),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            return response.text().then(text => {
                console.log('Error response:', text);
                try {
                    const data = JSON.parse(text);
                    throw new Error(data.message || `HTTP error! status: ${response.status}`);
                } catch (e) {
                    throw new Error(`HTTP error! status: ${response.status} - ${text}`);
                }
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Success response:', data);
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;

        if (data.success !== false) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: isEditing ? 'Device updated successfully.' : 'Device created successfully.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                // Close modal and reload table
                bootstrap.Modal.getInstance(document.getElementById('deviceModal')).hide();
                loadDevices();
            });
        } else {
            // Handle validation errors
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const input = document.getElementById(field);
                    if (input) {
                        input.classList.add('is-invalid');
                        const feedback = input.nextElementSibling;
                        if (feedback && feedback.classList.contains('invalid-feedback')) {
                            feedback.textContent = data.errors[field][0];
                        }
                    }
                });
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please check the form fields and try again.'
                });
            } else {
                throw new Error(data.message || 'Validation failed');
            }
        }
    })
    .catch(error => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: error.message || 'An unexpected error occurred while saving the device.'
        });
    });
});
</script>
@endsection
