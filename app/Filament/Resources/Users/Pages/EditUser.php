<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($record) {
                    $userId = auth()->id();
                    // Mencegah hapus user yang sedang login
                    if ($record->id === $userId) {
                        $this->halt();
                    }
                }),
        ];
    }

    public function getTitle(): string
    {
        return 'Edit User Officer';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('User berhasil diperbarui')
            ->body('Data user officer telah diperbarui.');
    }
}
