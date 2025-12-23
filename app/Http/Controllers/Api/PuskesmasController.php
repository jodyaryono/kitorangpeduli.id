<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Puskesmas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PuskesmasController extends Controller
{
    public function index(Request $request)
    {
        $regencyId = $request->input('regency_id');

        $query = Puskesmas::select('id', 'code', 'name', 'regency_id', 'address', 'phone')
            ->orderBy('name');

        if ($regencyId) {
            $query->where('regency_id', $regencyId);
        }

        return response()->json([
            'success' => true,
            'data' => $query->get()
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'regency_id' => 'required|exists:regencies,id',
            'code' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $puskesmas = Puskesmas::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $puskesmas,
            'message' => 'Puskesmas berhasil ditambahkan'
        ], 201);
    }

    public function show($id)
    {
        $puskesmas = Puskesmas::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $puskesmas
        ]);
    }
}
