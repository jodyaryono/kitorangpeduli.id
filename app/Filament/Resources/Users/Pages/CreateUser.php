<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    public function getTitle(): string
    {
        return 'Tambah User Officer';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-generate password untuk role non-admin yang login via OTP
        if (empty($data['password']) && !in_array($data['role'], ['admin', 'opd_admin'])) {
            $data['password'] = Hash::make(Str::random(32));
        }

        unset($data['password_confirmation']);

        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('User berhasil dibuat')
            ->body('User officer baru telah ditambahkan ke sistem.');
    }
}
