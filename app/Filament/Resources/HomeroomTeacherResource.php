<?php

namespace App\Filament\Resources;

use Closure;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\HomeroomTeacher;
use Filament\Resources\Resource;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\HomeroomTeacherResource\Pages;
use App\Filament\Resources\HomeroomTeacherResource\RelationManagers;
use App\Models\SchoolTerm;
use App\Models\SchoolYear;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class HomeroomTeacherResource extends Resource
{
    protected static ?string $model = HomeroomTeacher::class;

    protected static ?string $navigationIcon = 'heroicon-s-user-plus';

    protected static ?string $navigationLabel = 'Main Teacher';

    protected static ?string $navigationGroup = 'Configuration';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('school_year_id')
                    ->live()
                    ->relationship('schoolYear', 'school_year_name')
                    ->searchable(['school_year_name'])
                    ->preload()
                    ->createOptionForm(SchoolYearResource::getForm())
                    ->editOptionForm(SchoolYearResource::getForm())
                    ->default(fn($state) => $state ?? SchoolYear::activeId())
                    ->visibleOn('create')
                    ->required(),
                Forms\Components\Select::make('school_term_id')
                    ->live()
                    ->relationship('schoolTerm', 'school_term_name')
                    ->searchable(['school_term_name'])
                    ->preload()
                    ->createOptionForm(SchoolTermResource::getForm())
                    ->editOptionForm(SchoolTermResource::getForm())
                    ->default(fn($state) => $state ?? SchoolTerm::activeId())
                    ->visibleOn('create')
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable(['name', 'email'])
                    ->preload()
                    ->createOptionForm(UserResource::getForm())
                    ->editOptionForm(UserResource::getForm())
                    ->unique(modifyRuleUsing: function (Unique $rule, Get $get) {
                        return $rule->where('school_year_id', $get('school_year_id'))
                                    ->where('school_term_id', $get('school_term_id'));
                    },ignoreRecord:true)
                    // ->visibleOn('create')
                    // ->rules([
                    //     fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                    //         $check = HomeroomTeacher::query()
                    //                 ->where('user_id', $value)
                    //                 // ->whereNot('user_id', $value)
                    //                 ->orWhere('classroom_id', $get('classroom_id'))
                    //                 ->where('school_year_id', $get('school_year_id'))
                    //                 ->where('school_term_id', $get('school_term_id'))
                    //                 ->first();
                            
                    //         if ($check) {
                    //             $fail("This user already became homeroon teacher.");
                    //         }
                    //     },
                    // ])
                    ->required(),
                Forms\Components\Select::make('classroom_id')
                    ->live()
                    ->relationship('classroom', 'classroom_name')
                    ->searchable(['classroom_name'])
                    ->preload()
                    ->createOptionForm(ClassroomResource::getForm())
                    ->editOptionForm(ClassroomResource::getForm())
                    ->visibleOn('create')
                    ->unique(modifyRuleUsing: function (Unique $rule, Get $get) {
                        return $rule->where('school_year_id', $get('school_year_id'))
                                    ->where('school_term_id', $get('school_term_id'));
                    },ignoreRecord:true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.id')
                    ->label('user ID')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('user name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('user email')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('classroom.classroom_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('schoolYear.school_year_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('schoolTerm.school_term_name')
                    ->numeric()
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
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHomeroomTeachers::route('/'),
            'create' => Pages\CreateHomeroomTeacher::route('/create'),
            'edit' => Pages\EditHomeroomTeacher::route('/{record}/edit'),
        ];
    }    
}
