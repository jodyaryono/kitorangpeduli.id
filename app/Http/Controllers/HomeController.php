<?php

namespace App\Http\Controllers;

use App\Models\CitizenType;
use App\Models\Opd;
use App\Models\Province;
use App\Models\Questionnaire;
use App\Models\Respondent;
use App\Models\Response;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 6;
        $search = $request->get('search');
        $opdFilter = $request->get('opd');

        $query = Questionnaire::with(['opd', 'questions'])
            ->where('is_active', true)
            ->where(function ($q) {
                $q
                    ->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q
                    ->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->withCount('questions');

        // Eager load responses with answer counts for current respondent
        if (session('respondent')) {
            $query->with(['responses' => function ($q) {
                $q->where('respondent_id', session('respondent.id'))
                  ->withCount('answers');
            }]);
        }

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q
                    ->where('title', 'ilike', "%{$search}%")
                    ->orWhere('description', 'ilike', "%{$search}%")
                    ->orWhereHas('opd', function ($q) use ($search) {
                        $q
                            ->where('name', 'ilike', "%{$search}%")
                            ->orWhere('short_name', 'ilike', "%{$search}%");
                    });
            });
        }

        // OPD filter
        if ($opdFilter) {
            $query->where('opd_id', $opdFilter);
        }

        $questionnaires = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Get all OPDs for filter dropdown
        $opds = Opd::whereHas('questionnaires', function ($q) {
            $q->where('is_active', true);
        })->orderBy('name')->get();

        $stats = [
            'questionnaires' => Questionnaire::where('is_active', true)->count(),
            'respondents' => Respondent::count(),
            'responses' => Response::where('status', 'completed')->count(),
        ];

        // If AJAX request, return partial view
        if ($request->ajax()) {
            return response()->json([
                'html' => view('partials.questionnaire-cards', compact('questionnaires'))->render(),
                'hasMore' => $questionnaires->hasMorePages(),
                'nextPage' => $questionnaires->currentPage() + 1,
                'total' => $questionnaires->total(),
                'showing' => $questionnaires->count() + (($questionnaires->currentPage() - 1) * $perPage),
            ]);
        }

        return view('home', compact('questionnaires', 'stats', 'opds', 'search', 'opdFilter'));
    }
}
