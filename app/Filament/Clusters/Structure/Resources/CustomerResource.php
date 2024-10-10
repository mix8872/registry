<?php

namespace App\Filament\Clusters\Structure\Resources;

use App\Filament\Clusters\Structure;
use App\Filament\Clusters\Structure\Resources\CustomerResource\Pages;
use App\Filament\Clusters\Structure\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $navigationIcon = 'mdi-account-multiple-outline';
    protected static ?int $navigationSort = 99;
    protected static ?string $modelLabel = 'Заказчик';
    protected static ?string $pluralModelLabel = 'Заказчики';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $cluster = Structure::class;

    public static function form(Form $form): Form
    {
        return $form->schema(self::getFormFields());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Название')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->slideOver()->modalWidth(MaxWidth::SevenExtraLarge),
                Tables\Actions\EditAction::make()->icon('mdi-pencil')->slideOver()->modalWidth(MaxWidth::SevenExtraLarge),
                Tables\Actions\DeleteAction::make()->icon('mdi-close-thick')->requiresConfirmation(),
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
            'index' => Pages\ListCustomers::route('/'),
//            'create' => Pages\CreateCustomer::route('/create'),
//            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getFormFields()
    {
        return [
            Forms\Components\TextInput::make('name')->label('Название')
        ];
    }
}
