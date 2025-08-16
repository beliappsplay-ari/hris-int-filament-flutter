<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmpMasterResource\Pages;
use App\Filament\Resources\EmpMasterResource\RelationManagers;
use App\Models\Emp_Master;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use function Laravel\Prompts\form;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use App\Filament\Pages\Auth;

class EmpMasterResource extends Resource
{
    protected static ?string $model = emp_master::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Employee';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('User')
                    ->helperText('Select the user account for this employee')
                    ->live() // Enable real-time updates
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        // Auto-fill fullname ketika user dipilih
                        if ($state) {
                            $user = User::find($state);
                            if ($user) {
                                $set('fullname', $user->name);
                            }
                        }
                    }),
                    
                Forms\Components\TextInput::make('empno'),
                 Forms\Components\TextInput::make('fullname')
                    ->required()
                    ->label('Full Name')
                    ->helperText('Auto-filled from selected user, but can be modified')
                    ->disabled(fn (Forms\Get $get) => !$get('user_id')) // Disable jika user belum dipilih
                    ->dehydrated(), // Ensure value is saved even when disabled
                    
                Forms\Components\Select::make('city_id')
                ->relationship('city', 'name')
                ->required(),
                Forms\Components\Select::make('gender_id')
                ->relationship('gender', 'name')
                ->required(),
                Forms\Components\Select::make('maritalstatus_id')
                ->relationship('maritalstatus', 'name')
                ->required(),
                Forms\Components\Select::make('nationality_id')
                ->relationship('nationality', 'name')
                ->required(),
                Forms\Components\Select::make('religion_id')
                ->relationship('religion', 'name')
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('empno')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('fullname')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('city.name')->label('City')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('gender.name')->label('Jenis Kelamin')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('nationality.name')->label('Kewarganegaraan')->searchable()->sortable(),

                Tables\Columns\TextColumn::make('religion.name')->label('Agama')->searchable()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('religion_id')
                ->relationship('religion', 'name'),
                Tables\Filters\SelectFilter::make('nationality_id')
                ->relationship('nationality', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListEmpMasters::route('/'),
            'create' => Pages\CreateEmpMaster::route('/create'),
            'edit' => Pages\EditEmpMaster::route('/{record}/edit'),
        ];
    }
}
