<?php

namespace App\Filament\Resources\CharacterDescriptionResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\DB;
use App\Models\CharacterDescription;
use Filament\Notifications\Notification;
use App\Models\RangeCharacterDescription;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\CharacterDescriptionResource;

class ManageCharacterDescriptions extends ManageRecords
{
    protected static string $resource = CharacterDescriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('CreateMaster')
                ->color('success')
                ->action(function(){
                    $ranges = RangeCharacterDescription::all();
                    
                    $ADesc = [
                        ['range_character_description_id'=>$ranges->where('name','A')->first()->id,'description'=>'[STUDENT_NAME] Display a good attitude all the time'],
                        ['range_character_description_id'=>$ranges->where('name','A')->first()->id,'description'=>'[STUDENT_NAME] Shows a good work and study habits'],
                        ['range_character_description_id'=>$ranges->where('name','A')->first()->id,'description'=>'[STUDENT_NAME] Has been consistenly showing good responsibillity'],
                    ];
                    $BDesc = [
                        ['range_character_description_id'=>$ranges->where('name','B')->first()->id,'description'=>'[STUDENT_NAME] Has great potential and can improve more'],
                        ['range_character_description_id'=>$ranges->where('name','B')->first()->id,'description'=>'[STUDENT_NAME] Works well with groupmates'],
                        ['range_character_description_id'=>$ranges->where('name','B')->first()->id,'description'=>'[STUDENT_NAME] Willing to assume responsibillity'],
                        ['range_character_description_id'=>$ranges->where('name','B')->first()->id,'description'=>'[STUDENT_NAME] Has been consistenly progressing'],
                    ];
                    $CDesc = [
                        ['range_character_description_id'=>$ranges->where('name','C')->first()->id,'description'=>'[STUDENT_NAME] Has great potential but refuse to use it'],
                        ['range_character_description_id'=>$ranges->where('name','C')->first()->id,'description'=>'[STUDENT_NAME] Need to develop [SEX] work and study habits'],
                        ['range_character_description_id'=>$ranges->where('name','C')->first()->id,'description'=>'[STUDENT_NAME] Has to be encouraged to work with peers'],
                        ['range_character_description_id'=>$ranges->where('name','C')->first()->id,'description'=>'[STUDENT_NAME] Afraid to assume responsibillity'],
                    ];
                    $DDesc = [
                        ['range_character_description_id'=>$ranges->where('name','D')->first()->id,'description'=>'[STUDENT_NAME] Not that eager to change attitude'],
                        ['range_character_description_id'=>$ranges->where('name','D')->first()->id,'description'=>'[STUDENT_NAME] Need hard working to encourage [SEX] study and attitude'],
                        ['range_character_description_id'=>$ranges->where('name','D')->first()->id,'description'=>'[STUDENT_NAME] is seemingly regressing'],
                    ];
                    
                    if(!CharacterDescription::all()->count()){
                        DB::table('character_descriptions')->insertOrIgnore($ADesc);
                        DB::table('character_descriptions')->insertOrIgnore($BDesc);
                        DB::table('character_descriptions')->insertOrIgnore($CDesc);
                        DB::table('character_descriptions')->insertOrIgnore($DDesc);
                        Notification::make()
                            ->success()
                            ->title('yeayy, success!')
                            ->body('Successfully create master data')
                            ->send();
                    }

                })
                ,
        ];
    }
}
