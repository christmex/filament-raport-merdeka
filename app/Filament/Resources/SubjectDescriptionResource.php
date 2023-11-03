<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\SubjectUser;
use Filament\Resources\Resource;
use App\Models\SubjectDescription;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SubjectDescriptionResource\Pages;
use App\Filament\Resources\SubjectDescriptionResource\RelationManagers;

class SubjectDescriptionResource extends Resource
{
    protected static ?string $model = SubjectDescription::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('subject_user_id')
                    ->label('subject')
                    ->options(SubjectUser::with('subject')->whereIn('id',auth()->user()->activeSubjects->pluck('id')->toArray())->get()->pluck('subject_user_name', 'id'))
                    ->required()
                    ->searchable()
                    ->selectablePlaceholder(false)
                    ->preload(),
                Forms\Components\Select::make('topic_setting_id')
                    ->relationship('topicSetting','topic_setting_name')
                    ->required()
                    ->searchable()
                    ->helperText('Topic 1 also called Chapter 1 or bab 1, etc, they are all the same 🤩')
                    ->preload(),
                Forms\Components\TextInput::make('range_start')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->required(),
                Forms\Components\TextInput::make('range_end')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->required(),
                // Forms\Components\TextInput::make('topic_name')
                //     ->maxLength(255),
                // Forms\Components\TextInput::make('predicate')
                //     ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('subjectUserThrough.subject_name')
                Tables\Columns\TextColumn::make('subjectUser.subject_user_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('topicSetting.topic_setting_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextInputColumn::make('range_start')
                    ->type('number')
                    ->rules(['required','integer','min:0', 'max:100']),
                Tables\Columns\TextInputColumn::make('range_end')
                    ->type('number')
                    ->rules(['required','integer','min:0', 'max:100']),
                Tables\Columns\TextColumn::make('topic_name')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('predicate')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('description')
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
                SelectFilter::make('subject_user_id')
                    ->label('Subject')
                    ->options(SubjectUser::with('Subject')->ownSubject()->get()->pluck('subject_user_name','id'))
                    ->searchable(),
                SelectFilter::make('topic_setting')
                    ->preload()
                    ->optionsLimit(7)
                    ->relationship('topicSetting', 'topic_setting_name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ManageSubjectDescriptions::route('/'),
        ];
    }    
}
