@extends('layouts.admin')

@section('title', 'Role Management - Admin Dashboard')

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

    .permission-category {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        margin-bottom: 15px;
        background: #f8f9fa;
    }

    .permission-category-header {
        background: #e9ecef;
        padding: 10px 15px;
        border-radius: 8px 8px 0 0;
        font-weight: 600;
        border-bottom: 1px solid #dee2e6;
    }

    .permission-list {
        padding: 15px;
        max-height: 200px;
        overflow-y: auto;
    }

    .badge.bg-success {
        background-color: #198754 !important;
    }

    .badge.bg-secondary {
        background-color: #6c757d !important;
    }

    .btn-group .btn {
        margin: 0 2px;
    }

    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    .role-card {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .role-card:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transform: translateY(-2px);
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
                    <h3 class="mb-0"><i class="fas fa-user-tag me-2"></i>Role Management</h3>
                    <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#roleModal" onclick="openCreateModal()">
                        <i class="fas fa-plus me-2"></i>Add New Role
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles Grid -->
    <div class="row">
        @foreach($roles as $role)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card role-card h-100">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">{{ $role->name }}</h6>
                        <span class="badge bg-primary">{{ $role->users_count ?? $role->users->count() }} users</span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">{{ $role->description ?: 'No description available' }}</p>

                    <div class="mb-3">
                        <small class="text-muted fw-bold">Permissions ({{ $role->permissions->count() }}):</small>
                        <div class="mt-2" style="max-height: 100px; overflow-y: auto;">
                            @if($role->permissions->count() > 0)
                                @foreach($role->permissions->take(5) as $permission)
                                    <span class="badge bg-secondary me-1 mb-1 small">{{ $permission->name }}</span>
                                @endforeach
                                @if($role->permissions->count() > 5)
                                    <span class="badge bg-info">+{{ $role->permissions->count() - 5 }} more</span>
                                @endif
                            @else
                                <span class="text-muted small">No permissions assigned</span>
                            @endif
                        </div>
                    </div>

                    <small class="text-muted">Created: {{ $role->created_at->format('M d, Y') }}</small>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="btn-group w-100" role="group">
                        <button type="button" class="btn btn-outline-info btn-sm"
                                onclick="viewRole({{ $role->id }})"
                                data-bs-toggle="modal"
                                data-bs-target="#viewModal"
                                title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-sm"
                                onclick="editRole({{ $role->id }})"
                                data-bs-toggle="modal"
                                data-bs-target="#roleModal"
                                title="Edit Role">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm"
                                onclick="managePermissions({{ $role->id }})"
                                data-bs-toggle="modal"
                                data-bs-target="#permissionsModal"
                                title="Manage Permissions">
                            <i class="fas fa-key"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm"
                                onclick="confirmDelete({{ $role->id }})"
                                title="Delete Role">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Role Create/Edit Modal -->
<div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleModalLabel">
                    <i class="fas fa-user-tag me-2"></i>Add New Role
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="roleForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="name" class="form-label">
                                <i class="fas fa-tag me-1"></i>Role Name
                            </label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left me-1"></i>Description
                            </label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Describe the role's purpose and responsibilities"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">
                                <i class="fas fa-key me-1"></i>Permissions
                            </label>
                            <div id="permissionsContainer" style="max-height: 300px; overflow-y: auto;">
                                @foreach($permissions as $category => $categoryPermissions)
                                <div class="permission-category">
                                    <div class="permission-category-header">
                                        <div class="form-check">
                                            <input class="form-check-input category-checkbox" type="checkbox"
                                                   id="category_{{ $loop->index }}"
                                                   onchange="toggleCategory(this, '{{ $category }}')">
                                            <label class="form-check-label fw-bold" for="category_{{ $loop->index }}">
                                                {{ ucfirst($category) }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="permission-list">
                                        @foreach($categoryPermissions as $permission)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input permission-checkbox"
                                                   type="checkbox"
                                                   name="permissions[]"
                                                   value="{{ $permission->id }}"
                                                   id="perm_{{ $permission->id }}"
                                                   data-category="{{ $category }}"
                                                   onchange="updateCategoryCheckbox('{{ $category }}')">
                                            <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                {{ $permission->name }}
                                                @if($permission->description)
                                                    <small class="text-muted d-block">{{ $permission->description }}</small>
                                                @endif
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success" id="submitBtn">
                        <i class="fas fa-save me-1"></i>Save Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Role View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">
                    <i class="fas fa-user-tag me-2"></i>Role Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewModalBody">
                <div class="text-center">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading role details...</p>
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
                    <h5>Are you sure you want to delete this role?</h5>
                    <p class="text-muted">This action cannot be undone.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-1"></i>Delete Role
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Permissions Management Modal -->
<div class="modal fade" id="permissionsModal" tabindex="-1" aria-labelledby="permissionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="permissionsModalLabel">
                    <i class="fas fa-key me-2"></i>Manage Role Permissions
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="permissionsModalBody">
                <div class="text-center">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading permissions...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
                <button type="button" class="btn btn-success" id="savePermissionsBtn">
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
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let isEditing = false;
let currentRoleId = null;
let currentPermissionRoleId = null;

// Add CSRF token to all requests
function getCSRFToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.getAttribute('content') : '';
}

function openCreateModal() {
    isEditing = false;
    currentRoleId = null;

    // Reset modal
    document.getElementById('roleModalLabel').innerHTML = '<i class="fas fa-user-tag me-2"></i>Add New Role';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save me-1"></i>Save Role';
    document.getElementById('roleForm').reset();

    // Reset all checkboxes
    document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
    document.querySelectorAll('.category-checkbox').forEach(cb => cb.checked = false);

    // Reset validation states
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
}

function editRole(roleId) {
    if (!roleId) {
        Swal.fire('Error', 'Invalid role ID', 'error');
        return;
    }

    isEditing = true;
    currentRoleId = roleId;

    // Update modal title
    document.getElementById('roleModalLabel').innerHTML = '<i class="fas fa-user-edit me-2"></i>Edit Role';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save me-1"></i>Update Role';

    // Show loading
    Swal.fire({
        title: 'Loading...',
        text: 'Fetching role data',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => Swal.showLoading()
    });

    // Fetch role data
    fetch(`/admin/roles/${roleId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': getCSRFToken(),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();

        if (data.role) {
            const role = data.role;
            document.getElementById('name').value = role.name || '';
            document.getElementById('description').value = role.description || '';

            // Reset all checkboxes first
            document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
            document.querySelectorAll('.category-checkbox').forEach(cb => cb.checked = false);

            // Check role permissions
            if (role.permissions) {
                role.permissions.forEach(permission => {
                    const checkbox = document.querySelector(`input[value="${permission.id}"]`);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });

                // Update category checkboxes
                const categories = [...new Set(role.permissions.map(p => p.category))];
                categories.forEach(category => {
                    updateCategoryCheckbox(category);
                });
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Failed to load role data', 'error');
    });
}

function toggleCategory(categoryCheckbox, category) {
    const categoryPermissions = document.querySelectorAll(`input[data-category="${category}"]`);
    categoryPermissions.forEach(cb => {
        cb.checked = categoryCheckbox.checked;
    });
}

function updateCategoryCheckbox(category) {
    const categoryPermissions = document.querySelectorAll(`input[data-category="${category}"]`);
    const checkedPermissions = document.querySelectorAll(`input[data-category="${category}"]:checked`);
    const categoryCheckbox = document.querySelector(`input[onchange*="${category}"]`);

    if (categoryCheckbox) {
        if (checkedPermissions.length === categoryPermissions.length) {
            categoryCheckbox.checked = true;
            categoryCheckbox.indeterminate = false;
        } else if (checkedPermissions.length > 0) {
            categoryCheckbox.checked = false;
            categoryCheckbox.indeterminate = true;
        } else {
            categoryCheckbox.checked = false;
            categoryCheckbox.indeterminate = false;
        }
    }
}

// Form submission
document.getElementById('roleForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Clear previous validation states
    this.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

    // Show loading state
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving...';
    submitBtn.disabled = true;

    const formData = new FormData(this);
    const url = isEditing ? `/admin/roles/${currentRoleId}` : '/admin/roles';

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
    .then(response => response.json())
    .then(data => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;

        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
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
        Swal.fire('Error', error.message || 'An error occurred', 'error');
    });
});

function confirmDelete(roleId) {
    if (!roleId) {
        Swal.fire('Error', 'Invalid role ID', 'error');
        return;
    }

    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();

    document.getElementById('confirmDeleteBtn').onclick = function() {
        deleteRole(roleId);
        deleteModal.hide();
    };
}

function deleteRole(roleId) {
    Swal.fire({
        title: 'Deleting...',
        text: 'Please wait while we delete the role.',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => Swal.showLoading()
    });

    fetch(`/admin/roles/${roleId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': getCSRFToken(),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            throw new Error(data.message || 'Failed to delete role');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', error.message || 'An error occurred', 'error');
    });
}

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', function() {
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
