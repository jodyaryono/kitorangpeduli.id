<?php

namespace Database\Seeders;

use App\Models\Opd;
use Illuminate\Database\Seeder;

class OpdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $opds = [
            [
                'code' => 'DINKES',
                'name' => 'Dinas Kesehatan',
                'description' => 'Dinas Kesehatan Kota/Kabupaten bertanggung jawab atas pelayanan kesehatan masyarakat',
                'is_active' => true,
            ],
            [
                'code' => 'DISDIK',
                'name' => 'Dinas Pendidikan',
                'description' => 'Dinas Pendidikan bertanggung jawab atas penyelenggaraan pendidikan di daerah',
                'is_active' => true,
            ],
            [
                'code' => 'PUPR',
                'name' => 'Dinas PUPR & Tata Ruang',
                'description' => 'Dinas Pekerjaan Umum, Penataan Ruang dan Tata Ruang',
                'is_active' => true,
            ],
            [
                'code' => 'PRINDAKOP',
                'name' => 'Dinas Perindustrian, Perdagangan, Koperasi & UKM',
                'description' => 'Dinas yang menangani bidang perindustrian, perdagangan, koperasi dan usaha kecil menengah',
                'is_active' => true,
            ],
            [
                'code' => 'DISDUKCAPIL',
                'name' => 'Dinas Kependudukan dan Pencatatan Sipil',
                'description' => 'Dinas yang menangani administrasi kependudukan dan pencatatan sipil',
                'is_active' => true,
            ],
            [
                'code' => 'DISNAKER',
                'name' => 'Dinas Tenaga Kerja',
                'description' => 'Dinas yang menangani bidang ketenagakerjaan dan transmigrasi',
                'is_active' => true,
            ],
            [
                'code' => 'DPMPK',
                'name' => 'Dinas Pemberdayaan Masyarakat, Pemerintahan Kampung',
                'description' => 'Dinas yang menangani pemberdayaan masyarakat dan pemerintahan kampung/desa',
                'is_active' => true,
            ],
            [
                'code' => 'DINSOS',
                'name' => 'Dinas Sosial',
                'description' => 'Dinas yang menangani bidang sosial dan kesejahteraan masyarakat',
                'is_active' => true,
            ],
            [
                'code' => 'DLHK',
                'name' => 'Dinas Lingkungan Hidup dan Kebersihan',
                'description' => 'Dinas yang menangani bidang lingkungan hidup, kebersihan dan persampahan',
                'is_active' => true,
            ],
            [
                'code' => 'DP3AKB',
                'name' => 'Dinas Pemberdayaan Perempuan, Perlindungan Anak dan Keluarga Berencana',
                'description' => 'Dinas yang menangani pemberdayaan perempuan, perlindungan anak dan program keluarga berencana',
                'is_active' => true,
            ],
        ];

        foreach ($opds as $opd) {
            Opd::updateOrCreate(
                ['code' => $opd['code']],
                $opd
            );
        }
    }
}
