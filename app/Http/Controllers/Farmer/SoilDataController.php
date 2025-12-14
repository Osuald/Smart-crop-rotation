<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Models\SoilData;
use App\Models\Device;
use App\Models\Farm;
use App\Models\SoilRecommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SoilDataController extends Controller
{
    /**
     * Display the main soil management dashboard for farmers
     */
    public function index()
    {
        return view('farmer.soil_management_farmer');
    }

    /**
     * Get live/current soil data for farmer's devices
     */
    public function liveData(Request $request)
    {
        try {
            $userId = Auth::id();

            // First check if user has devices
            $userHasDevices = Device::where('user_id', $userId)->exists();

            if (!$userHasDevices) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'season' => $this->getCurrentSeason(),
                    'message' => 'No devices found for your account. Please contact admin to set up devices.'
                ]);
            }

            $query = SoilData::with(['device', 'farm'])
                ->whereHas('device', function($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->orderBy('recorded_at', 'desc');

            // Apply filters
            if ($request->device_id) {
                $query->where('device_id', $request->device_id);
            }

            if ($request->farm_id) {
                $query->where('farm_id', $request->farm_id);
            }

            $data = $query->limit(50)->get();

            return response()->json([
                'success' => true,
                'data' => $data,
                'season' => $this->getCurrentSeason()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading live data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get soil recommendations for farmer's land
     */
    public function recommendations(Request $request)
    {
        try {
            $userId = Auth::id();

            $data = SoilRecommendation::with(['user', 'farm'])
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $data,
                'season' => $this->getCurrentSeason()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading recommendations: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get soil history data for farmer's devices
     */
    public function history(Request $request)
    {
        try {
            $userId = Auth::id();

            $query = SoilData::with(['device', 'farm'])
                ->whereHas('device', function($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->orderBy('created_at', 'desc');

            // Apply date filters
            if ($request->start_date) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->end_date) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            if ($request->season) {
                $query->where('season', $request->season);
            }

            $data = $query->paginate(50);

            return response()->json([
                'success' => true,
                'data' => $data->items()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get analytics data for farmer's soil health
     */
    public function analytics(Request $request)
    {
        try {
            $userId = Auth::id();

            // Get stats for farmer's devices and farms
            $totalDevices = Device::where('user_id', $userId)->count();
            $totalFarms = Farm::where('user_id', $userId)->count();
            $totalReadings = SoilData::whereHas('device', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->count();

            $avgHealthScore = SoilData::whereHas('device', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->avg('soil_health_score') ?? 0;

            // Health distribution for farmer's data
            $healthDistribution = SoilData::whereHas('device', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->select(
                DB::raw('
                    CASE
                        WHEN soil_health_score >= 80 THEN "excellent"
                        WHEN soil_health_score >= 60 THEN "good"
                        WHEN soil_health_score >= 40 THEN "fair"
                        ELSE "poor"
                    END as health_status
                '),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('health_status')
            ->get();

            // Trends for last 30 days
            $trends = SoilData::whereHas('device', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('recorded_at', '>=', Carbon::now()->subDays(30))
            ->select(
                DB::raw('DATE(recorded_at) as date'),
                DB::raw('AVG(ph_level) as avg_ph'),
                DB::raw('AVG(moisture_level) as avg_moisture'),
                DB::raw('AVG(temperature) as avg_temperature')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

            return response()->json([
                'success' => true,
                'stats' => [
                    'total_devices' => $totalDevices,
                    'total_farms' => $totalFarms,
                    'total_readings' => $totalReadings,
                    'avg_health_score' => round($avgHealthScore, 1)
                ],
                'health_distribution' => $healthDistribution,
                'trends' => $trends,
                'season' => $this->getCurrentSeason()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading analytics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get filters data for dropdowns
     */
    public function getFilters(Request $request)
    {
        try {
            $userId = Auth::id();

            $devices = Device::where('user_id', $userId)
                ->get(['id', 'device_name', 'device_serial_number', 'device_type']);

            $farms = Farm::where('user_id', $userId)
                ->get(['id', 'name', 'location']);

            $seasons = ['Season A', 'Season B', 'Season C'];

            return response()->json([
                'success' => true,
                'devices' => $devices,
                'farms' => $farms,
                'seasons' => $seasons
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading filters: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate demo data for testing
     */
    public function generateDemoData(Request $request)
    {
        try {
            $userId = Auth::id();

            // First ensure user has at least one farm and device
            $farm = Farm::firstOrCreate([
                'user_id' => $userId,
                'name' => 'Demo Farm'
            ], [
                'location' => 'Demo Location',
                'size' => 10.5,
                'soil_type' => 'Clay Loam',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $device = Device::firstOrCreate([
                'user_id' => $userId,
                'device_serial_number' => 'DEMO-' . $userId . '-001'
            ], [
                'device_name' => 'Demo Soil Sensor',
                'device_type' => 'Soil Sensor',
                'farm_id' => $farm->id,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Generate demo soil data
            $count = 0;
            for ($i = 0; $i < 20; $i++) {
                SoilData::create([
                    'device_id' => $device->id,
                    'farm_id' => $farm->id,
                    'ph_level' => round(rand(55, 85) / 10, 1), // 5.5 to 8.5
                    'moisture_level' => rand(20, 80),
                    'temperature' => rand(18, 35),
                    'nitrogen' => rand(10, 50),
                    'phosphorus' => rand(5, 25),
                    'potassium' => rand(15, 40),
                    'soil_health_score' => rand(40, 95),
                    'recorded_at' => Carbon::now()->subHours(rand(1, 168)), // Last week
                    'season' => 'Season A',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $count++;
            }

            return response()->json([
                'success' => true,
                'message' => "Generated {$count} demo soil readings, created 1 farm and 1 device for your account!"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating demo data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Manual soil data input for farmers
     */
    public function manualInput()
    {
        return view('farmer.soil_manual_input');
    }

    /**
     * Store manually entered soil data
     */
    public function storeManualData(Request $request)
    {
        try {
            $userId = Auth::id();

            $validated = $request->validate([
                'device_id' => 'required|exists:devices,id',
                'farm_id' => 'required|exists:farms,id',
                'ph_level' => 'required|numeric|min:0|max:14',
                'moisture_level' => 'required|numeric|min:0|max:100',
                'temperature' => 'required|numeric|min:-10|max:60',
                'nitrogen' => 'nullable|numeric|min:0',
                'phosphorus' => 'nullable|numeric|min:0',
                'potassium' => 'nullable|numeric|min:0',
                'season' => 'required|string'
            ]);

            // Verify that the device and farm belong to the user
            $device = Device::where('id', $validated['device_id'])
                           ->where('user_id', $userId)
                           ->first();

            $farm = Farm::where('id', $validated['farm_id'])
                        ->where('user_id', $userId)
                        ->first();

            if (!$device || !$farm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device or farm not found or does not belong to you.'
                ], 403);
            }

            // Calculate soil health score
            $healthScore = $this->calculateSoilHealthScore(
                $validated['ph_level'],
                $validated['moisture_level'],
                $validated['temperature'],
                $validated['nitrogen'] ?? 0,
                $validated['phosphorus'] ?? 0,
                $validated['potassium'] ?? 0
            );

            $soilData = SoilData::create([
                'device_id' => $validated['device_id'],
                'farm_id' => $validated['farm_id'],
                'ph_level' => $validated['ph_level'],
                'moisture_level' => $validated['moisture_level'],
                'temperature' => $validated['temperature'],
                'nitrogen' => $validated['nitrogen'] ?? 0,
                'phosphorus' => $validated['phosphorus'] ?? 0,
                'potassium' => $validated['potassium'] ?? 0,
                'soil_health_score' => $healthScore,
                'recorded_at' => now(),
                'season' => $validated['season']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Soil data recorded successfully!',
                'data' => $soilData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error storing soil data: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calculateSoilHealthScore($ph, $moisture, $temperature, $nitrogen, $phosphorus, $potassium)
    {
        $score = 0;

        // pH score (optimal range 6.0-7.0)
        if ($ph >= 6.0 && $ph <= 7.0) {
            $score += 25;
        } elseif ($ph >= 5.5 && $ph <= 7.5) {
            $score += 20;
        } elseif ($ph >= 5.0 && $ph <= 8.0) {
            $score += 15;
        } else {
            $score += 5;
        }

        // Moisture score (optimal range 40-60%)
        if ($moisture >= 40 && $moisture <= 60) {
            $score += 25;
        } elseif ($moisture >= 30 && $moisture <= 70) {
            $score += 20;
        } elseif ($moisture >= 20 && $moisture <= 80) {
            $score += 15;
        } else {
            $score += 5;
        }

        // Temperature score (optimal range 20-25°C)
        if ($temperature >= 20 && $temperature <= 25) {
            $score += 25;
        } elseif ($temperature >= 15 && $temperature <= 30) {
            $score += 20;
        } elseif ($temperature >= 10 && $temperature <= 35) {
            $score += 15;
        } else {
            $score += 5;
        }

        // NPK score (basic scoring)
        $npkScore = min(25, ($nitrogen + $phosphorus + $potassium) / 3);
        $score += $npkScore;

        return min(100, max(0, $score));
    }

    /**
     * Show analysis results for a specific soil data entry
     */
    public function analysisResults($soilDataId)
    {
        // Implementation for farmers
        return view('farmer.soil_analysis_results', compact('soilDataId'));
    }

    /**
     * Get crop history for a specific farm
     */
    public function getCropHistory(Request $request)
    {
        try {
            $userId = Auth::id();

            // Get crop history for farmer's farms
            $farms = Farm::where('user_id', $userId)->get();

            return response()->json([
                'success' => true,
                'data' => $farms
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting crop history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current season based on the month
     */
    private function getCurrentSeason()
    {
        $month = date('n');
        if ($month >= 3 && $month <= 5) {
            return 'Spring Season';
        } elseif ($month >= 6 && $month <= 8) {
            return 'Summer Season';
        } elseif ($month >= 9 && $month <= 11) {
            return 'Fall Season';
        } else {
            return 'Winter Season';
        }
    }
}
