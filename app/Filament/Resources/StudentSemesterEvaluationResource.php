<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\SubjectUser;
use Filament\Resources\Resource;
use App\Models\StudentSemesterEvaluation;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Tables\Columns\GradingTextInputColumn;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StudentSemesterEvaluationResource\Pages;
use App\Filament\Resources\StudentSemesterEvaluationResource\RelationManagers;

class StudentSemesterEvaluationResource extends Resource
{
    protected static ?string $model = StudentSemesterEvaluation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->relationship('student', 'id')
                    ->required(),
                Forms\Components\Select::make('subject_user_id')
                    ->relationship('subjectUser', 'id')
                    ->required(),
                Forms\Components\TextInput::make('grading')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.student_name')
                    ->searchable(isIndividual:true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.active_classroom_name')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault:true)
                    ->label('Classroom')
                    ,
                Tables\Columns\TextColumn::make('subjectUserThrough.subject_name')
                    ->toggleable()
                    ->label('Subject'),
                GradingTextInputColumn::make('grading')
                    ->type('number')
                    // ->rules(['required','integer','min:0', 'max:100'])
                    ->rules(['nullable','integer','min:0', 'max:100'])
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
            ->deferLoading()
            ->filters([
                SelectFilter::make('subject_user_id')
                    ->label('Subject')
                    ->options(SubjectUser::with('Subject')->ownSubject()->get()->pluck('subject_user_name','id'))
                    ->searchable()
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageStudentSemesterEvaluations::route('/'),
        ];
    }    
}
