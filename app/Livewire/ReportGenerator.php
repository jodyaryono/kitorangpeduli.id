<?php

namespace App\Livewire;

use App\Models\Questionnaire;
use App\Models\Report;
use App\Services\GeminiReportService;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ReportGenerator extends Component
{
    #[Validate('required')]
    public ?int $questionnaire_id = null;

    #[Validate('required|min:5|max:500')]
    public string $user_prompt = '';

    public $selectedTab = 'data';
    public $isLoading = false;
    public $reportData = null;
    public $error = null;
    public $success = false;

    private GeminiReportService $geminiService;

    public function mount()
    {
        // Security check: user harus sudah login
        if (!auth()->check()) {
            redirect()->route('login');
        }

        // Security check: user harus punya OPD
        if (!auth()->user()->opd_id) {
            $this->error = 'Anda belum terdaftar di OPD manapun. Hubungi administrator.';
            return;
        }

        $this->geminiService = new GeminiReportService();
    }

    public function generateReport()
    {
        $this->validate();

        // Security check: verify questionnaire belongs to user's OPD
        $questionnaire = Questionnaire::findOrFail($this->questionnaire_id);

        if ($questionnaire->opd_id !== auth()->user()->opd_id) {
            $this->error = 'Anda tidak memiliki akses ke questionnaire ini. Hanya OPD pemilik yang dapat mengakses.';
            return;
        }

        $this->isLoading = true;
        $this->error = null;
        $this->reportData = null;
        $this->success = false;

        try {
            $questionnaire = Questionnaire::findOrFail($this->questionnaire_id);

            // Generate report via Gemini
            $result = $this->geminiService->generateReport(
                $questionnaire,
                $this->user_prompt,
                auth()->user()->opd_id
            );

            if ($result['status'] === 'failed') {
                $this->error = $result['error_message'] ?? 'Unknown error occurred';
            } else {
                // Save to database
                $report = Report::create([
                    'user_id' => auth()->id(),
                    'opd_id' => auth()->user()->opd_id,
                    'questionnaire_id' => $this->questionnaire_id,
                    'user_prompt' => $this->user_prompt,
                    'ai_response' => $result['ai_response'] ?? null,
                    'raw_data' => $result['raw_data'] ?? null,
                    'chart_data' => $result['chart_data'] ?? null,
                    'map_data' => $result['map_data'] ?? null,
                    'input_type' => 'text',
                    'status' => 'completed',
                ]);

                $this->reportData = $result;
                $this->selectedTab = 'data';
                $this->success = true;

                // Reset form
                $this->user_prompt = '';
            }
        } catch (\Exception $e) {
            $this->error = 'Error: ' . $e->getMessage();
        } finally {
            $this->isLoading = false;
        }
    }

    public function render()
    {
        return view('livewire.report-generator', [
            'questionnaires' => auth()->user()->opd_id
                ? Questionnaire::where('opd_id', auth()->user()->opd_id)
                    ->where('is_active', true)
                    ->orderBy('created_at', 'desc')
                    ->get()
                : collect([]),
            'opdName' => auth()->user()->opd?->name ?? 'N/A',
        ]);
    }
}
