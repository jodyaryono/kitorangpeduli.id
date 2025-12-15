<?php

namespace App\Http\Controllers;

use App\Exports\AnswersExport;
use App\Exports\RespondentsExport;
use App\Exports\ResponsesExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function exportRespondents()
    {
        $status = request('status');
        $citizenType = request('citizen_type_id');

        $filename = 'respondents_' . ($status ?? 'all') . '_' . now()->format('YmdHis') . '.xlsx';

        return Excel::download(new RespondentsExport($status, $citizenType), $filename);
    }

    public function exportResponses()
    {
        $questionnaireId = request('questionnaire_id');

        $filename = 'responses_' . now()->format('YmdHis') . '.xlsx';

        return Excel::download(new ResponsesExport($questionnaireId), $filename);
    }

    public function exportAnswers(int $questionnaireId)
    {
        $filename = 'answers_questionnaire_' . $questionnaireId . '_' . now()->format('YmdHis') . '.xlsx';

        return Excel::download(new AnswersExport($questionnaireId), $filename);
    }
}
