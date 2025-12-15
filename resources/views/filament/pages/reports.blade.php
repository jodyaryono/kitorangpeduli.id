<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Survey Export --}}
        <x-filament::section>
            <x-slot name="heading">
                Export Data Survey
            </x-slot>
            <x-slot name="description">
                Download data jawaban survey dalam format Excel
            </x-slot>

            <form wire:submit="exportSurvey">
                {{ $this->form }}

                <div class="mt-4">
                    <x-filament::button type="submit" icon="heroicon-o-arrow-down-tray">
                        Export Survey
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        {{-- Respondent Export --}}
        <x-filament::section>
            <x-slot name="heading">
                Export Data Responden
            </x-slot>
            <x-slot name="description">
                Download seluruh data responden dalam format Excel
            </x-slot>

            <div class="flex gap-4 flex-wrap">
                <x-filament::button
                    wire:click="exportRespondents"
                    icon="heroicon-o-users"
                    color="success"
                >
                    Export Semua Responden
                </x-filament::button>

                <x-filament::link
                    :href="route('export.respondents', ['status' => 'verified'])"
                    icon="heroicon-o-check-circle"
                    color="info"
                    target="_blank"
                >
                    Export Terverifikasi
                </x-filament::link>

                <x-filament::link
                    :href="route('export.respondents', ['status' => 'pending'])"
                    icon="heroicon-o-clock"
                    color="warning"
                    target="_blank"
                >
                    Export Menunggu Verifikasi
                </x-filament::link>
            </div>
        </x-filament::section>

        {{-- Quick Stats --}}
        <x-filament::section>
            <x-slot name="heading">
                Ringkasan Data
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ \App\Models\Questionnaire::count() }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Kuesioner</div>
                </div>
                <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">{{ \App\Models\Respondent::verified()->count() }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Responden Terverifikasi</div>
                </div>
                <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-purple-600">{{ \App\Models\Response::completed()->count() }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Survey Selesai</div>
                </div>
                <div class="bg-amber-50 dark:bg-amber-900/20 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-amber-600">{{ \App\Models\KartuKeluarga::count() }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Kartu Keluarga</div>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
