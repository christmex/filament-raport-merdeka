<?php

namespace App\Filament\Resources;

use App\Models\SubjectUser;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Assessment;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Tables\Columns\GradingTextInputColumn;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AssessmentResource\Pages;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Filament\Resources\AssessmentResource\RelationManagers;

class AssessmentResource extends Resource
{
    protected static ?string $model = Assessment::class;

    protected static ?string $navigationIcon = 'heroicon-s-document-duplicate';

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
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),
                Forms\Components\TextInput::make('topic_name')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('student.student_name')
                    ->wrap()
                    ->label('Student Name')
                    // ->description(fn (Assessment $record): string => $record->student->active_classroom_name)
                    ->searchable(isIndividual: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.active_classroom_name')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault:true)
                    ->label('Classroom')
                    // ->searchable()
                    // ->sortable()
                    ,
                Tables\Columns\TextColumn::make('assessmentMethodSetting.assessment_method_setting_name')
                    ->toggleable()
                    ->wrap()
                    ->label('Assessment Method')
                    ->sortable(),
                Tables\Columns\TextColumn::make('topicSetting.topic_setting_name')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault:true)
                    ->label('Topic')
                    ->sortable(),
                Tables\Columns\TextColumn::make('topic_name')
                    ->toggleable()
                    ->wrap()
                    ->searchable(isIndividual: true),
                Tables\Columns\TextColumn::make('subjectUserThrough.subject_name')
                    ->toggleable()
                    ->label('Subject')
                    // ->searchable()
                    ,
                // Tables\Columns\TextColumn::make('subject_user_id')
                //     ->numeric()
                //     ->sortable(),
                GradingTextInputColumn::make('grading')
                    ->type('number')
                    ->rules(['required','integer','min:0', 'max:100'])
                    ->sortable(),
                // Tables\Columns\TextInputColumn::make('grading')
                //     ->type('number')
                //     ->rules(['integer','min:0', 'max:100'])
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
            ->deferLoading()
            ->filters([
                SelectFilter::make('subject_user_id')
                    ->label('Subject')
                    ->options(SubjectUser::with('Subject')->ownSubject()->get()->pluck('subject_user_name','id'))
                    ->searchable()
                    // ->relationship('subjectUser', 'id')
                    // ->preload()
                    // ->optionsLimit(5)
                    // ->multiple()
                    ,
                // SelectFilter::make('student')
                //     ->multiple()
                //     ->preload()
                //     ->optionsLimit(5)
                //     ->relationship('student', 'student_name'),
                SelectFilter::make('topic_setting')
                    // ->multiple()
                    ->preload()
                    ->optionsLimit(7)
                    ->relationship('topicSetting', 'topic_setting_name'),
                SelectFilter::make('assessmentMethodSetting')
                    // ->multiple()
                    ->preload()
                    ->optionsLimit(5)
                    ->relationship('assessmentMethodSetting', 'assessment_method_setting_name'),
                
                // SelectFilter::make('classroom')
                //     ->multiple()
                //     ->preload()
                //     ->optionsLimit(5)
                //     // ->baseQuery(function(Builder $query, $livewire){
                //     //     $query->whereIn('id',$livewire->tableFilters['subject']['values']);
                //     //     // dd();
                //     // })
                //     ->relationship('classroomSubjectUserThrough', 'classroom_name'),

            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->deselectRecordsAfterCompletion(),
                    ExportBulkAction::make()
                ]),
            ])
            ->emptyStateActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->defaultPaginationPageOption(50);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAssessments::route('/'),
        ];
    }

}
