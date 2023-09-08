<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssessmentResource\Pages;
use App\Filament\Resources\AssessmentResource\RelationManagers;
use App\Models\Assessment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssessmentResource extends Resource
{
    protected static ?string $model = Assessment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('student_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('assessment_method_setting_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('topic_setting_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('subject_user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('grading')
                    ->numeric(),
                Forms\Components\TextInput::make('topic_name')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assessment_method_setting_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('topic_setting_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject_user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('grading')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('topic_name')
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAssessments::route('/'),
        ];
    }    
}
