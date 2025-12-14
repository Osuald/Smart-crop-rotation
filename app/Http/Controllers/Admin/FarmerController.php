<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class FarmerController extends Controller
{
    /**
     * Display a listing of farmers.
     */
    public function index()
    {
        try {
            $farmers = User::where('role_id', 1)->orderBy('created_at', 'desc')->get();

            if (request()->ajax()) {
                return response()->json(['farmers' => $farmers]);
            }

            return view('admin.farmer_management', compact('farmers'));
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Failed to load farmers'], 500);
            }
            return redirect()->back()->with('error', 'Failed to load farmers: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created farmer in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'nullable|string|max:20',
                'location' => 'nullable|string|max:255',
                'password' => 'required|string|min:8|confirmed',
            ]);

            // Create the farmer user (role_id = 1 for farmer)
            $farmer = User::create([
                'name' => $validated['name'],
                'username' => explode('@', $validated['email'])[0], // Generate username from email
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'location' => $validated['location'] ?? null,
                'password' => Hash::make($validated['password']),
                'role_id' => 1, // Farmer role
                'status' => 'active',
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Farmer created successfully',
                    'farmer' => $farmer
                ]);
            }

            return redirect()->route('admin.farmers')->with('success', 'Farmer added successfully');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating farmer: ' . $e->getMessage()
                ], 422);
            }
            return redirect()->back()->with('error', 'Error creating farmer: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified farmer.
     */
    public function show($id)
    {
        try {
            $farmer = User::findOrFail($id);

            if ($farmer->role_id != 1) {
                if (request()->ajax()) {
                    return response()->json(['error' => 'User is not a farmer'], 403);
                }
                return redirect()->back()->with('error', 'User is not a farmer');
            }

            if (request()->ajax()) {
                return response()->json($farmer);
            }

            return view('admin.farmer_show', compact('farmer'));

        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Farmer not found'], 404);
            }
            return redirect()->back()->with('error', 'Farmer not found');
        }
    }

    /**
     * Update the specified farmer in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $farmer = User::findOrFail($id);

            // Ensure we're updating a farmer
            if ($farmer->role_id != 1) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User is not a farmer'
                    ], 403);
                }
                return redirect()->back()->with('error', 'User is not a farmer');
            }

            $rules = [
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($farmer->id),
                ],
                'phone' => 'nullable|string|max:20',
                'location' => 'nullable|string|max:255',
            ];

            if ($request->filled('password')) {
                $rules['password'] = 'string|min:8|confirmed';
            }

            $validated = $request->validate($rules);

            // Update the farmer's details
            $farmer->name = $validated['name'];
            $farmer->email = $validated['email'];
            $farmer->phone = $validated['phone'] ?? $farmer->phone;
            $farmer->location = $validated['location'] ?? $farmer->location;

            // Update password only if provided
            if (!empty($validated['password'])) {
                $farmer->password = Hash::make($validated['password']);
            }

            $farmer->save();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Farmer updated successfully',
                    'farmer' => $farmer
                ]);
            }

            return redirect()->route('admin.farmers')->with('success', 'Farmer updated successfully');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating farmer: ' . $e->getMessage()
                ], 422);
            }
            return redirect()->back()->with('error', 'Error updating farmer: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified farmer from storage.
     */
    public function destroy($id)
    {
        try {
            $farmer = User::findOrFail($id);

            // Ensure we're deleting a farmer
            if ($farmer->role_id != 1) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User is not a farmer'
                    ], 403);
                }
                return redirect()->back()->with('error', 'User is not a farmer');
            }

            $farmer->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Farmer deleted successfully'
                ]);
            }

            return redirect()->route('admin.farmers')->with('success', 'Farmer deleted successfully');

        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting farmer: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Error deleting farmer: ' . $e->getMessage());
        }
    }
}
