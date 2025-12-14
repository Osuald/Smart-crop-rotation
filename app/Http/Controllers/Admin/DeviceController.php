<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        try {
            $devices = Device::with('user')->get();
            $users = User::all();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'devices' => $devices,
                    'users' => $users
                ]);
            }

            return view('admin.device_management', compact('devices', 'users'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading devices: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error loading devices: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $users = User::all();
        return view('admin.devices.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_serial_number' => 'required|string|unique:devices,device_serial_number|max:255',
            'device_name' => 'required|string|max:255',
            'device_type' => 'required|string|max:100',
            'user_id' => 'required|exists:users,id',
            'installation_location' => 'required|string|max:255',
            'farm_upi' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive,maintenance,offline',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'firmware_version' => 'nullable|string|max:50',
            'battery_level' => 'nullable|integer|between:0,100',
            'sensor_types' => 'nullable|string',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $deviceData = $request->all();

            // Handle sensor types
            if ($request->has('sensor_types')) {
                $sensorTypes = json_decode($request->sensor_types, true);
                $deviceData['sensor_types'] = $sensorTypes ?: [];
            } else {
                $deviceData['sensor_types'] = [];
            }

            // Set installation date
            $deviceData['installed_at'] = now();

            $device = Device::create($deviceData);

            return response()->json([
                'success' => true,
                'message' => 'Device created successfully',
                'device' => $device->load('user')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating device: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $device = Device::with('user')->findOrFail($id);

            return response()->json($device);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found'
            ], 404);
        }
    }

    public function edit($id)
    {
        $device = Device::findOrFail($id);
        $users = User::all();
        return view('admin.devices.edit', compact('device', 'users'));
    }

    public function update(Request $request, $id)
    {
        $device = Device::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'device_serial_number' => 'required|string|unique:devices,device_serial_number,' . $id . '|max:255',
            'device_name' => 'required|string|max:255',
            'device_type' => 'required|string|max:100',
            'user_id' => 'required|exists:users,id',
            'installation_location' => 'required|string|max:255',
            'farm_upi' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive,maintenance,offline',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'firmware_version' => 'nullable|string|max:50',
            'battery_level' => 'nullable|integer|between:0,100',
            'sensor_types' => 'nullable|string',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $deviceData = $request->all();

            // Handle sensor types
            if ($request->has('sensor_types')) {
                $sensorTypes = json_decode($request->sensor_types, true);
                $deviceData['sensor_types'] = $sensorTypes ?: [];
            }

            $device->update($deviceData);

            return response()->json([
                'success' => true,
                'message' => 'Device updated successfully',
                'device' => $device->load('user')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating device: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $device = Device::findOrFail($id);
            $device->delete();

            return response()->json([
                'success' => true,
                'message' => 'Device deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting device: ' . $e->getMessage()
            ], 500);
        }
    }
}
