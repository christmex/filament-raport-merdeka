<?php

namespace App\Filament\Resources\SchoolSettingResource\Pages;

use Filament\Actions;
use App\Helpers\Helper;
use App\Models\SchoolSetting;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\SchoolSettingResource;

class ManageSchoolSettings extends ManageRecords
{
    protected static string $resource = SchoolSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            Actions\Action::make('createDefaultMeta')
            ->action(function(){
                // {"show_fase":"1","show_top_kkm":"0"}
                $SchoolSetting = SchoolSetting::first();
                $SchoolSetting->meta = $SchoolSetting->meta ?? Helper::getDefaultMetaForSchoolSetting();
                if($SchoolSetting->save()){
                    Notification::make()
                        ->success()
                        ->title('yeayy, success!')
                        ->body('Successfully create meta master data')
                        ->send();
                }
            })
        ];
    }
}
