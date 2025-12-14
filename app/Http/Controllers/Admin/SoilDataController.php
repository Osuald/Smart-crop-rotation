<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SoilData;
use App\Models\Device;
use App\Models\User;
use App\Models\Farm;
use App\Models\CropHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SoilDataController extends Controller
{
    public function index()
    {
        return view('admin.soil_management');
    }

    public function liveData(Request $request)
    {
        try {
            $query = SoilData::with(['device.user', 'farm'])
                ->orderBy('created_at', 'desc');

            // Apply filters
            if ($request->device_id) {
                $query->where('device_id', $request->device_id);
            }

            if ($request->user_id) {
                $query->whereHas('device', function($q) use ($request) {
                    $q->where('user_id', $request->user_id);
                });
            }

            // Get recent data (last 24 hours)
            $query->where('created_at', '>=', Carbon::now()->subDay());

            $data = $query->take(50)->get();

            // Determine current season
            $currentMonth = Carbon::now()->month;
            $season = ($currentMonth >= 9 || $currentMonth <= 2) ? 'Season A' : 'Season B';

            return response()->json([
                'success' => true,
                'data' => $data->map(function($item) {
                    return [
                        'id' => $item->id,
                        'ph_level' => $item->ph,
                        'moisture_level' => $item->moisture,
                        'temperature' => $item->temperature,
                        'nitrogen_level' => $item->nitrogen,
                        'phosphorus_level' => $item->phosphorus,
                        'potassium_level' => $item->potassium,
                        'soil_health_score' => $item->soil_health_score,
                        'recorded_at' => $item->created_at,
                        'device' => $item->device ? [
                            'id' => $item->device->id,
                            'device_name' => $item->device->device_name,
                            'device_serial_number' => $item->device->device_serial_number
                        ] : null,
                        'user' => $item->device && $item->device->user ? [
                            'id' => $item->device->user->id,
                            'name' => $item->device->user->name
                        ] : null,
                        'farm' => $item->farm ? [
                            'id' => $item->farm->id,
                            'name' => $item->farm->name
                        ] : null
                    ];
                }),
                'season' => $season
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading live data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function recommendations(Request $request)
    {
        try {
            // Since we don't have a recommendations table, let's generate based on soil data
            $soilData = SoilData::with(['device.user', 'farm'])
                ->orderBy('created_at', 'desc')
                ->take(20)
                ->get();

            $recommendations = $soilData->map(function($data) {
                $recommendation = $this->generateRecommendation($data);
                return [
                    'id' => $data->id,
                    'recommended_crop' => $recommendation['crop'],
                    'recommendation_details' => $recommendation['details'],
                    'priority' => $recommendation['priority'],
                    'confidence_score' => $recommendation['confidence'],
                    'created_at' => $data->created_at,
                    'user' => $data->device && $data->device->user ? [
                        'id' => $data->device->user->id,
                        'name' => $data->device->user->name
                    ] : null
                ];
            });

            $currentMonth = Carbon::now()->month;
            $season = ($currentMonth >= 9 || $currentMonth <= 2) ? 'Season A' : 'Season B';

            return response()->json([
                'success' => true,
                'data' => $recommendations,
                'season' => $season
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading recommendations: ' . $e->getMessage()
            ], 500);
        }
    }

    public function history(Request $request)
    {
        try {
            $query = SoilData::with(['device.user', 'farm'])
                ->orderBy('created_at', 'desc');

            // Apply date filters
            if ($request->start_date) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->end_date) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            // Season filter
            if ($request->season) {
                if ($request->season === 'Season A') {
                    $query->where(function($q) {
                        $q->whereMonth('created_at', '>=', 9)
                          ->orWhereMonth('created_at', '<=', 2);
                    });
                } else if ($request->season === 'Season B') {
                    $query->whereMonth('created_at', '>=', 3)
                          ->whereMonth('created_at', '<=', 8);
                }
            }

            $data = $query->paginate(100);

            return response()->json([
                'success' => true,
                'data' => $data->items(),
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'total_pages' => $data->lastPage(),
                    'total_items' => $data->total()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading history: ' . $e->getMessage()
            ], 500);
        }
    }

    public function analytics(Request $request)
    {
        try {
            $stats = [
                'total_devices' => Device::where('status', 'active')->count(),
                'active_users' => User::where('status', 'active')->count(),
                'total_readings' => SoilData::count(),
                'total_farms' => Farm::count()
            ];

            // Health distribution
            $healthDistribution = SoilData::select(
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

            // Trends data (last 30 days)
            $trends = SoilData::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('AVG(ph) as avg_ph'),
                DB::raw('AVG(moisture) as avg_moisture'),
                DB::raw('AVG(temperature) as avg_temperature')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

            $currentMonth = Carbon::now()->month;
            $season = ($currentMonth >= 9 || $currentMonth <= 2) ? 'Season A' : 'Season B';

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'health_distribution' => $healthDistribution,
                'trends' => $trends,
                'season' => $season
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading analytics: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getFilters()
    {
        try {
            $devices = Device::with('user')->where('status', 'active')->get();
            $users = User::where('status', 'active')->get();
            $seasons = ['Season A', 'Season B'];

            return response()->json([
                'success' => true,
                'devices' => $devices,
                'users' => $users,
                'seasons' => $seasons
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading filters: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateDemoData(Request $request)
    {
        try {
            // Get all active devices
            $devices = Device::with(['user', 'farm'])->where('status', 'active')->get();

            if ($devices->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active devices found. Please create devices first.'
                ]);
            }

            $count = 0;
            foreach ($devices as $device) {
                // Generate 5-10 demo readings per device
                $readings = rand(5, 10);

                for ($i = 0; $i < $readings; $i++) {
                    $ph = round(5.5 + (mt_rand() / mt_getrandmax()) * 2.5, 1);
                    $moisture = round(20 + (mt_rand() / mt_getrandmax()) * 60, 1);
                    $temperature = round(15 + (mt_rand() / mt_getrandmax()) * 20, 1);
                    $nitrogen = rand(10, 80);
                    $phosphorus = rand(5, 40);
                    $potassium = rand(15, 60);

                    $healthScore = $this->calculateSoilHealthScore($ph, $moisture, $temperature, $nitrogen, $phosphorus, $potassium);

                    SoilData::create([
                        'device_id' => $device->id,
                        'farm_id' => $device->farm_id,
                        'ph' => $ph,
                        'moisture' => $moisture,
                        'temperature' => $temperature,
                        'nitrogen' => $nitrogen,
                        'phosphorus' => $phosphorus,
                        'potassium' => $potassium,
                        'soil_health_score' => $healthScore,
                        'notes' => 'Demo data for testing',
                        'is_manual' => false,
                        'created_by' => Auth::id(),
                        'created_at' => Carbon::now()->subHours(rand(1, 72))
                    ]);

                    $count++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Generated {$count} demo soil readings successfully!",
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating demo data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function manualInput()
    {
        $farms = Farm::with('user')->get();
        $devices = Device::with(['user', 'farm'])->where('status', 'active')->get();

        return view('admin.soil_manual_input', compact('farms', 'devices'));
    }

    public function storeManualData(Request $request)
    {
        $request->validate([
            'device_id' => 'required|exists:devices,id',
            'farm_id' => 'required|exists:farms,id',
            'ph' => 'required|numeric|min:0|max:14',
            'moisture' => 'required|numeric|min:0|max:100',
            'temperature' => 'required|numeric|min:-50|max:60',
            'nitrogen' => 'nullable|numeric|min:0',
            'phosphorus' => 'nullable|numeric|min:0',
            'potassium' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            // Calculate soil health score
            $healthScore = $this->calculateSoilHealthScore(
                $request->ph,
                $request->moisture,
                $request->temperature,
                $request->nitrogen ?? 0,
                $request->phosphorus ?? 0,
                $request->potassium ?? 0
            );

            $soilData = SoilData::create([
                'device_id' => $request->device_id,
                'farm_id' => $request->farm_id,
                'ph' => $request->ph,
                'moisture' => $request->moisture,
                'temperature' => $request->temperature,
                'nitrogen' => $request->nitrogen,
                'phosphorus' => $request->phosphorus,
                'potassium' => $request->potassium,
                'soil_health_score' => $healthScore,
                'notes' => $request->notes,
                'is_manual' => true,
                'created_by' => Auth::id()
            ]);

            return redirect()->route('admin.soil.analysis-results', $soilData->id)
                ->with('success', 'Soil data recorded successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error saving soil data: ' . $e->getMessage()]);
        }
    }

    public function analysisResults(SoilData $soil_data)
    {
        $soil_data->load(['device.user', 'farm', 'createdBy']);

        // Generate recommendations
        $recommendation = $this->generateRecommendation($soil_data);

        // Get crop history for this farm
        $cropHistory = CropHistory::where('farm_id', $soil_data->farm_id)
            ->orderBy('planted_date', 'desc')
            ->take(5)
            ->get();

        return view('admin.soil_analysis_results', compact('soil_data', 'recommendation', 'cropHistory'));
    }

    public function getCropHistory(Request $request)
    {
        try {
            $farmId = $request->farm_id;

            $cropHistory = CropHistory::where('farm_id', $farmId)
                ->orderBy('planted_date', 'desc')
                ->take(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $cropHistory
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading crop history: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calculateSoilHealthScore($ph, $moisture, $temperature, $nitrogen, $phosphorus, $potassium)
    {
        $score = 0;

        // pH score (0-30 points)
        if ($ph >= 6.0 && $ph <= 7.5) {
            $score += 30;
        } elseif ($ph >= 5.5 && $ph <= 8.0) {
            $score += 20;
        } else {
            $score += 10;
        }

        // Moisture score (0-25 points)
        if ($moisture >= 40 && $moisture <= 70) {
            $score += 25;
        } elseif ($moisture >= 30 && $moisture <= 80) {
            $score += 15;
        } else {
            $score += 5;
        }

        // Temperature score (0-25 points)
        if ($temperature >= 18 && $temperature <= 28) {
            $score += 25;
        } elseif ($temperature >= 15 && $temperature <= 35) {
            $score += 15;
        } else {
            $score += 5;
        }

        // Nutrient score (0-20 points)
        if ($nitrogen >= 20 && $phosphorus >= 10 && $potassium >= 15) {
            $score += 20;
        } elseif ($nitrogen >= 10 && $phosphorus >= 5 && $potassium >= 10) {
            $score += 10;
        }

        return min(100, $score);
    }

    private function generateRecommendation($soilData)
    {
        $ph = $soilData->ph;
        $moisture = $soilData->moisture;
        $temperature = $soilData->temperature;
        $healthScore = $soilData->soil_health_score;

        // Seasonal crops
        $currentMonth = Carbon::now()->month;
        $isSeasonA = ($currentMonth >= 9 || $currentMonth <= 2);

        $seasonACrops = ['Maize', 'Rice', 'Sorghum', 'Groundnuts', 'Wheat'];
        $seasonBCrops = ['Beans', 'Potato', 'Sweet Potato', 'Soybeans', 'Cassava'];

        $availableCrops = $isSeasonA ? $seasonACrops : $seasonBCrops;

        // Determine best crop based on soil conditions
        $recommendedCrop = 'Mixed Vegetables'; // Default
        $details = '';
        $priority = 'medium';
        $confidence = 70;

        if ($ph >= 6.0 && $ph <= 7.0 && $moisture >= 40 && $moisture <= 70) {
            $recommendedCrop = $availableCrops[0]; // Best crop for good conditions
            $details = 'Excellent soil conditions detected. This crop should perform very well.';
            $priority = 'high';
            $confidence = 95;
        } elseif ($ph >= 5.5 && $ph <= 7.5 && $moisture >= 30 && $moisture <= 80) {
            $recommendedCrop = $availableCrops[1] ?? $availableCrops[0];
            $details = 'Good soil conditions. Consider soil amendments for optimal yield.';
            $priority = 'medium';
            $confidence = 80;
        } else {
            $recommendedCrop = 'Soil Improvement';
            $details = 'Soil requires treatment before planting. Consider pH adjustment and moisture management.';
            $priority = 'low';
            $confidence = 60;
        }

        // Add specific recommendations
        if ($ph < 6.0) {
            $details .= ' Add lime to increase pH.';
        } elseif ($ph > 7.5) {
            $details .= ' Add organic matter to lower pH.';
        }

        if ($moisture < 30) {
            $details .= ' Increase irrigation or water retention.';
        } elseif ($moisture > 80) {
            $details .= ' Improve drainage to prevent waterlogging.';
        }

        return [
            'crop' => $recommendedCrop,
            'details' => $details,
            'priority' => $priority,
            'confidence' => $confidence
        ];
    }
}
