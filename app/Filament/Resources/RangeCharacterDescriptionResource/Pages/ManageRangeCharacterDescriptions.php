<?php

namespace App\Filament\Resources\RangeCharacterDescriptionResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use App\Models\RangeCharacterDescription;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\RangeCharacterDescriptionResource;

class ManageRangeCharacterDescriptions extends ManageRecords
{
    protected static string $resource = RangeCharacterDescriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('CreateMaster')
                ->color('success')
                ->action(function(){
                    $ranges = [
                        ['name' => 'A', 'start' => 3.75, 'end' => 4.0],
                        ['name' => 'B', 'start' => 3.5, 'end' => 3.74],
                        ['name' => 'C', 'start' => 3.25, 'end' => 3.49],
                        ['name' => 'D', 'start' => 2.75, 'end' => 3.24],
                    ];

                    foreach ($ranges as $value) {
                        RangeCharacterDescription::firstOrCreate($value);
                    }
                    Notification::make()
                        ->success()
                        ->title('yeayy, success!')
                        ->body('Successfully create master data')
                        ->send();
                })
                ,
        ];
    }
}
