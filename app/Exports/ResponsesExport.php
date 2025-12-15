<?php

namespace App\Exports;

use App\Models\Response;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ResponsesExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    protected ?int $questionnaireId = null;

    public function __construct(?int $questionnaireId = null)
    {
        $this->questionnaireId = $questionnaireId;
    }

    public function query()
    {
        $query = Response::with([
            'respondent.citizenType',
            'respondent.village',
            'respondent.district',
            'respondent.regency',
            'respondent.province',
            'questionnaire',
            'answers.question',
            'answers.selectedOption',
        ])
            ->where('status', 'completed');

        if ($this->questionnaireId) {
            $query->where('questionnaire_id', $this->questionnaireId);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Kuesioner',
            'OPD',
            'NIK Responden',
            'Nama Responden',
            'Jenis Warga',
            'No. WhatsApp',
            'Provinsi',
            'Kabupaten/Kota',
            'Kecamatan',
            'Kelurahan',
            'Latitude Survey',
            'Longitude Survey',
            'Waktu Mulai',
            'Waktu Selesai',
            'Durasi (menit)',
        ];
    }

    public function map($response): array
    {
        $duration = null;
        if ($response->started_at && $response->completed_at) {
            $duration = $response->started_at->diffInMinutes($response->completed_at);
        }

        return [
            $response->id,
            $response->questionnaire->title,
            $response->questionnaire->opd->name ?? '-',
            $response->respondent->nik,
            $response->respondent->nama_lengkap,
            $response->respondent->citizenType->name ?? '-',
            $response->respondent->phone,
            $response->respondent->province->name ?? '-',
            $response->respondent->regency->name ?? '-',
            $response->respondent->district->name ?? '-',
            $response->respondent->village->name ?? '-',
            $response->latitude,
            $response->longitude,
            $response->started_at?->format('Y-m-d H:i:s'),
            $response->completed_at?->format('Y-m-d H:i:s'),
            $duration,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
