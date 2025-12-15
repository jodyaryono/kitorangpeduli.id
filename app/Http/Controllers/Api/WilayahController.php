<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Village;
use Illuminate\Http\JsonResponse;

class WilayahController extends Controller
{
    /**
     * Get all provinces
     */
    public function provinces(): JsonResponse
    {
        $provinces = Province::orderBy('name')->get(['id', 'code', 'name']);

        return response()->json([
            'success' => true,
            'data' => $provinces,
        ]);
    }

    /**
     * Get regencies by province ID (which is the province code in CHAR format)
     */
    public function regencies($province): JsonResponse
    {
        // Province ID is the code itself (CHAR)
        $provinceModel = Province::find($province);

        if (!$provinceModel) {
            return response()->json([
                'success' => false,
                'message' => 'Province not found',
                'data' => [],
            ], 404);
        }

        $regencies = $provinceModel->regencies()->orderBy('name')->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $regencies,
        ]);
    }

    /**
     * Get districts by regency
     */
    public function districts(Regency $regency): JsonResponse
    {
        $districts = $regency->districts()->orderBy('name')->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $districts,
        ]);
    }

    /**
     * Get villages by district
     */
    public function villages(District $district): JsonResponse
    {
        $villages = $district->villages()->orderBy('name')->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $villages,
        ]);
    }

    /**
     * Search villages by name
     */
    public function searchVillages(string $query): JsonResponse
    {
        $villages = Village::with(['district.regency.province'])
            ->where('name', 'ILIKE', "%{$query}%")
            ->limit(20)
            ->get()
            ->map(function ($village) {
                return [
                    'id' => $village->id,
                    'code' => $village->code,
                    'name' => $village->name,
                    'district' => $village->district->name,
                    'regency' => $village->district->regency->name,
                    'province' => $village->district->regency->province->name,
                    'full_name' => "{$village->name}, {$village->district->name}, {$village->district->regency->name}, {$village->district->regency->province->name}",
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $villages,
        ]);
    }
}
