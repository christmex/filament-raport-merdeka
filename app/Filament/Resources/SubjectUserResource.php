<?php

namespace App\Filament\Resources;

use Closure;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use App\Models\SchoolTerm;
use App\Models\SchoolYear;
use Filament\Tables\Table;
use App\Models\SubjectUser;
use Filament\Resources\Resource;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Database\Eloquent\Builder;
use App\Tables\Columns\GradingTextInputColumn;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SubjectUserResource\Pages;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Filament\Resources\SubjectUserResource\RelationManagers;

class SubjectUserResource extends Resource
{
    protected static ?string $model = SubjectUser::class;

    protected static ?string $navigationIcon = 'heroicon-s-book-open';

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?string $navigationLabel = 'Teacher\'s Subject';

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
                    // ->mutateDehydratedState()
                    ->default(fn($state) => $state)
                    ->visibleOn('create')
                    ->unique(modifyRuleUsing: function (Unique $rule, Get $get) {
                        return $rule->where('school_year_id', $get('school_year_id'))
                                    ->where('school_term_id', $get('school_term_id'))
                                    ->where('classroom_id', $get('classroom_id'))
                                    ->where('subject_id', $get('subject_id'));
                    },ignoreRecord:true)
                    // ->rules([
                    //     fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                    //         $check = SubjectUser::where('user_id', $value)
                    //                 ->where('subject_id', $get('subject_id'))
                    //                 ->where('school_year_id', $get('school_year_id'))
                    //                 ->where('school_term_id', $get('school_term_id'))
                    //                 ->where('classroom_id', $get('classroom_id'))
                    //                 ->first();
                    //         if ($check) {
                    //             $fail("This user already teach that subject in that class.");
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
                    ->default(fn($state) => $state)
                    ->visibleOn('create')
                    ->unique(modifyRuleUsing: function (Unique $rule, Get $get) {
                        return $rule->where('school_year_id', $get('school_year_id'))
                                    ->where('school_term_id', $get('school_term_id'))
                                    ->where('subject_id', $get('subject_id'));
                    },ignoreRecord:true)
                    ->required(),
                Forms\Components\Select::make('subject_id')
                    ->live()
                    ->relationship('subject', 'subject_name')
                    ->searchable(['subject_name', 'subject_code'])
                    ->preload()
                    ->default(fn($state) => $state)
                    ->createOptionForm(SubjectResource::getForm())
                    ->editOptionForm(SubjectResource::getForm())
                    ->visible(fn() => auth()->user()->can('create_subject::user'))
                    // ->visibleOn('create')
                    ->required(),
                Forms\Components\TextInput::make('grade_minimum')
                    ->maxValue(75)
                    ->minValue(60)
                    ->numeric()
                    ->default(fn($state) => $state ?? 75)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('user name')
                    ->searchable()
                    ->wrap(true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('user email')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('subject.subject_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('classroom.classroom_name')
                    ->numeric()
                    ->sortable(),
                GradingTextInputColumn::make('grade_minimum')
                    ->type('number')
                    ->rules(['integer','min:60', 'max:75'])
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
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListSubjectUsers::route('/'),
            'create' => Pages\CreateSubjectUser::route('/create'),
            'edit' => Pages\EditSubjectUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->OwnSubject()
            // ->withoutGlobalScopes([
            //     SoftDeletingScope::class,
            // ])
            ;
    }
}
