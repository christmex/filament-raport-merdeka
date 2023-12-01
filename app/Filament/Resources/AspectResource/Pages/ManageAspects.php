<?php

namespace App\Filament\Resources\AspectResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use App\Filament\Resources\AspectResource;
use App\Models\Aspect;
use Filament\Resources\Pages\ManageRecords;

class ManageAspects extends ManageRecords
{
    protected static string $resource = AspectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            Actions\Action::make('CreateMaster')
                ->color('success')
                ->action(function(){
                    // $aspects = ['Spiritual','Psychological','Social','Cultural'];
                    $aspects = [
                        ['name' => 'Spiritual'],
                        ['name' => 'Psychological'],
                        ['name' => 'Social'],
                        ['name' => 'Cultural'],
                    ];

                    foreach ($aspects as $value) {
                        Aspect::firstOrCreate($value);
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
