<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Student;
use Filament\Forms\Get;
use Filament\Forms\Form;
use App\Models\Classroom;
use App\Models\SchoolTerm;
use App\Models\SchoolYear;
use Filament\Tables\Table;
use Illuminate\Http\Request;
use App\Models\Extracurricular;
use App\Models\StudentClassroom;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Tabs;
use App\Models\StudentExtracurricular;
use Filament\Forms\Components\Repeater;
use Illuminate\Validation\Rules\Unique;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\ActionsPosition;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\StudentResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Filament\Resources\StudentResource\RelationManagers;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-s-user-group';


    public static function form(Form $form): Form
    {
        return $form->schema(self::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student_name')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('activeStudentClassrooms.homeroomTeacher.classroom.classroom_name')
                Tables\Columns\TextColumn::make('activeStudentClassrooms.classroom.classroom_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('activeExtracurriculars.extracurricular.name')
                    ->listWithLineBreaks(),
                Tables\Columns\TextColumn::make('activeExtracurriculars.description')
                    ->listWithLineBreaks(),
                Tables\Columns\TextColumn::make('student_nis')
                    ->searchable(),
                Tables\Columns\TextColumn::make('student_nisn')
                    ->searchable(),
                Tables\Columns\TextColumn::make('born_place')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('born_date')
                    ->searchable()
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('religion.name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status_in_family')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sibling_order_in_family')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('previous_education')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('father_name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('mother_name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('parent_address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('parent_phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('father_job')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('mother_job')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('guardian_name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('guardian_address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('guardian_phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('guardian_job')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),


                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                    // Tables\Actions\Action::make('add_description_extracurricular')
                    //     ->form([
                    //         Repeater::make('extracurriculars')
                    //             ->relationship()
                    //             ->schema([
                    //                 // Forms\Components\Select::make('school_year_id')
                    //                 //     ->relationship('schoolYear', 'school_year_name')
                    //                 //     ->required(),
                    //                 Forms\Components\Select::make('extracurricular_id')
                    //                     ->relationship('extracurricular', 'name')
                    //                     ->required(),
                    //             ])
                    //             ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {
                    //                 // dd($livewire);
                    //                 $data['extracurricular_id'] = 1;
                             
                    //                 return $data;
                    //             })
                    //     ])
                    //     ->action(function(){

                    //     }),
                    Tables\Actions\Action::make('print_progress_report')
                        ->url(fn (Student $record): string => route('students.print', $record))
                        ->openUrlInNewTab()
                        ->icon('heroicon-o-printer'),
                    
                    Tables\Actions\Action::make('detail_progress_report')
                        ->url(fn (Student $record): string => route('students.print', ['student' => $record,'detailed' => true]))
                        ->openUrlInNewTab()
                        ->icon('heroicon-o-eye'),
                    Tables\Actions\Action::make('print_raport')
                        ->form([
                            Forms\Components\TextInput::make('fase')
                                ->required(),
                            Forms\Components\Toggle::make('newPageAfterFirstTabel'),
                        ])
                        ->action(function(array $data, Student $record){
                            return redirect()->route('students.print-raport', [$record->id, 'fase' => $data['fase'],'newPageAfterFirstTabel'=>$data['newPageAfterFirstTabel']]);
                        })
                        // ->url(fn (Student $record): string => route('students.print-raport', $record))
                        // ->openUrlInNewTab()
                        ->icon('heroicon-o-printer'),
                    // Tables\Actions\Action::make('print_raport_cover')
                    //     ->url(fn (Student $record): string => route('students.print-raport-cover', $record))
                    //     ->openUrlInNewTab()
                    //     ->icon('heroicon-o-printer'),
                ])
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\BulkAction::make('add_extracurricular')
                    ->form([
                        Forms\Components\Select::make('school_year_id')
                            ->options(SchoolYear::all()->pluck('school_year_name','id'))
                            ->searchable(['school_year_name'])
                            ->preload()
                            ->default(fn($state) => $state ?? SchoolYear::activeId())
                            ->required(),
                        Forms\Components\Select::make('school_term_id')
                            ->options(SchoolTerm::all()->pluck('school_term_name','id'))
                            ->searchable(['school_term_name'])
                            ->preload()
                            ->default(fn($state) => $state ?? SchoolTerm::activeId())
                            ->required(),
                        Forms\Components\Select::make('extracurricular_id')
                            ->options(Extracurricular::all()->pluck('name','id'))
                            ->searchable(['name'])
                            ->preload()
                            ->required(),
                    ])
                    ->action(function(array $data, $livewire){
                        // dd($data, $livewire->selectedTableRecords);
                            DB::beginTransaction();
                            try {
                                foreach ($livewire->selectedTableRecords as $key => $value) {
                                    StudentExtracurricular::firstOrCreate([
                                        'student_id' => $value,
                                        'extracurricular_id' => $data['extracurricular_id'],
                                        'school_year_id' => $data['school_year_id'],
                                        'school_term_id' => $data['school_term_id'],
                                    ]);
                                }
                                DB::commit();
                                Notification::make()
                                    ->success()
                                    ->title('Success')
                                    ->send();
                            } catch (\Throwable $th) {
                                DB::rollback();
                                Notification::make()
                                    ->danger()
                                    ->title($th->getMessage())
                                    ->send();
                            }

                    })
                    ->deselectRecordsAfterCompletion(),
                    // Tables\Actions\BulkAction::make('promoted_students')
                    //     ->form([
                    //         Forms\Components\Select::make('school_year_id')
                    //             ->live()
                    //             ->options(SchoolYear::all()->pluck('school_year_name', 'id'))
                    //             ->searchable(['school_year_name'])
                    //             ->default(fn($state) => $state)
                    //             ->required(),
                    //         Forms\Components\Select::make('school_term_id')
                    //             ->live()
                    //             ->options(SchoolTerm::all()->pluck('school_term_name', 'id'))
                    //             ->searchable(['school_term_name'])
                    //             ->default(fn($state) => $state)
                    //             ->required(),
                    //         Forms\Components\Select::make('classroom_id')
                    //             ->live()
                    //             ->options(function($livewire){
                    //                 // dd($livewire->getSelectedTableRecords()->each()->classrooms);
                    //                 return Classroom::all()
                    //                 ->pluck('classroom_name', 'id');
                    //             })
                    //             ->searchable(['classroom_name'])
                    //             ->default(fn($state) => $state)
                    //             ->required(),
                    //     ])
                    //     ->action(function(Collection $records, $data){
                    //         DB::beginTransaction();
                    //         try {
                    //             foreach ($records as $key => $value) {
                    //                 StudentClassroom::create([
                    //                     'student_id' => $value->id,
                    //                     'classroom_id' => $data['classroom_id'],
                    //                     'school_year_id' => SchoolYear::active(),
                    //                     'school_term_id' => SchoolTerm::active(),
                    //                 ]);
                    //             }
                    //             DB::commit();
                    //             Notification::make()
                    //                 ->success()
                    //                 ->title('Success')
                    //                 ->send();
                    //         } catch (\Throwable $th) {
                    //             DB::rollback();
                    //             Notification::make()
                    //                 ->danger()
                    //                 ->title($th->getMessage())
                    //                 ->send();
                    //         }
                            
                    //     })
                    //     ->deselectRecordsAfterCompletion()
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            RelationManagers\ExtracurricularsRelationManager::class,
            RelationManagers\AbsenceRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }

    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->ownStudent()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getForm(): array 
    {
        return [
            Tabs::make('Label')
            ->tabs([
                Tabs\Tab::make('Student Details')
                    ->schema([
                        Forms\Components\TextInput::make('student_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('student_nis')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('student_nisn')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
            
                        Forms\Components\TextInput::make('born_place')->maxLength(255),
                        Forms\Components\DatePicker::make('born_date'),
                        Forms\Components\Select::make('sex')
                        ->options([
                            0 => 'Perempuan',
                            1 => 'Laki-Laki'
                        ]),
                        Forms\Components\Select::make('religion_id')
                            ->relationship('religion','name'),
                        Forms\Components\TextInput::make('status_in_family')->maxLength(255)->placeholder('Ex: Anak'),
                        Forms\Components\TextInput::make('sibling_order_in_family')->integer()->minValue(1)->placeholder('Ex: 1'),
                        Forms\Components\Textarea::make('address'),
                        Forms\Components\TextInput::make('phone')->tel(),
                        Forms\Components\TextInput::make('previous_education')->maxLength(255),
                    ]),
                Tabs\Tab::make('Parent Details')
                    ->schema([
                        Forms\Components\TextInput::make('father_name')->maxLength(255),
                        Forms\Components\TextInput::make('mother_name')->maxLength(255),
                        Forms\Components\Textarea::make('parent_address'),
                        Forms\Components\TextInput::make('parent_phone')->tel(),
                        Forms\Components\TextInput::make('father_job')->maxLength(255),
                        Forms\Components\TextInput::make('mother_job')->maxLength(255),
                    ]),
                Tabs\Tab::make('Guardian Details')
                    ->schema([
                        Forms\Components\TextInput::make('guardian_name')->maxLength(255),
                        Forms\Components\Textarea::make('guardian_address'),
                        Forms\Components\TextInput::make('guardian_phone')->tel(),
                        Forms\Components\TextInput::make('guardian_job')->maxLength(255),
                    ]),
            ])->columnSpanFull(),

        ];
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     if(auth()->user()->email != 'super@sekolahbasic.sch.id'){
    //         if(auth()->user()->activeHomeroom->count()){
    //             $userHomeroom = auth()->user()->activeHomeroom->first()->classroom_id;
    //             return parent::getEloquentQuery()->whereHas('classrooms',function($q) use($userHomeroom){
    //                 $q->where('classroom_id', $userHomeroom);
    //             });
    //         }
    //     }

    //     return parent::getEloquentQuery();
    // }
}
