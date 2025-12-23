<?php

namespace App\Exports;

use App\Models\Questionnaire;
use App\Models\Response;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AnswersExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected int $questionnaireId;

    public function __construct(int $questionnaireId)
    {
        $this->questionnaireId = $questionnaireId;
    }

    public function collection()
    {
        $questionnaire = Questionnaire::with('questions')->find($this->questionnaireId);
        $questions = $questionnaire->questions()->orderBy('order')->get();

        $responses = Response::with([
            'resident',
            'answers.question',
            'answers.selectedOption',
        ])
            ->where('questionnaire_id', $this->questionnaireId)
            ->where('status', 'completed')
            ->get();

        $data = [];

        foreach ($responses as $response) {
            $row = [
                'ID' => $response->id,
                'NIK' => $response->respondent->nik,
                'Nama' => $response->respondent->nama_lengkap,
                'Waktu' => $response->completed_at?->format('Y-m-d H:i:s'),
            ];

            foreach ($questions as $question) {
                $answer = $response->answers->firstWhere('question_id', $question->id);

                if ($answer) {
                    if ($answer->selectedOption) {
                        $row['Q' . $question->order . ': ' . substr($question->question_text, 0, 30)] = $answer->selectedOption->option_text;
                    } else {
                        $row['Q' . $question->order . ': ' . substr($question->question_text, 0, 30)] = $answer->answer_text ?? '-';
                    }
                } else {
                    $row['Q' . $question->order . ': ' . substr($question->question_text, 0, 30)] = '-';
                }
            }

            $data[] = $row;
        }

        return collect($data);
    }

    public function headings(): array
    {
        $questionnaire = Questionnaire::with('questions')->find($this->questionnaireId);
        $questions = $questionnaire->questions()->orderBy('order')->get();

        $headings = ['ID', 'NIK', 'Nama', 'Waktu'];

        foreach ($questions as $question) {
            $headings[] = 'Q' . $question->order . ': ' . substr($question->question_text, 0, 30);
        }

        return $headings;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Jawaban Survey';
    }
}
