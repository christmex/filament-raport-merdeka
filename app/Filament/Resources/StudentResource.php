<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Student;
use Filament\Forms\Form;
use App\Models\Classroom;
use App\Models\SchoolTerm;
use App\Models\SchoolYear;
use Filament\Tables\Table;
use Illuminate\Http\Request;
use App\Models\StudentClassroom;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\StudentResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StudentResource\RelationManagers;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


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
                Tables\Columns\TextColumn::make('activeClassroom.classroom_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('student_nis')
                    ->searchable(),
                Tables\Columns\TextColumn::make('student_nisn')
                    ->searchable(),
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            //
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

    public static function getForm(): array 
    {
        return [
            Forms\Components\TextInput::make('student_name')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('student_nis')
                ->maxLength(255),
            Forms\Components\TextInput::make('student_nisn')
                ->unique(ignoreRecord: true)
                ->maxLength(255),
        ];
    }
}
