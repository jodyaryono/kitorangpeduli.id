<?php

namespace App\Filament\Pages;

use App\Exports\AnswersExport;
use App\Exports\RespondentsExport;
use App\Exports\ResponsesExport;
use App\Models\Questionnaire;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Maatwebsite\Excel\Facades\Excel;

class Reports extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected string $view = 'filament.pages.reports';

    protected static ?string $navigationLabel = 'Export Laporan';

    protected static ?string $title = 'Export Laporan';

    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Export Data Survey')
                    ->description('Download data jawaban survey dalam format Excel')
                    ->schema([
                        Select::make('questionnaire_id')
                            ->label('Pilih Kuesioner')
                            ->options(Questionnaire::pluck('title', 'id'))
                            ->placeholder('Semua Kuesioner'),
                        Select::make('export_type')
                            ->label('Tipe Export')
                            ->options([
                                'responses' => 'Data Responden Survey',
                                'answers' => 'Data Jawaban Detail',
                            ])
                            ->default('responses')
                            ->required(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function exportSurvey(): void
    {
        $data = $this->form->getState();

        if ($data['export_type'] === 'answers' && empty($data['questionnaire_id'])) {
            Notification::make()
                ->title('Pilih kuesioner untuk export jawaban detail')
                ->warning()
                ->send();
            return;
        }

        $filename = 'survey_' . now()->format('YmdHis') . '.xlsx';

        if ($data['export_type'] === 'answers') {
            $export = new AnswersExport($data['questionnaire_id']);
        } else {
            $export = new ResponsesExport($data['questionnaire_id'] ?? null);
        }

        Notification::make()
            ->title('Export berhasil dibuat')
            ->success()
            ->send();

        Excel::download($export, $filename);
    }

    public function exportRespondents(): void
    {
        $filename = 'respondents_' . now()->format('YmdHis') . '.xlsx';

        Notification::make()
            ->title('Export responden berhasil dibuat')
            ->success()
            ->send();

        Excel::download(new RespondentsExport(), $filename);
    }
}
