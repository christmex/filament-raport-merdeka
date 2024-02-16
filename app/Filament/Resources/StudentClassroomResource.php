<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\SchoolTerm;
use App\Models\SchoolYear;
use Filament\Tables\Table;
use App\Models\StudentClassroom;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StudentClassroomResource\Pages;
use App\Filament\Resources\StudentClassroomResource\RelationManagers;

class StudentClassroomResource extends Resource
{
    protected static ?string $model = StudentClassroom::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Configuration';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->relationship('student', 'id')
                    ->required(),
                Forms\Components\Select::make('school_term_id')
                    ->relationship('schoolTerm', 'id'),
                Forms\Components\Select::make('school_year_id')
                    ->relationship('schoolYear', 'id'),
                Forms\Components\Select::make('classroom_id')
                    ->relationship('classroom', 'id'),
                Forms\Components\Select::make('homeroom_teacher_id')
                    ->relationship('homeroomTeacher', 'id')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.student_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('schoolTerm.school_term_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('schoolYear.school_year_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('classroom.classroom_name')
                    ->searchable()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('homeroomTeacher.classroom.classroom_name')
                //     ->numeric()
                //     ->sortable(),
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
                SelectFilter::make('school_year_id')
                    ->searchable()
                    ->preload()
                    ->optionsLimit(7)
                    ->relationship('schoolYear', 'school_year_name'),
                SelectFilter::make('school_term_id')
                    ->preload()
                    ->searchable()
                    ->optionsLimit(7)
                    ->relationship('schoolTerm', 'school_term_name'),
                SelectFilter::make('classroom_id')
                    ->preload()
                    ->searchable()
                    ->optionsLimit(7)
                    ->relationship('classroom', 'classroom_name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('movingClass')
                    ->form([
                        \Filament\Forms\Components\Select::make('school_year_id')
                            ->relationship('schoolYear', 'school_year_name')
                            ->searchable(['school_year_name'])
                            ->preload()
                            ->createOptionForm(SchoolYearResource::getForm())
                            ->editOptionForm(SchoolYearResource::getForm())
                            ->default(fn($state) => $state ?? SchoolYear::activeId())
                            ->required(),
                        \Filament\Forms\Components\Select::make('school_term_id')
                            ->relationship('schoolTerm', 'school_term_name')
                            ->searchable(['school_term_name'])
                            ->preload()
                            ->createOptionForm(SchoolTermResource::getForm())
                            ->editOptionForm(SchoolTermResource::getForm())
                            ->default(fn($state) => $state ?? SchoolTerm::activeId())
                            ->required(),
                        \Filament\Forms\Components\Select::make('classroom_id')
                            ->relationship('classroom', 'classroom_name')
                            ->searchable(['classroom_name'])
                            ->preload()
                            ->createOptionForm(ClassroomResource::getForm())
                            ->editOptionForm(ClassroomResource::getForm())
                            ->default(fn($state) => $state)
                            ->required(),
                    ])
                    ->action(function(array $data,$livewire){
                        // dd($livewire->getSelectedTableRecords()->pluck('id')->toArray());
                        // dd($livewire->selectedTableRecords);
                        DB::beginTransaction();
                        // $records = $livewire->getSelectedTableRecords()
                        //     ->where('school_year_id','!=',$data['school_year_id'])
                        //     ->where('school_term_id','!=',$data['school_term_id'])
                        //     ->where('classroom_id','!=',$data['classroom_id'])
                        //     ;
                        // dd($records, $data, $livewire->getSelectedTableRecords());
                        try {
                            foreach ($livewire->getSelectedTableRecords() as $record) {

                                StudentClassroom::firstOrCreate(
                                    [
                                        'student_id' => $record->student_id,
                                        'school_year_id' => $data['school_year_id'],
                                        'school_term_id' => $data['school_term_id'],
                                        'classroom_id' => $data['classroom_id'],
                                    ]
                                );
                            DB::commit();
                            }
                            Notification::make()
                                ->success()
                                ->title('Student added to the selected classroom')
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
                    Tables\Actions\BulkAction::make('syncSchoolSetting')
                        ->icon('heroicon-s-cog')
                        ->form([
                            \Filament\Forms\Components\Select::make('school_year_id')
                                ->relationship('schoolYear', 'school_year_name')
                                ->searchable(['school_year_name'])
                                ->preload()
                                ->createOptionForm(SchoolYearResource::getForm())
                                ->editOptionForm(SchoolYearResource::getForm())
                                ->default(fn($state) => $state ?? SchoolYear::activeId())
                                ->required(),
                            \Filament\Forms\Components\Select::make('school_term_id')
                                ->relationship('schoolTerm', 'school_term_name')
                                ->searchable(['school_term_name'])
                                ->preload()
                                ->createOptionForm(SchoolTermResource::getForm())
                                ->editOptionForm(SchoolTermResource::getForm())
                                ->default(fn($state) => $state ?? SchoolTerm::activeId())
                                ->required(),
                        ])
                        ->action(function(array $data,$livewire){
                            DB::beginTransaction();
                            try {
                                $livewire->getSelectedTableRecords()->each(function ($item, $key) use($data) {
                                    StudentClassroom::firstOrCreate(
                                        [
                                            'school_year_id' => $data['school_year_id'],
                                            'school_term_id' => $data['school_term_id'],
                                            'classroom_id' => $item['classroom_id'],
                                            'student_id' => $item['student_id'],
                                        ],
                                    );
                                });
                                DB::commit();
                                Notification::make()
                                    ->success()
                                    ->title('Successfully sync to the new school year and school term')
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
                ]),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentClassrooms::route('/'),
            'create' => Pages\CreateStudentClassroom::route('/create'),
            'edit' => Pages\EditStudentClassroom::route('/{record}/edit'),
        ];
    }    
}
