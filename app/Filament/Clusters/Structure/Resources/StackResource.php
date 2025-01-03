<?php

namespace App\Filament\Clusters\Structure\Resources;

use App\Filament\Clusters\Structure;
use App\Filament\Clusters\Structure\Resources\StackResource\Pages;
use App\Filament\Clusters\Structure\Resources\StackResource\RelationManagers;
use App\Livewire\ListProjects;
use App\Models\Stack;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;

class StackResource extends Resource
{
    protected static ?string $model = Stack::class;
    protected static ?string $modelLabel = 'Стек';
    protected static ?string $pluralModelLabel = 'Стек';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 5;
    protected static ?string $cluster = Structure::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()->maxLength(255)
                    ->label('Название'),
                Forms\Components\ToggleButtons::make('type')
                    ->options(Stack::$types)->inline()
                    ->required()->label('Тип'),
                Forms\Components\Livewire::make(ListProjects::class, ['model' => $form->model, 'operation' => $form->getOperation()])
                    ->disabled(function (Stack $r) {
                        return !$r->exists();
                    })
                    ->hidden(function (Stack $r) {
                        return !$r->exists();
                    })
                    ->columnSpanFull()
                    ->label('Проекты'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Название'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Тип'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(Stack::$types)->label('Тип'),
            ])
            ->headerActions([
                // ...
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->slideOver()->modalWidth(MaxWidth::SevenExtraLarge)
                ->size(ActionSize::ExtraLarge)->label(''),
                Tables\Actions\EditAction::make()->icon('mdi-pencil')->slideOver()->modalWidth(MaxWidth::SevenExtraLarge)
                ->size(ActionSize::ExtraLarge)->label(''),
                Tables\Actions\DeleteAction::make()->icon('mdi-close-thick')->requiresConfirmation()
                ->size(ActionSize::ExtraLarge)->label(''),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->poll('5s');
    }

    public static function getRelations(): array
    {
        return [
//            ProjectsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStacks::route('/'),
            'create' => Pages\CreateStack::route('/create'),
//            'edit' => Pages\EditStack::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
