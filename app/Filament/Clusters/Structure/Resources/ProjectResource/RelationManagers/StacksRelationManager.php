<?php

namespace App\Filament\Clusters\Structure\Resources\ProjectResource\RelationManagers;

use App\Models\Stack;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class StacksRelationManager extends RelationManager
{
    protected static string $relationship = 'stacks';
    protected static ?string $modelLabel = 'Стек';
    protected static ?string $pluralModelLabel = 'Стек';
    protected static ?string $title = 'Стек';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $icon = 'heroicon-o-rectangle-stack';

    protected static bool $isLazy = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)->label('Название'),
                Forms\Components\ToggleButtons::make('type')
                    ->options(Stack::$types)->inline()
                    ->required()->label('Тип'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Название'),
                Tables\Columns\TextColumn::make('type')->label('Тип'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(Stack::$types)->label('Тип'),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()->multiple()->preloadRecordSelect()->color('primary'),
                Tables\Actions\CreateAction::make()->color('gray'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->icon('mdi-pencil'),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
