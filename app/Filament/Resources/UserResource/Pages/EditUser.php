<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\EditRecord;
use STS\FilamentImpersonate\Pages\Actions\Impersonate;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Impersonate::make()
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}
