<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RaportConfigResource\Pages;
use App\Filament\Resources\RaportConfigResource\RelationManagers;
use App\Models\RaportConfig;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RaportConfigResource extends Resource
{
    protected static ?string $model = RaportConfig::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    public function mount(): void
    {
        // this is doenst work
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\KeyValue::make('meta_key'),
                Forms\Components\TextInput::make('meta_key')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('meta_value')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('meta_key')
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
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRaportConfigs::route('/'),
        ];
    }    
}
