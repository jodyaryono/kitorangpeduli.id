<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class FieldOfficerController extends Controller
{
    public function index(Request $request)
    {
        $opdId = $request->input('opd_id');

        $query = User::where('role', 'field_officer')
            ->select('id', 'name', 'email', 'phone', 'opd_id')
            ->orderBy('name');

        if ($opdId) {
            $query->where('opd_id', $opdId);
        }

        return response()->json([
            'success' => true,
            'data' => $query->get()
        ]);
    }

    public function show($id)
    {
        $officer = User::where('role', 'field_officer')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $officer
        ]);
    }
}
