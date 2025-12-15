<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CitizenType;
use Illuminate\Http\JsonResponse;

class CitizenTypeController extends Controller
{
    /**
     * Get all active citizen types
     */
    public function index(): JsonResponse
    {
        $types = CitizenType::active()->orderBy('name')->get(['id', 'code', 'name', 'description']);

        return response()->json([
            'success' => true,
            'data' => $types,
        ]);
    }
}
