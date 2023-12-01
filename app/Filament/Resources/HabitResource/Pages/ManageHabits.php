<?php

namespace App\Filament\Resources\HabitResource\Pages;

use App\Models\Habit;
use Filament\Actions;
use App\Models\Aspect;
use Filament\Notifications\Notification;
use App\Filament\Resources\HabitResource;
use Filament\Resources\Pages\ManageRecords;

class ManageHabits extends ManageRecords
{
    protected static string $resource = HabitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            // Actions\Action::make('CreateMaster')
            //     ->color('success')
            //     ->action(function(){

            //         $getAspects = Aspect::all();

            //         $habits = [
            //             [
            //                 'aspect_id' => $getAspects->where('name','Spiritual')->first()->id,
            //                 'name'=>'praying',
            //             ],
            //             [
            //                 'aspect_id' => $getAspects->where('name','Spiritual')->first()->id,
            //                 'name'=>'reading holy bible scripture',
            //             ],
            //             [
            //                 'aspect_id' => $getAspects->where('name','Spiritual')->first()->id,
            //                 'name'=>'worship',
            //             ],
            //             [
            //                 'aspect_id' => $getAspects->where('name','Psychological')->first()->id,
            //                 'name'=>'taking food properly',
            //             ],
            //             [
            //                 'aspect_id' => $getAspects->where('name','Psychological')->first()->id,
            //                 'name'=>'honest',
            //             ],
            //             [
            //                 'aspect_id' => $getAspects->where('name','Psychological')->first()->id,
            //                 'name'=>'keep being clean',
            //             ],
            //             [
            //                 'aspect_id' => $getAspects->where('name','Psychological')->first()->id,
            //                 'name'=>'carrying stationary',
            //             ],
            //             [
            //                 'aspect_id' => $getAspects->where('name','Social')->first()->id,
            //                 'name'=>'speaking nicely',
            //             ],
            //             [
            //                 'aspect_id' => $getAspects->where('name','Social')->first()->id,
            //                 'name'=>'sitting properly',
            //             ],
            //             [
            //                 'aspect_id' => $getAspects->where('name','Cultural')->first()->id,
            //                 'name'=>'throwing rubbish into trash can',
            //             ],
            //             [
            //                 'aspect_id' => $getAspects->where('name','Cultural')->first()->id,
            //                 'name'=>'doing tasks',
            //             ],
            //         ];

            //         foreach ($habits as $value) {
            //             Habit::firstOrCreate($value);
            //         }
            //         Notification::make()
            //             ->success()
            //             ->title('yeayy, success!')
            //             ->body('Successfully create master data')
            //             ->send();
            //     })
        ];
    }
}
