<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Subject;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\SubjectGroup;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SubjectResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use IbrahimBougaoua\FilamentSortOrder\Actions\UpStepAction;
use App\Filament\Resources\SubjectResource\RelationManagers;
use IbrahimBougaoua\FilamentSortOrder\Actions\DownStepAction;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    protected static ?string $navigationIcon = 'heroicon-m-archive-box';

    protected static ?string $navigationGroup = 'Master';

    public static function form(Form $form): Form
    {
        return $form->schema(self::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject_code')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ToggleColumn::make('is_curiculum_basic')
                    ->sortable(),
                Tables\Columns\SelectColumn::make('subject_group_id')
                ->options(fn()=> SubjectGroup::all()->pluck('name','id')),
                Tables\Columns\TextInputColumn::make('sort_order')
                    ->rules(['required','integer','min:0', 'max:100'])
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
                // Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
                // Tables\Actions\ForceDeleteAction::make(),
                // Tables\Actions\RestoreAction::make(),
                // DownStepAction::make(),
                // UpStepAction::make(),
            ])
            ->defaultSort('sort_order', 'asc')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                    // Tables\Actions\ForceDeleteBulkAction::make(),
                    // Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSubjects::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            // ->orderByDesc('created_at')
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getForm(): array 
    {
        return [
            Forms\Components\TextInput::make('subject_name')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            Forms\Components\TextInput::make('subject_code')
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            Forms\Components\Select::make('subject_group_id')
                ->relationship(name: 'subjectGroup', titleAttribute: 'name'),
            Forms\Components\Toggle::make('is_curiculum_basic')
                ->onColor('success'),
        ];
    }
}
