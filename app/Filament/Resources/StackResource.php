<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommonRelationManagers\ProjectsRelationManager;
use App\Filament\Resources\StackResource\Pages;
use App\Filament\Resources\StackResource\RelationManagers;
use App\Models\Stack;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StackResource extends Resource
{
    protected static ?string $model = Stack::class;
    protected static ?string $modelLabel = 'Стек';
    protected static ?string $pluralModelLabel = 'Стек';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 5;

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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Название'),
                Tables\Columns\TextColumn::make('type')->label('Тип'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(Stack::$types)->label('Тип'),
            ])
            ->headerActions([
                // ...
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

    public static function getRelations(): array
    {
        return [
            ProjectsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStacks::route('/'),
            'create' => Pages\CreateStack::route('/create'),
            'edit' => Pages\EditStack::route('/{record}/edit'),
        ];
    }
}
