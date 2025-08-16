<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaritalstatusResource\Pages;
use App\Filament\Resources\MaritalstatusResource\RelationManagers;
use App\Models\Maritalstatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MaritalstatusResource extends Resource
{
    protected static ?string $model = Maritalstatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Marital Status';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                //
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
            'index' => Pages\ListMaritalstatuses::route('/'),
            'create' => Pages\CreateMaritalstatus::route('/create'),
            'edit' => Pages\EditMaritalstatus::route('/{record}/edit'),
        ];
    }
}
