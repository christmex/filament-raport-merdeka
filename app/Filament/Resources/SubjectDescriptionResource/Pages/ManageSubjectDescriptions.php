<?php

namespace App\Filament\Resources\SubjectDescriptionResource\Pages;

use Filament\Actions;
use App\Helpers\Helper;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\SubjectUser;
use Illuminate\Support\Str;
use App\Models\SubjectDescription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\SubjectDescriptionResource;

class ManageSubjectDescriptions extends ManageRecords
{
    protected static string $resource = SubjectDescriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            Actions\Action::make('newSubjectDescription')
            ->form([

                \Filament\Forms\Components\Group::make()
                    ->schema([
                        \Filament\Forms\Components\Section::make()
                            ->schema([
                                \Filament\Forms\Components\Select::make('subject_user_id')
                                    ->label('subject')
                                    ->options(SubjectUser::with('subject')->whereIn('id',auth()->user()->activeSubjects->pluck('id')->toArray())->get()->pluck('subject_user_name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->multiple()
                                    ->selectablePlaceholder(false)
                                    ->preload(),
                                \Filament\Forms\Components\Select::make('topic_setting_id')
                                    ->relationship('topicSetting','topic_setting_name')
                                    ->required()
                                    ->searchable()
                                    ->helperText('Topic 1 also called Chapter 1 or bab 1, etc, they are all the same 🤩')
                                    ->preload(),
                                // \Filament\Forms\Components\TextInput::make('range_start')
                                //     ->numeric()
                                //     ->minValue(0)
                                //     ->maxValue(100)
                                //     ->required(),
                                // \Filament\Forms\Components\TextInput::make('range_end')
                                //     ->numeric()
                                //     ->minValue(0)
                                //     ->maxValue(100)
                                //     ->required(),
                                // \Filament\Forms\Components\TextInput::make('topic_name')
                                //     ->placeholder('optional')
                                //     ->maxLength(255),
                                // \Filament\Forms\Components\TextInput::make('predicate')
                                //     ->placeholder('optional')
                                //     ->maxLength(255),
                                // \Filament\Forms\Components\Textarea::make('description')
                                //     ->columnSpanFull()
                                //     ->required()
                                //     ->live()
                                //     ->default('Ananda [STUDENT_NAME] [STUDENT_PREDICATE] dalam ...')
                                //     ->helperText('Please use [STUDENT_NAME] when you want to mention the student name and [STUDENT_PREDICATE] if you want mention the predicate'),
                                // \Filament\Forms\Components\Toggle::make('is_english_description')
                                //     ->helperText('please turn this on, if you make description in english')

                                \Filament\Forms\Components\Textarea::make('description')
                                    ->columnSpanFull()
                                    ->required()
                                    ->live(onBlur: true)
                                    ->default('Ananda [STUDENT_NAME] [STUDENT_PREDICATE] dalam ...')
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $state = Str::replace('[STUDENT_PREDICATE]', Helper::predicate(90,70,$get('is_english_description')), Str::replace('[STUDENT_NAME]', Str::title('John Doe'), $state));
                                        $set('result', $state);
                                    })
                                    ->helperText(new HtmlString('Please use [STUDENT_NAME] when you want to mention the student name and [STUDENT_PREDICATE] if you want mention the predicate')),

                                \Filament\Forms\Components\Toggle::make('is_english_description')
                                    ->live()
                                    ->afterStateUpdated(function ($state,Set $set, Get $get) {
                                        $desc = $get('description');
                                        $desc = Str::replace('[STUDENT_PREDICATE]', Helper::predicate(90,70,$state), Str::replace('[STUDENT_NAME]', Str::title('John Doe'), $desc));
                                        $set('result', $desc);
                                    })
                                    ->helperText('please turn this on, if you make description in english')
                            ])
                    ]),
                \Filament\Forms\Components\Group::make()
                    ->schema([
                        \Filament\Forms\Components\Section::make()
                            ->schema([
                                \Filament\Forms\Components\Textarea::make('result')
                                    ->rows(10)
                                    ->cols(20)
                                    ->default('Ananda [STUDENT_NAME] [STUDENT_PREDICATE] dalam ...')
                            ])
                    ])


                
            ])
            // ->form(SubjectDescriptionResource::getForm())
            ->action(function(array $data){
                $dataArray = [];

                foreach ($data['subject_user_id'] as $key => $value) {
                    // dd($data);
                    $dataArray[] = [
                        'subject_user_id' => $value,
                        'topic_setting_id' => $data['topic_setting_id'],
                        // 'range_start' => $data['range_start'],
                        // 'range_end' => $data['range_end'],
                        // 'topic_name' => $data['topic_name'],
                        // 'predicate' => $data['predicate'],
                        'description' => $data['description'],
                    ];
                }

                $check = SubjectDescription::with('subjectUserThrough')->where('topic_setting_id',$data['topic_setting_id'])
                ->whereIn('subject_user_id', $data['subject_user_id'])->get();
                if($check->count()){
                    $names ='';

                        foreach ($check as $key => $value) {
                            $names .= $value->subjectUserThrough->subject_name."<br>";
                        }
                        
                        Notification::make()
                        ->danger()
                        ->title('Failed! data exist with selected subject and topic')
                        ->body($names)
                        ->send();
                }else {
                    if(DB::table('subject_descriptions')->insertOrIgnore($dataArray)){
                        Notification::make()
                            ->success()
                            ->title('yeayy, success!')
                            ->body('Successfully added data')
                            ->send();
                    }
                }
                
            }),
        ];
    }
}
