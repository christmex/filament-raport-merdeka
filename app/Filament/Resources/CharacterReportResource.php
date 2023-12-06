<?php

namespace App\Filament\Resources;

use App\Models\Habit;
use Filament\Forms;
use Filament\Tables;
use App\Models\Student;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\CharacterReport;
use App\Models\StudentClassroom;
use Filament\Resources\Resource;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Tables\Columns\GradingTextInputColumn;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CharacterReportResource\Pages;
use App\Filament\Resources\CharacterReportResource\RelationManagers;

class CharacterReportResource extends Resource
{
    protected static ?string $model = CharacterReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->relationship('student', 'id')
                    ->required(),
                Forms\Components\Select::make('school_year_id')
                    ->relationship('schoolYear', 'id')
                    ->required(),
                Forms\Components\Select::make('school_term_id')
                    ->relationship('schoolTerm', 'id')
                    ->required(),
                Forms\Components\Select::make('habit_id')
                    ->relationship('habit', 'name')
                    ->required(),
                Forms\Components\Toggle::make('week')
                    ->required(),
                Forms\Components\Toggle::make('home'),
                Forms\Components\Toggle::make('school'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.student_name')
                    ->numeric()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('schoolYear.school_year_name')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('schoolTerm.school_term_name')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('habit.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('week'),
                GradingTextInputColumn::make('home')
                    ->type('number')
                    ->rules(['nullable','integer','min:0', 'max:16'])
                    ->sortable(),
                GradingTextInputColumn::make('school')
                    ->type('number')
                    ->rules(['nullable','integer','min:0', 'max:4'])
                    ->sortable(),
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
                SelectFilter::make('student_id')
                    ->label('Student')
                    ->options(function(){

                        $activeHomeroom = auth()->user()->activeHomeroom->first();
                        if($activeHomeroom){
                            $studentIds = StudentClassroom::query()
                            ->where('classroom_id',$activeHomeroom->classroom_id)
                            ->where('school_year_id',$activeHomeroom->school_year_id)
                            ->where('school_term_id',$activeHomeroom->school_term_id)
                            ->get()
                            ->pluck('student_id')
                            ->toArray();
    
                            return Student::whereIn('id',$studentIds)->get()->pluck('student_name','id');
                        }
                    })
                    ->searchable(),
                SelectFilter::make('habit_id')
                    ->label('habit')
                    ->options(Habit::all()->pluck('name','id')),
                SelectFilter::make('week')
                    ->label('Week')
                    ->options(function(){
                        $week = [];
                        for ($i=1; $i <= 17; $i++) { 
                            $week[$i] = $i;
                        }

                        return $week;
                    }),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->paginated([1,17])
            ->defaultPaginationPageOption(1)
            ;
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCharacterReports::route('/'),
        ];
    }    
}
