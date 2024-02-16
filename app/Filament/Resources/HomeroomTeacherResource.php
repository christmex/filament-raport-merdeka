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
use App\Models\HomeroomTeacher;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Unique;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\HomeroomTeacherResource\Pages;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Filament\Resources\HomeroomTeacherResource\RelationManagers;

class HomeroomTeacherResource extends Resource
{
    protected static ?string $model = HomeroomTeacher::class;

    protected static ?string $navigationIcon = 'heroicon-s-user-plus';

    protected static ?string $navigationLabel = 'Main Teacher';

    protected static ?string $navigationGroup = 'Main Configuration';

    protected static ?int $navigationSort = 3;

    
    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.id')
                    ->label('user ID')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->label('user name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email address copied')
                    ->copyMessageDuration(1500)
                    ->label('user email')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('classroom.classroom_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('classroom.school_level')
                    ->label('School Level')
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
                SelectFilter::make('school_year_id')
                    ->searchable()
                    ->preload()
                    ->optionsLimit(7)
                    ->relationship('schoolYear', 'school_year_name'),
                SelectFilter::make('school_term_id')
                    ->preload()
                    ->searchable()
                    ->optionsLimit(7)
                    ->relationship('schoolTerm', 'school_term_name'),
                SelectFilter::make('classroom_id')
                    ->preload()
                    ->searchable()
                    ->optionsLimit(7)
                    ->relationship('classroom', 'classroom_name'),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make(),
                    Tables\Actions\BulkAction::make('syncSchoolSetting')
                        ->icon('heroicon-s-cog')
                        ->form([
                            \Filament\Forms\Components\Select::make('school_year_id')
                                ->relationship('schoolYear', 'school_year_name')
                                ->searchable(['school_year_name'])
                                ->preload()
                                ->createOptionForm(SchoolYearResource::getForm())
                                ->editOptionForm(SchoolYearResource::getForm())
                                ->default(fn($state) => $state ?? SchoolYear::activeId())
                                ->required(),
                            \Filament\Forms\Components\Select::make('school_term_id')
                                ->relationship('schoolTerm', 'school_term_name')
                                ->searchable(['school_term_name'])
                                ->preload()
                                ->createOptionForm(SchoolTermResource::getForm())
                                ->editOptionForm(SchoolTermResource::getForm())
                                ->default(fn($state) => $state ?? SchoolTerm::activeId())
                                ->required(),
                        ])
                        ->action(function(array $data,$livewire){
                            DB::beginTransaction();
                            try {
                                $livewire->getSelectedTableRecords()->each(function ($item, $key) use($data) {
                                    HomeroomTeacher::firstOrCreate(
                                        [
                                            'school_year_id' => $data['school_year_id'],
                                            'school_term_id' => $data['school_term_id'],
                                            'classroom_id' => $item['classroom_id'],
                                            'user_id' => $item['user_id'],
                                        ],
                                    );
                                });
                                DB::commit();
                                Notification::make()
                                    ->success()
                                    ->title('Successfully sync to the new school year and school term')
                                    ->send();
                            } catch (\Throwable $th) {
                                DB::rollback();
                                Notification::make()
                                    ->danger()
                                    ->title($th->getMessage())
                                    ->send();
                            }
                        })
                        ->deselectRecordsAfterCompletion()
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

    public static function getForm(): array 
    {
        return [
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
        ];
    }
}
