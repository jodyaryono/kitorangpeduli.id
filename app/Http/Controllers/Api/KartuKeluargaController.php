<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KartuKeluarga;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KartuKeluargaController extends Controller
{
    /**
     * Check KK by no_kk
     */
    public function check(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'no_kk' => 'required|string|size:16',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $kk = KartuKeluarga::where('no_kk', $request->no_kk)
            ->with(['province', 'regency', 'district', 'village'])
            ->first();

        if (!$kk) {
            return response()->json([
                'success' => true,
                'exists' => false,
                'message' => 'KK tidak ditemukan',
            ]);
        }

        return response()->json([
            'success' => true,
            'exists' => true,
            'data' => [
                'id' => $kk->id,
                'no_kk' => $kk->no_kk,
                'kepala_keluarga' => $kk->kepala_keluarga,
                'alamat' => $kk->full_address,
                'verification_status' => $kk->verification_status,
                'jumlah_anggota' => $kk->anggota()->count(),
            ],
        ]);
    }

    /**
     * Store new KK
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'no_kk' => 'required|string|size:16|unique:kartu_keluarga,no_kk',
            'kepala_keluarga' => 'required|string|max:100',
            'alamat' => 'required|string',
            'rt' => 'nullable|string|max:3',
            'rw' => 'nullable|string|max:3',
            'kode_pos' => 'nullable|string|max:5',
            'province_id' => 'required|exists:provinces,id',
            'regency_id' => 'required|exists:regencies,id',
            'district_id' => 'required|exists:districts,id',
            'village_id' => 'required|exists:villages,id',
            'kk_image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $kk = KartuKeluarga::create($request->except('kk_image'));

        // Handle image upload
        if ($request->hasFile('kk_image')) {
            $path = $request->file('kk_image')->store('kk', 'public');
            $kk->update(['kk_image_path' => $path]);
        }

        return response()->json([
            'success' => true,
            'message' => 'KK berhasil didaftarkan dan menunggu verifikasi',
            'data' => [
                'id' => $kk->id,
                'no_kk' => $kk->no_kk,
                'verification_status' => $kk->verification_status,
            ],
        ], 201);
    }

    /**
     * Get KK members
     */
    public function members(KartuKeluarga $kartuKeluarga): JsonResponse
    {
        $members = $kartuKeluarga
            ->anggota()
            ->with('citizenType')
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->id,
                    'nik' => $member->nik,
                    'nama_lengkap' => $member->nama_lengkap,
                    'jenis_kelamin' => $member->jenis_kelamin,
                    'citizen_type' => $member->citizenType?->name,
                    'verification_status' => $member->verification_status,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'kk' => [
                    'no_kk' => $kartuKeluarga->no_kk,
                    'kepala_keluarga' => $kartuKeluarga->kepala_keluarga,
                ],
                'members' => $members,
            ],
        ]);
    }
}
