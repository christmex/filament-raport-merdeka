<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Assessment;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AssessmentResource\Pages;
use App\Filament\Resources\AssessmentResource\RelationManagers;

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
                Tables\Columns\TextColumn::make('student.student_name')
                    // ->description(fn (Assessment $record): string => $record->student->active_classroom_name)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.active_classroom_name')
                    ->label('Classroom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assessmentMethodSetting.assessment_method_setting_name')
                    ->label('Assessment Method')
                    ->sortable(),
                Tables\Columns\TextColumn::make('topicSetting.topic_setting_name')
                    ->label('Topic')
                    ->sortable(),
                Tables\Columns\TextColumn::make('topic_name')
                    ->searchable(isIndividual:true),
                Tables\Columns\TextColumn::make('subjectUserThrough.subject_name')
                    ->label('Subject')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('subject_user_id')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextInputColumn::make('grading')
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
                SelectFilter::make('topic_setting')
                    ->multiple()
                    ->preload()
                    ->optionsLimit(7)
                    ->relationship('topicSetting', 'topic_setting_name'),
                SelectFilter::make('assessmentMethodSetting')
                    ->multiple()
                    ->relationship('assessmentMethodSetting', 'assessment_method_setting_name'),
                SelectFilter::make('subject')
                    ->multiple()
                    ->relationship('subjectUserThrough', 'subject_name'),
                Filter::make('topic_name')
                // SelectFilter::make('subject_topic')
                //     ->relationship('topicSetting', 'topic_setting_name'),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateActions([
                // Tables\Actions\CreateAction::make(),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAssessments::route('/'),
        ];
    }    
}
