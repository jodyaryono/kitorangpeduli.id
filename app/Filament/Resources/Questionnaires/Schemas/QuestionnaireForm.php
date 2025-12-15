<?php

namespace App\Filament\Resources\Questionnaires\Schemas;

use App\Models\Question;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuestionnaireForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ==================== INFO KUESIONER ====================
                Section::make('📋 Informasi Kuesioner')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('opd_id')
                                ->label('OPD')
                                ->relationship('opd', 'name')
                                ->required()
                                ->searchable()
                                ->preload(),
                            TextInput::make('title')
                                ->label('Judul Kuesioner')
                                ->required()
                                ->maxLength(255),
                        ]),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpanFull(),
                        Grid::make(2)->schema([
                            FileUpload::make('cover_image_path')
                                ->label('Cover Image')
                                ->image()
                                ->directory('questionnaire-covers'),
                            TextInput::make('cover_video_path')
                                ->label('Cover Video URL')
                                ->url()
                                ->placeholder('https://youtube.com/...'),
                        ]),
                    ])
                    ->columnSpanFull(),
                // ==================== PENGATURAN ====================
                Section::make('⚙️ Pengaturan')
                    ->schema([
                        Grid::make(2)->schema([
                            DatePicker::make('start_date')
                                ->label('Tanggal Mulai')
                                ->displayFormat('d/m/Y')
                                ->native(false),
                            DatePicker::make('end_date')
                                ->label('Tanggal Berakhir')
                                ->displayFormat('d/m/Y')
                                ->native(false),
                        ]),
                        Grid::make(3)->schema([
                            Select::make('visibility')
                                ->label('Visibility Entry')
                                ->options([
                                    'self_entry' => 'Responden Isi Sendiri',
                                    'officer_assisted' => 'Officer-Assisted',
                                    'both' => 'Keduanya',
                                ])
                                ->default('self_entry')
                                ->required(),
                        ]),
                        Grid::make(4)->schema([
                            Toggle::make('is_active')
                                ->label('Aktif')
                                ->default(true)
                                ->required(),
                            Toggle::make('requires_location')
                                ->label('Wajib Lokasi GPS')
                                ->default(false),
                            Toggle::make('requires_verified_respondent')
                                ->label('Wajib Terverifikasi')
                                ->default(false),
                            TextInput::make('max_responses')
                                ->label('Max Responden')
                                ->numeric()
                                ->placeholder('Tak terbatas'),
                        ]),
                    ])
                    ->columnSpanFull()
                    ->collapsed(),
                // ==================== PERTANYAAN ====================
                Section::make('❓ Daftar Pertanyaan')
                    ->description('Tambahkan pertanyaan untuk kuesioner ini')
                    ->schema([
                        Repeater::make('questions')
                            ->label('')
                            ->relationship()
                            ->orderColumn('order')
                            ->reorderable()
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(fn(array $state): ?string =>
                                isset($state['question_text'])
                                    ? 'Q' . ($state['order'] ?? '') . ': ' . substr($state['question_text'], 0, 50) . (strlen($state['question_text'] ?? '') > 50 ? '...' : '')
                                    : 'Pertanyaan Baru')
                            ->schema([
                                Textarea::make('question_text')
                                    ->label('Teks Pertanyaan')
                                    ->required()
                                    ->rows(2)
                                    ->columnSpanFull(),
                                Grid::make(3)->schema([
                                    Select::make('question_type')
                                        ->label('Tipe Pertanyaan')
                                        ->options(Question::TYPES)
                                        ->required()
                                        ->live()
                                        ->default('single_choice'),
                                    Select::make('media_type')
                                        ->label('Tipe Media')
                                        ->options(Question::MEDIA_TYPES)
                                        ->default('none'),
                                    Toggle::make('is_required')
                                        ->label('Wajib Dijawab')
                                        ->default(true),
                                ]),
                                FileUpload::make('media_path')
                                    ->label('Media (Gambar/Video)')
                                    ->image()
                                    ->directory('question-media')
                                    ->visible(fn(Get $get) => in_array($get('media_type'), ['image', 'video']))
                                    ->columnSpanFull(),
                                // Options untuk pertanyaan pilihan
                                Repeater::make('options')
                                    ->label('Pilihan Jawaban')
                                    ->relationship()
                                    ->orderColumn('order')
                                    ->reorderable()
                                    ->simple(
                                        TextInput::make('option_text')
                                            ->required()
                                            ->placeholder('Ketik pilihan jawaban...')
                                    )
                                    ->defaultItems(4)
                                    ->addActionLabel('+ Tambah Pilihan')
                                    ->visible(fn(Get $get) => in_array($get('question_type'), ['single_choice', 'multiple_choice', 'dropdown']))
                                    ->columnSpanFull(),
                                // Settings untuk scale
                                Grid::make(4)
                                    ->schema([
                                        TextInput::make('settings.min')
                                            ->label('Nilai Min')
                                            ->numeric()
                                            ->default(1),
                                        TextInput::make('settings.max')
                                            ->label('Nilai Max')
                                            ->numeric()
                                            ->default(5),
                                        TextInput::make('settings.min_label')
                                            ->label('Label Min')
                                            ->default('Sangat Buruk'),
                                        TextInput::make('settings.max_label')
                                            ->label('Label Max')
                                            ->default('Sangat Baik'),
                                    ])
                                    ->visible(fn(Get $get) => $get('question_type') === 'scale'),
                            ])
                            ->addActionLabel('+ Tambah Pertanyaan')
                            ->defaultItems(0)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
