<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SchoolTermResource\Pages;
use App\Filament\Resources\SchoolTermResource\RelationManagers;
use App\Models\SchoolTerm;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SchoolTermResource extends Resource
{
    protected static ?string $model = SchoolTerm::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema(self::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('school_term_name')
                    ->searchable(),
                Tables\Columns\IconColumn::make('school_term_status')
                    ->boolean(),
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
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                // Tables\Actions\CreateAction::make(),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSchoolTerms::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getForm(): array 
    {
        return [
            Forms\Components\TextInput::make('school_term_name')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            Forms\Components\Toggle::make('school_term_status')
                ->required(),
        ];
    }
}
