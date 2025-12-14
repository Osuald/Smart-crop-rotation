@extends('layouts.admin')

@section('title', 'Farmer Management - Admin Dashboard')

@section('styles')
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<!-- Custom styles -->
<style>
    .modal-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
    .status-inactive { background-color: #ffc107 !important; }
    .status-suspended { background-color: #dc3545 !important; }
    .farmer-avatar {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h3 class="mb-0"><i class="fas fa-seedling me-2"></i>Farmer Management</h3>
                    <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#farmerModal" onclick="openCreateModal()">
                        <i class="fas fa-plus me-2"></i>Add New Farmer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Farmers Table Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="farmersTable">
<thead style="background-color: #343a40; color: black;font-weight:boldd">                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Registered</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="farmersTableBody">
                                <!-- Dynamic content will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Farmer Create/Edit Modal -->
<div class="modal fade" id="farmerModal" tabindex="-1" aria-labelledby="farmerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="farmerModalLabel">
                    <i class="fas fa-user-plus me-2"></i>Add New Farmer
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="farmerForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">
                                <i class="fas fa-user me-1"></i>Full Name
                            </label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-1"></i>Email
                            </label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">
                                <i class="fas fa-phone me-1"></i>Phone
                            </label>
                            <input type="text" class="form-control" id="phone" name="phone">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="location" class="form-label">
                                <i class="fas fa-map-marker-alt me-1"></i>Location
                            </label>
                            <input type="text" class="form-control" id="location" name="location">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1"></i>Password
                            </label>
                            <input type="password" class="form-control" id="password" name="password">
                            <div class="form-text">Leave blank to keep current password (for edit)</div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-lock me-1"></i>Confirm Password
                            </label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success" id="submitBtn">
                        <i class="fas fa-save me-1"></i>Save Farmer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Farmer View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">
                    <i class="fas fa-user me-2"></i>Farmer Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewModalBody">
                <div class="text-center">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading farmer details...</p>
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
                    <i class="fas fa-user-times fa-3x text-danger mb-3"></i>
                    <h5>Are you sure you want to delete this farmer?</h5>
                    <p class="text-muted">This action cannot be undone and will also delete all associated farms.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-1"></i>Delete Farmer
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
let currentFarmerId = null;

// Add CSRF token to all requests
function getCSRFToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.getAttribute('content') : '';
}

// Load farmers on page load
document.addEventListener('DOMContentLoaded', function() {
    loadFarmers();

    // Add CSRF token to meta tag if it doesn't exist
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const meta = document.createElement('meta');
        meta.name = 'csrf-token';
        meta.content = '{{ csrf_token() }}';
        document.getElementsByTagName('head')[0].appendChild(meta);
    }
});

function loadFarmers() {
    fetch('/admin/farmers', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.farmers) {
            renderFarmersTable(data.farmers);
        }
    })
    .catch(error => {
        console.error('Error loading farmers:', error);
        Swal.fire('Error', 'Failed to load farmers', 'error');
    });
}

function renderFarmersTable(farmers) {
    const tbody = document.getElementById('farmersTableBody');
    tbody.innerHTML = '';

    if (farmers.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    <i class="fas fa-seedling fa-2x mb-2"></i>
                    <br>No farmers found. Add your first farmer to get started.
                </td>
            </tr>
        `;
        return;
    }

    farmers.forEach(farmer => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <div class="d-flex align-items-center">

                    <div>
                        <div class="fw-bold">${farmer.name || 'N/A'}</div>
                        <small class="text-muted">${farmer.username || 'N/A'}</small>
                    </div>
                </div>
            </td>
            <td>${farmer.email || 'N/A'}</td>
            <td>${farmer.phone || 'N/A'}</td>
            <td>${farmer.location || 'N/A'}</td>
            <td>
                <span class="badge status-${farmer.status} rounded-pill">
                    <i class="fas fa-circle me-1" style="font-size: 0.6em;"></i>
                    ${farmer.status ? farmer.status.charAt(0).toUpperCase() + farmer.status.slice(1) : 'N/A'}
                </span>
            </td>
            <td>
                <small>${new Date(farmer.created_at).toLocaleDateString()}</small>
            </td>
            <td>
                <div class="btn-group" role="group" aria-label="Farmer actions">
                    <button type="button" class="btn btn-outline-info btn-sm"
                            onclick="viewFarmer(${farmer.id})"
                            data-bs-toggle="modal"
                            data-bs-target="#viewModal"
                            title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-outline-warning btn-sm"
                            onclick="editFarmer(${farmer.id})"
                            data-bs-toggle="modal"
                            data-bs-target="#farmerModal"
                            title="Edit Farmer">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm"
                            onclick="confirmDelete(${farmer.id})"
                            title="Delete Farmer">
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
    currentFarmerId = null;

    // Reset modal
    document.getElementById('farmerModalLabel').innerHTML = '<i class="fas fa-user-plus me-2"></i>Add New Farmer';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save me-1"></i>Save Farmer';
    document.getElementById('farmerForm').reset();

    // Reset validation states
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

    // Set password as required for new farmer
    document.getElementById('password').required = true;
    document.getElementById('password_confirmation').required = true;
}

function editFarmer(farmerId) {
    if (!farmerId) {
        Swal.fire('Error', 'Invalid farmer ID', 'error');
        return;
    }

    isEditing = true;
    currentFarmerId = farmerId;

    // Update modal title
    document.getElementById('farmerModalLabel').innerHTML = '<i class="fas fa-user-edit me-2"></i>Edit Farmer';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save me-1"></i>Update Farmer';

    // Password not required for edit
    document.getElementById('password').required = false;
    document.getElementById('password_confirmation').required = false;

    // Show loading
    Swal.fire({
        title: 'Loading...',
        text: 'Fetching farmer data',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => Swal.showLoading()
    });

    // Fetch farmer data
    fetch(`/admin/farmers/${farmerId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
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
            document.getElementById('name').value = data.name || '';
            document.getElementById('email').value = data.email || '';
            document.getElementById('phone').value = data.phone || '';
            document.getElementById('location').value = data.location || '';
        } else {
            throw new Error('Farmer data not found');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Failed to load farmer data: ' + error.message, 'error');
    });
}

function viewFarmer(farmerId) {
    if (!farmerId) {
        Swal.fire('Error', 'Invalid farmer ID', 'error');
        return;
    }

    // Reset modal content with loading state
    document.getElementById('viewModalBody').innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading farmer details...</p>
        </div>
    `;

    fetch(`/admin/farmers/${farmerId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
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
            document.getElementById('viewModalBody').innerHTML = `
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-success">Personal Information</h6>
                                <p><strong>Name:</strong> ${data.name || 'N/A'}</p>
                                <p><strong>Username:</strong> ${data.username || 'N/A'}</p>
                                <p><strong>Email:</strong> ${data.email || 'N/A'}</p>
                                <p><strong>Phone:</strong> ${data.phone || 'N/A'}</p>
                                <p><strong>Location:</strong> ${data.location || 'N/A'}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-success">Account Information</h6>
                                <p><strong>Status:</strong>
                                    <span class="badge bg-${data.status === 'active' ? 'success' : (data.status === 'inactive' ? 'warning' : 'danger')}">
                                        ${data.status ? data.status.charAt(0).toUpperCase() + data.status.slice(1) : 'N/A'}
                                    </span>
                                </p>
                                <p><strong>Registered:</strong> ${new Date(data.created_at).toLocaleDateString()}</p>
                                <p><strong>Last Updated:</strong> ${new Date(data.updated_at).toLocaleDateString()}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else {
            throw new Error('Farmer data not found');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('viewModalBody').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Error loading farmer data: ${error.message}
            </div>
        `;
    });
}

function confirmDelete(farmerId) {
    if (!farmerId) {
        Swal.fire('Error', 'Invalid farmer ID', 'error');
        return;
    }

    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();

    document.getElementById('confirmDeleteBtn').onclick = function() {
        deleteFarmer(farmerId);
        deleteModal.hide();
    };
}

function deleteFarmer(farmerId) {
    Swal.fire({
        title: 'Deleting...',
        text: 'Please wait while we delete the farmer.',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => Swal.showLoading()
    });

    fetch(`/admin/farmers/${farmerId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': getCSRFToken(),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
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
                text: 'Farmer has been deleted successfully.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                loadFarmers(); // Reload the table
            });
        } else {
            throw new Error(data.message || 'Failed to delete farmer');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: error.message || 'An unexpected error occurred while deleting the farmer.'
        });
    });
}

// Form submission
document.getElementById('farmerForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Clear previous validation states
    this.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

    // Show loading state
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving...';
    submitBtn.disabled = true;

    const formData = new FormData(this);
    const url = isEditing ? `/admin/farmers/${currentFarmerId}` : '/admin/farmers';

    if (isEditing) {
        formData.append('_method', 'PUT');
    }

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': getCSRFToken(),
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || `HTTP error! status: ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;

        if (data.success !== false) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: isEditing ? 'Farmer updated successfully.' : 'Farmer created successfully.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                // Close modal and reload table
                bootstrap.Modal.getInstance(document.getElementById('farmerModal')).hide();
                loadFarmers();
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
            }
            throw new Error(data.message || 'Validation failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: error.message || 'An unexpected error occurred while saving the farmer.'
        });
    });
});
</script>
@endsection
