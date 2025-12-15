<?php

namespace App\Http\Controllers;

use App\Models\Opd;
use App\Models\Questionnaire;
use App\Services\GeminiReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AiReportController extends Controller
{
    public function submit(Request $request, GeminiReportService $service): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'opd_id' => $user?->opd_id ? ['nullable'] : ['required', 'exists:opds,id'],
            'questionnaire_id' => ['required', 'exists:questionnaires,id'],
            'prompt' => ['required', 'string', 'max:500'],
        ]);

        $opdId = $user?->opd_id ?: ($validated['opd_id'] ?? null);

        $questionnaire = Questionnaire::with(['questions', 'questions.options'])
            ->findOrFail($validated['questionnaire_id']);

        // Use loose comparison or cast to int for comparison
        if ($opdId && (int) $questionnaire->opd_id !== (int) $opdId) {
            abort(403, 'Questionnaire tidak sesuai dengan OPD yang dipilih');
        }

        $report = $service->generateReport(
            $questionnaire,
            $validated['prompt'],
            $opdId
        );

        if (($report['status'] ?? '') !== 'completed') {
            return back()
                ->withInput()
                ->with('report_error', $report['error_message'] ?? 'Gagal menghasilkan laporan');
        }

        // Get OPD name
        $opdName = 'N/A';
        if ($opdId) {
            $opd = Opd::find($opdId);
            $opdName = $opd?->name ?? 'N/A';
        }

        return back()
            ->with('report', $report)
            ->with('questionnaire_title', $questionnaire->title)
            ->with('prompt', $validated['prompt'])
            ->with('selected_opd', $opdId)
            ->with('selected_opd_name', $opdName)
            ->with('selected_questionnaire_id', $questionnaire->id);
    }
}
