<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use App\Models\SchoolTerm;
use App\Models\SchoolYear;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class DescriptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'descriptions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('school_year_id')
                    ->options(SchoolYear::all()->pluck('school_year_name','id'))
                    ->searchable(['school_year_name'])
                    ->preload()
                    ->live()
                    ->default(fn($state) => $state ?? SchoolYear::activeId())
                    ->required(),
                Forms\Components\Select::make('school_term_id')
                    ->options(SchoolTerm::all()->pluck('school_term_name','id'))
                    ->searchable(['school_term_name'])
                    ->preload()
                    ->live()
                    ->default(fn($state) => $state ?? SchoolTerm::activeId())
                    ->unique(modifyRuleUsing: function (Unique $rule, Get $get){
                        return $rule->where('school_year_id', $get('school_year_id'))
                                    ->where('school_term_id', $get('school_term_id'))
                                    ->where('student_id', $this->ownerRecord->id);
                    },ignoreRecord:true)
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                Tables\Columns\TextColumn::make('description'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
