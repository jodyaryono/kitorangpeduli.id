<?php

namespace App\Filament\Resources\CitizenTypes\Pages;

use App\Filament\Resources\CitizenTypes\CitizenTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCitizenType extends CreateRecord
{
    protected static string $resource = CitizenTypeResource::class;
}
