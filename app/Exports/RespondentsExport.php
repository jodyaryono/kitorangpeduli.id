<?php

namespace App\Exports;

use App\Models\Resident;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RespondentsExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    protected ?string $verificationStatus = null;
    protected ?int $citizenTypeId = null;

    public function __construct(?string $verificationStatus = null, ?int $citizenTypeId = null)
    {
        $this->verificationStatus = $verificationStatus;
        $this->citizenTypeId = $citizenTypeId;
    }

    public function query()
    {
        $query = Respondent::with([
            'kartuKeluarga',
            'citizenType',
            'village',
            'district',
            'regency',
            'province',
        ]);

        if ($this->verificationStatus) {
            $query->where('verification_status', $this->verificationStatus);
        }

        if ($this->citizenTypeId) {
            $query->where('citizen_type_id', $this->citizenTypeId);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'NIK',
            'Nama Lengkap',
            'No. KK',
            'Status Hubungan',
            'Jenis Warga',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'Agama',
            'Status Perkawinan',
            'Pekerjaan',
            'Pendidikan',
            'No. WhatsApp',
            'Email',
            'Alamat',
            'RT',
            'RW',
            'Provinsi',
            'Kabupaten/Kota',
            'Kecamatan',
            'Kelurahan',
            'Latitude',
            'Longitude',
            'Status Verifikasi',
            'Terdaftar',
        ];
    }

    public function map($respondent): array
    {
        return [
            $respondent->nik,
            $respondent->nama_lengkap,
            $respondent->kartuKeluarga->no_kk ?? '-',
            $respondent->status_hubungan,
            $respondent->citizenType->name ?? '-',
            $respondent->tempat_lahir,
            $respondent->tanggal_lahir?->format('Y-m-d'),
            $respondent->jenis_kelamin,
            $respondent->agama,
            $respondent->status_perkawinan,
            $respondent->pekerjaan,
            $respondent->pendidikan,
            $respondent->phone,
            $respondent->email,
            $respondent->alamat,
            $respondent->rt,
            $respondent->rw,
            $respondent->province->name ?? '-',
            $respondent->regency->name ?? '-',
            $respondent->district->name ?? '-',
            $respondent->village->name ?? '-',
            $respondent->latitude,
            $respondent->longitude,
            $respondent->verification_status,
            $respondent->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
