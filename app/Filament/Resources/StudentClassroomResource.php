<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentClassroomResource\Pages;
use App\Filament\Resources\StudentClassroomResource\RelationManagers;
use App\Models\StudentClassroom;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentClassroomResource extends Resource
{
    protected static ?string $model = StudentClassroom::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
