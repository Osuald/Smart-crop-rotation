@extends('layouts.admin')

@section('title', 'User Management - Admin Dashboard')

@section('styles')
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<!-- Custom styles -->
<style>
    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .modal-header .btn-close {
        filter: invert(1);
    }

    .permission-badge {
        margin: 2px;
        font-size: 0.8em;
    }

    .permission-badge[title]:hover {
        transform: scale(1.05);
        transition: transform 0.2s ease;
    }

    .badge.bg-primary {
        background-color: #0d6efd !important;
    }

    .badge.bg-success {
        background-color: #198754 !important;
    }

    .badge.bg-secondary {
        background-color: #6c757d !important;
    }

    .badge.bg-info {
        background-color: #0dcaf0 !important;
    }

    /* Tooltip styling for better visibility */
    .permission-badge {
        cursor: help;
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
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="mb-0"><i class="fas fa-users me-2"></i>User Management</h3>
                    <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#userModal" onclick="openCreateModal()">
                        <i class="fas fa-plus me-2"></i>Add New User
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th><i class="fas fa-user me-1"></i>Name</th>
                                    <th><i class="fas fa-envelope me-1"></i>Email</th>
                                    <th><i class="fas fa-user-tag me-1"></i>Role</th>
                                    <th><i class="fas fa-key me-1"></i>Permissions</th>
                                    <th><i class="fas fa-circle me-1"></i>Status</th>
                                    <th><i class="fas fa-calendar me-1"></i>Registered</th>
                                    <th><i class="fas fa-cogs me-1"></i>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $user->name ?? $user->username }}</div>
                                                <small class="text-muted">{{ $user->username }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-primary rounded-pill">{{ $user->role->name ?? 'No Role' }}</span>
                                    </td>
                                    <td>
                                        @if($user->role && $user->role->permissions->count() > 0)
                                            @foreach($user->role->permissions->take(2) as $permission)
                                                <span class="badge bg-secondary permission-badge">{{ $permission->name }}</span>
                                            @endforeach
                                            @if($user->role->permissions->count() > 2)
                                                <span class="badge bg-info permission-badge">+{{ $user->role->permissions->count() - 2 }}</span>
                                            @endif
                                        @else
                                            <span class="text-muted small">No permissions</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge status-{{ $user->status }} rounded-pill">
                                            <i class="fas fa-circle me-1" style="font-size: 0.6em;"></i>
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ $user->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="User actions">
                                            <button type="button" class="btn btn-outline-info btn-sm"
                                                    onclick="viewUser({{ $user->id }})"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#viewModal"
                                                    title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-warning btn-sm"
                                                    onclick="editUser({{ $user->id }})"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#userModal"
                                                    title="Edit User">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-success btn-sm"
                                                    onclick="managePermissions({{ $user->id }})"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#permissionsModal"
                                                    title="Manage Permissions">
                                                <i class="fas fa-key"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                    onclick="confirmDelete({{ $user->id }})"
                                                    title="Delete User">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- User Create/Edit Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">
                    <i class="fas fa-user-plus me-2"></i>Add New User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userForm">
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
                            <label for="username" class="form-label">
                                <i class="fas fa-at me-1"></i>Username
                            </label>
                            <input type="text" class="form-control" id="username" name="username" required>
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
                            <label for="role_id" class="form-label">
                                <i class="fas fa-user-tag me-1"></i>Role
                            </label>
                            <select class="form-select" id="role_id" name="role_id" required onchange="updatePermissions()">
                                <option value="">Select Role</option>
                                @if(isset($roles))
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" data-permissions="{{ $role->permissions->pluck('name')->toJson() }}">
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">
                                <i class="fas fa-circle me-1"></i>Status
                            </label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="suspended">Suspended</option>
                            </select>
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
                        <div class="col-12">
                            <label class="form-label">
                                <i class="fas fa-key me-1"></i>Role Permissions
                            </label>
                            <div id="permissionsList" class="border rounded p-3 bg-light">
                                <p class="text-muted mb-0">
                                    <i class="fas fa-info-circle me-1"></i>Select a role to view permissions
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save me-1"></i>Save User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- User View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">
                    <i class="fas fa-user me-2"></i>User Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewModalBody">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading user details...</p>
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
                    <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                    <h5>Are you sure you want to delete this user?</h5>
                    <p class="text-muted">This action cannot be undone.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-1"></i>Delete User
                </button>
            </div>
        </div>
    </div>
</div>

<!-- User Permissions Management Modal -->
<div class="modal fade" id="permissionsModal" tabindex="-1" aria-labelledby="permissionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="permissionsModalLabel">
                    <i class="fas fa-key me-2"></i>Manage User Permissions
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="permissionsModalBody">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading permissions...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
                <button type="button" class="btn btn-primary" id="savePermissionsBtn">
                    <i class="fas fa-save me-1"></i>Save Permissions
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
let currentUserId = null;
let currentPermissionUserId = null;

// Add CSRF token to all requests
function getCSRFToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.getAttribute('content') : '';
}

function openCreateModal() {
    isEditing = false;
    currentUserId = null;

    // Reset modal
    document.getElementById('userModalLabel').innerHTML = '<i class="fas fa-user-plus me-2"></i>Add New User';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save me-1"></i>Save User';
    document.getElementById('userForm').reset();
    document.getElementById('permissionsList').innerHTML = '<p class="text-muted mb-0"><i class="fas fa-info-circle me-1"></i>Select a role to view permissions</p>';

    // Reset validation states
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

    // Set password as required for new user
    document.getElementById('password').required = true;
    document.getElementById('password_confirmation').required = true;
}

function editUser(userId) {
    if (!userId) {
        Swal.fire('Error', 'Invalid user ID', 'error');
        return;
    }

    isEditing = true;
    currentUserId = userId;

    // Update modal title
    document.getElementById('userModalLabel').innerHTML = '<i class="fas fa-user-edit me-2"></i>Edit User';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save me-1"></i>Update User';

    // Password not required for edit
    document.getElementById('password').required = false;
    document.getElementById('password_confirmation').required = false;

    // Show loading in modal
    Swal.fire({
        title: 'Loading...',
        text: 'Fetching user data',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => Swal.showLoading()
    });

    // Fetch user data
    fetch(`/admin/users/${userId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': getCSRFToken(),
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
        Swal.close();

        if (data.user) {
            const user = data.user;
            document.getElementById('name').value = user.name || '';
            document.getElementById('username').value = user.username || '';
            document.getElementById('email').value = user.email || '';
            document.getElementById('phone').value = user.phone || '';
            document.getElementById('role_id').value = user.role_id || '';
            document.getElementById('status').value = user.status || 'active';
            updatePermissions();
        } else {
            throw new Error('User data not found');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Failed to load user data: ' + error.message, 'error');
    });
}

function viewUser(userId) {
    if (!userId) {
        Swal.fire('Error', 'Invalid user ID', 'error');
        return;
    }

    // Reset modal content with loading state
    document.getElementById('viewModalBody').innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading user details...</p>
        </div>
    `;

    fetch(`/admin/users/${userId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': getCSRFToken(),
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
        if (data.user) {
            const user = data.user;
            const permissions = user.role && user.role.permissions ? user.role.permissions : [];

            document.getElementById('viewModalBody').innerHTML = `
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary">Personal Information</h6>
                                <p><strong>Name:</strong> ${user.name || 'N/A'}</p>
                                <p><strong>Username:</strong> ${user.username || 'N/A'}</p>
                                <p><strong>Email:</strong> ${user.email || 'N/A'}</p>
                                <p><strong>Phone:</strong> ${user.phone || 'N/A'}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary">Account Information</h6>
                                <p><strong>Role:</strong> ${user.role ? user.role.name : 'No Role'}</p>
                                <p><strong>Status:</strong> <span class="badge bg-${user.status === 'active' ? 'success' : (user.status === 'inactive' ? 'warning' : 'danger')}">${user.status ? user.status.charAt(0).toUpperCase() + user.status.slice(1) : 'N/A'}</span></p>
                                <p><strong>Created:</strong> ${new Date(user.created_at).toLocaleDateString()}</p>
                                <p><strong>Updated:</strong> ${new Date(user.updated_at).toLocaleDateString()}</p>
                            </div>
                        </div>
                        <hr>
                        <div>
                            <h6 class="text-primary">Permissions</h6>
                            <div class="mt-2">
                                ${permissions.length > 0 ?
                                    permissions.map(permission => `<span class="badge bg-secondary me-1 mb-1">${permission.name}</span>`).join('') :
                                    '<span class="text-muted">No permissions assigned</span>'
                                }
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else {
            throw new Error('User data not found');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('viewModalBody').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Error loading user data: ${error.message}
            </div>
        `;
    });
}

function updatePermissions() {
    const roleSelect = document.getElementById('role_id');
    const permissionsList = document.getElementById('permissionsList');

    if (!roleSelect || !permissionsList) return;

    const selectedOption = roleSelect.options[roleSelect.selectedIndex];

    if (selectedOption.value) {
        try {
            const permissions = JSON.parse(selectedOption.getAttribute('data-permissions') || '[]');
            if (permissions.length > 0) {
                permissionsList.innerHTML = `
                    <div class="d-flex flex-wrap gap-1">
                        ${permissions.map(permission =>
                            `<span class="badge bg-secondary permission-badge">${permission}</span>`
                        ).join('')}
                    </div>
                `;
            } else {
                permissionsList.innerHTML = '<p class="text-muted mb-0"><i class="fas fa-info-circle me-1"></i>No permissions assigned to this role</p>';
            }
        } catch (e) {
            permissionsList.innerHTML = '<p class="text-danger mb-0"><i class="fas fa-exclamation-triangle me-1"></i>Error loading permissions</p>';
        }
    } else {
        permissionsList.innerHTML = '<p class="text-muted mb-0"><i class="fas fa-info-circle me-1"></i>Select a role to view permissions</p>';
    }
}

function confirmDelete(userId) {
    if (!userId) {
        Swal.fire('Error', 'Invalid user ID', 'error');
        return;
    }

    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();

    document.getElementById('confirmDeleteBtn').onclick = function() {
        deleteUser(userId);
        deleteModal.hide();
    };
}

function deleteUser(userId) {
    Swal.fire({
        title: 'Deleting...',
        text: 'Please wait while we delete the user.',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => Swal.showLoading()
    });

    fetch(`/admin/users/${userId}`, {
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
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                text: data.message || 'User has been deleted successfully.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            throw new Error(data.message || 'Failed to delete user');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: error.message || 'An unexpected error occurred while deleting the user.'
        });
    });
}

// Enhanced form submission
document.getElementById('userForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Clear previous validation states
    this.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

    // Show loading state
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving...';
    submitBtn.disabled = true;

    const formData = new FormData(this);
    const url = isEditing ? `/admin/users/${currentUserId}` : '/admin/users';

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

        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message || 'User saved successfully.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
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
            text: error.message || 'An unexpected error occurred while saving the user.'
        });
    });
});

// Manage permissions
function managePermissions(userId) {
    if (!userId) {
        Swal.fire('Error', 'Invalid user ID', 'error');
        return;
    }

    currentPermissionUserId = userId;

    // Reset modal content with loading state
    document.getElementById('permissionsModalBody').innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading permissions...</p>
        </div>
    `;

    fetch(`/admin/users/${userId}/permissions`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': getCSRFToken(),
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
        if (data.user && data.permissions) {
            renderPermissionsModal(data.user, data.permissions);
        } else {
            throw new Error('Permission data not found');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('permissionsModalBody').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Error loading permissions: ${error.message}
            </div>
        `;
    });
}

function renderPermissionsModal(user, permissions) {
    const rolePermissions = user.role && user.role.permissions ? user.role.permissions : [];
    const userPermissions = user.permissions || [];

    let html = `
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="alert alert-info">
                        <strong><i class="fas fa-user me-1"></i>User:</strong> ${user.name || user.username}
                        <span class="badge bg-primary ms-2">${user.role ? user.role.name : 'No Role'}</span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary"><i class="fas fa-user-tag me-1"></i>Role Permissions</h6>
                    <div class="border rounded p-3 mb-3" style="max-height: 300px; overflow-y: auto;">
    `;

    if (rolePermissions.length > 0) {
        rolePermissions.forEach(permission => {
            html += `
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" disabled checked>
                    <label class="form-check-label text-muted">
                        <i class="fas fa-lock me-1"></i>${permission.name}
                        <small class="text-muted d-block">${permission.description || ''}</small>
                    </label>
                </div>
            `;
        });
    } else {
        html += '<p class="text-muted">No role permissions assigned</p>';
    }

    html += `
                    </div>
                </div>

                <div class="col-md-6">
                    <h6 class="text-success"><i class="fas fa-key me-1"></i>Additional User Permissions</h6>
                    <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                        <form id="userPermissionsForm">
    `;

    Object.keys(permissions).forEach(category => {
        html += `
            <div class="mb-3">
                <h6 class="text-secondary border-bottom pb-1">${category}</h6>
        `;

        permissions[category].forEach(permission => {
            const isRolePermission = rolePermissions.some(rp => rp.id === permission.id);
            const isUserPermission = userPermissions.some(up => up.id === permission.id);
            const isChecked = isUserPermission ? 'checked' : '';
            const isDisabled = isRolePermission ? 'disabled' : '';

            if (!isRolePermission) {
                html += `
                    <div class="form-check mb-2">
                        <input class="form-check-input user-permission" type="checkbox"
                               value="${permission.id}" id="perm_${permission.id}" ${isChecked} ${isDisabled}>
                        <label class="form-check-label" for="perm_${permission.id}">
                            ${permission.name}
                            <small class="text-muted d-block">${permission.description || ''}</small>
                        </label>
                    </div>
                `;
            }
        });

        html += '</div>';
    });

    html += `
                        </form>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <div class="alert alert-warning">
                        <small><i class="fas fa-info-circle me-1"></i>
                        <strong>Note:</strong> Role permissions (locked) are inherited from the user's role.
                        You can only grant/revoke additional permissions here.</small>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.getElementById('permissionsModalBody').innerHTML = html;
}

// Save permissions function
document.getElementById('savePermissionsBtn').addEventListener('click', function() {
    if (!currentPermissionUserId) {
        Swal.fire('Error', 'No user selected', 'error');
        return;
    }

    const checkedPermissions = document.querySelectorAll('.user-permission:checked');
    const permissionIds = Array.from(checkedPermissions).map(cb => cb.value);

    // Show loading state
    const saveBtn = this;
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving...';
    saveBtn.disabled = true;

    fetch(`/admin/users/${currentPermissionUserId}/permissions`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': getCSRFToken(),
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            permissions: permissionIds
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;

        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message || 'Permissions updated successfully.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                // Close modal and reload page
                bootstrap.Modal.getInstance(document.getElementById('permissionsModal')).hide();
                location.reload();
            });
        } else {
            throw new Error(data.message || 'Failed to update permissions');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: error.message || 'An unexpected error occurred while updating permissions.'
        });
    });
});

// Initialize tooltips when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Add CSRF token to meta tag if it doesn't exist
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const meta = document.createElement('meta');
        meta.name = 'csrf-token';
        meta.content = '{{ csrf_token() }}';
        document.getElementsByTagName('head')[0].appendChild(meta);
    }
});
</script>
@endsection
