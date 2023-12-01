<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\SchoolSetting;
use Filament\Resources\Resource;
use Filament\Forms\Components\Tabs;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SchoolSettingResource\Pages;
use App\Filament\Resources\SchoolSettingResource\RelationManagers;

class SchoolSettingResource extends Resource
{
    protected static ?string $model = SchoolSetting::class;

    protected static ?string $navigationIcon = 'heroicon-s-cog';

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('School')
                            ->schema([
                                Forms\Components\TextInput::make('school_name_prefix')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('school_name_suffix')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('school_address')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('school_principal_name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('school_progress_report_date')
                                    ->label('Print Date')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('sumatif_avg')
                                    ->numeric()
                                    ->live()
                                    ->suffix('%')
                                    ->minValue(0)
                                    ->maxValue(fn(Get $get)=> 100 - $get('pas_avg')),
                                Forms\Components\TextInput::make('pas_avg')
                                    ->numeric()
                                    ->minValue(0)
                                    ->live()
                                    ->suffix('%')
                                    ->maxValue(fn(Get $get)=> 100 - $get('sumatif_avg')),
                                // Forms\Components\TextInput::make('school_principal_signature')
                                //     ->maxLength(255),
                                Forms\Components\TextInput::make('npsn')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('nis_nss_nds')
                                    ->label('NIS/NSS/NDS')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('telp')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('postal_code')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('village')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('subdistrict')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('city')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('province')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('website')
                                    ->required()
                                    ->default('https://')
                                    ->url()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->required()
                                    ->email()
                                    ->maxLength(255),
                            ]),
                        Tabs\Tab::make('Meta')
                            ->schema([
                                Forms\Components\KeyValue::make('meta')
                            ]),
                    ])
                    ->contained(false)
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('school_name_prefix')
                    ->searchable(),
                Tables\Columns\TextColumn::make('school_name_suffix')
                    ->searchable(),
                Tables\Columns\TextColumn::make('school_address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('school_principal_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('school_progress_report_date')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('school_principal_signature')
                //     ->searchable(),
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
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                // Tables\Actions\CreateAction::make(),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSchoolSettings::route('/'),
        ];
    }    
}
