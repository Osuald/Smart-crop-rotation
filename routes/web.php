<?php

use App\Models\Farm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController as AdminUserController;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Authentication Routes
Route::get('/login', function() {
    return view('login');
})->name('login');

Route::post('/login', function(Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        // Update last login time
        $user = Auth::user();
        $user->last_login = now();
        $user->save();

        // Flash success message
        $request->session()->flash('success', 'Login successful! Welcome back, ' . $user->name);

        // Redirect based on role
        if ($user->role_id == 4) { // System Admin
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('farmer.dashboard');
        }
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
})->name('login.process');

// Registration Routes
Route::get('/register', function() {
    return view('register');
})->name('register');

Route::post('/register', function(Request $request) {
    $validated = $request->validate([
        'username' => ['required', 'string', 'max:255', 'unique:users'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'phone' => ['nullable', 'string', 'max:20'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    // Get farmer role (ID 1)
    $farmerRoleId = 1;

    $user = User::create([
        'name' => $validated['username'],
        'username' => $validated['username'],
        'email' => $validated['email'],
        'phone' => $validated['phone'] ?? null,
        'password' => Hash::make($validated['password']),
        'role_id' => $farmerRoleId,
        'status' => 'active',
    ]);

    Auth::login($user);

    return redirect()->route('farmer.dashboard');
})->name('register.process');

Route::post('/logout', function(Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
})->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Farmer dashboard route
    Route::get('/farmer/dashboard', function () {
        if (Auth::user()->role_id != 1) {
            return redirect()->route('admin.dashboard');
        }
        return view('farmer.dashboard');
    })->name('farmer.dashboard');

    // Farmer Soil Management Routes - Remove duplicate middleware
    Route::prefix('farmer')->name('farmer.')->group(function () {
        Route::middleware('farmer')->group(function () {
            Route::get('/soil', [App\Http\Controllers\Farmer\SoilDataController::class, 'index'])->name('soil');
            Route::get('/soil/live-data', [App\Http\Controllers\Farmer\SoilDataController::class, 'liveData'])->name('soil.live');
            Route::get('/soil/recommendations', [App\Http\Controllers\Farmer\SoilDataController::class, 'recommendations'])->name('soil.recommendations');
            Route::get('/soil/history', [App\Http\Controllers\Farmer\SoilDataController::class, 'history'])->name('soil.history');
            Route::get('/soil/analytics', [App\Http\Controllers\Farmer\SoilDataController::class, 'analytics'])->name('soil.analytics');
            Route::get('/soil/filters', [App\Http\Controllers\Farmer\SoilDataController::class, 'getFilters'])->name('soil.filters');

            // Manual soil data input for farmers
            Route::get('/soil/manual-input', [App\Http\Controllers\Farmer\SoilDataController::class, 'manualInput'])->name('soil.manual-input');
            Route::post('/soil/manual-input', [App\Http\Controllers\Farmer\SoilDataController::class, 'storeManualData'])->name('soil.store-manual');
            Route::get('/soil/analysis-results/{soil_data}', [App\Http\Controllers\Farmer\SoilDataController::class, 'analysisResults'])->name('soil.analysis-results');
            Route::post('/soil/crop-history', [App\Http\Controllers\Farmer\SoilDataController::class, 'getCropHistory'])->name('soil.crop-history');

            // Generate simulated data
            Route::post('/soil/generate-demo-data', [App\Http\Controllers\Farmer\SoilDataController::class, 'generateDemoData'])->name('soil.generate-demo');
        });
    });

    // Admin routes - using proper middleware alias
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        // Admin Dashboard
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // User management routes
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        // User permission management routes
        Route::get('/users/{user}/permissions', [AdminUserController::class, 'permissions'])->name('users.permissions');
        Route::put('/users/{user}/permissions', [AdminUserController::class, 'updatePermissions'])->name('users.permissions.update');
        Route::post('/users/{user}/permissions/grant', [AdminUserController::class, 'grantPermission'])->name('users.permissions.grant');
        Route::delete('/users/{user}/permissions/revoke', [AdminUserController::class, 'revokePermission'])->name('users.permissions.revoke');

        // Farmer Management
        Route::get('/farmers', [App\Http\Controllers\Admin\FarmerController::class, 'index'])->name('farmers');
        Route::post('/farmers', [App\Http\Controllers\Admin\FarmerController::class, 'store'])->name('farmers.store');
        Route::get('/farmers/{id}', [App\Http\Controllers\Admin\FarmerController::class, 'show'])->name('farmers.show');
        Route::put('/farmers/{id}', [App\Http\Controllers\Admin\FarmerController::class, 'update'])->name('farmers.update');
        Route::delete('/farmers/{id}', [App\Http\Controllers\Admin\FarmerController::class, 'destroy'])->name('farmers.destroy');

        // Device Management
        Route::get('/devices', [App\Http\Controllers\Admin\DeviceController::class, 'index'])->name('devices');
        Route::post('/devices', [App\Http\Controllers\Admin\DeviceController::class, 'store'])->name('devices.store');
        Route::get('/devices/{id}', [App\Http\Controllers\Admin\DeviceController::class, 'show'])->name('devices.show');
        Route::put('/devices/{id}', [App\Http\Controllers\Admin\DeviceController::class, 'update'])->name('devices.update');
        Route::delete('/devices/{id}', [App\Http\Controllers\Admin\DeviceController::class, 'destroy'])->name('devices.destroy');
        Route::patch('/devices/{id}/status', [App\Http\Controllers\Admin\DeviceController::class, 'updateStatus'])->name('devices.status');
        Route::get('/devices/user/{user_id}/farms', [App\Http\Controllers\Admin\DeviceController::class, 'getFarmsForUser'])->name('devices.user-farms');
        Route::patch('/devices/bulk-status', [App\Http\Controllers\Admin\DeviceController::class, 'bulkUpdateStatus'])->name('devices.bulk-status');

        // Reports
        Route::get('/reports', function () {
            return view('admin.reports');
        })->name('reports');

        // Crop Database
        Route::get('/crops', function () {
            return view('admin.crops');
        })->name('crops');

        // System Settings
        Route::get('/settings', function () {
            return view('admin.settings');
        })->name('settings');

        // Role management - Using proper resource route with explicit controller
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        Route::get('/roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
        Route::put('/roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');

        // Soil Management
        Route::get('/soil', [App\Http\Controllers\Admin\SoilDataController::class, 'index'])->name('soil');
        Route::get('/soil/live-data', [App\Http\Controllers\Admin\SoilDataController::class, 'liveData'])->name('soil.live');
        Route::get('/soil/recommendations', [App\Http\Controllers\Admin\SoilDataController::class, 'recommendations'])->name('soil.recommendations');
        Route::get('/soil/history', [App\Http\Controllers\Admin\SoilDataController::class, 'history'])->name('soil.history');
        Route::get('/soil/analytics', [App\Http\Controllers\Admin\SoilDataController::class, 'analytics'])->name('soil.analytics');
        Route::get('/soil/filters', [App\Http\Controllers\Admin\SoilDataController::class, 'getFilters'])->name('soil.filters');
        Route::post('/soil/{soilData}/generate-recommendations', [App\Http\Controllers\Admin\SoilDataController::class, 'generateRecommendations'])->name('soil.generate-recommendations');

        // Generate demo data
        Route::post('/soil/generate-demo-data', [App\Http\Controllers\Admin\SoilDataController::class, 'generateDemoData'])->name('soil.generate-demo');

        // Manual soil data input routes
        Route::get('/soil/manual-input', [App\Http\Controllers\Admin\SoilDataController::class, 'manualInput'])->name('soil.manual-input');
        Route::post('/soil/manual-input', [App\Http\Controllers\Admin\SoilDataController::class, 'storeManualData'])->name('soil.store-manual');
        Route::get('/soil/analysis-results/{soil_data}', [App\Http\Controllers\Admin\SoilDataController::class, 'analysisResults'])->name('soil.analysis-results');
        Route::post('/soil/crop-history', [App\Http\Controllers\Admin\SoilDataController::class, 'getCropHistory'])->name('soil.crop-history');
    });
});
