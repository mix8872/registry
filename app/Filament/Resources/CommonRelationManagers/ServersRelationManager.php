<?php

namespace App\Filament\Resources\CommonRelationManagers;

use App\Filament\Resources\ServerResource;
use App\Models\Repository;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Table;
use Livewire\Component as Livewire;

class ServersRelationManager extends RelationManager
{
    protected static string $relationship = 'servers';
    protected static ?string $title = 'Серверы';
    protected static ?string $modelLabel = 'Сервер';
    protected static ?string $pluralModelLabel = 'Серверы';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $icon = 'heroicon-s-server-stack';

    public function form(Form $form): Form
    {
        return $form->schema(array_merge(self::getRepositoryPivotFields(), ServerResource::getFormFields()));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Название'),
                Tables\Columns\TextColumn::make('type')
                    ->visible(fn (Livewire $livewire) => $livewire->ownerRecord instanceof Repository)
                    ->label('Тип'),
                Tables\Columns\TextColumn::make('url')
                    ->visible(fn (Livewire $livewire) => $livewire->ownerRecord instanceof Repository)
                    ->label('Url'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->form(fn(AttachAction $action): array => array_merge([$action->getRecordSelect()], self::getRepositoryPivotFields()))
                    ->preloadRecordSelect()
                    ->color('primary'),
                CreateAction::make()
                    ->form(fn(CreateAction $action): array => array_merge(self::getRepositoryPivotFields(), ServerResource::getFormFields()))
                    ->color('gray'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }

    public static function getRepositoryPivotFields()
    {
        return [
            Forms\Components\TextInput::make('type')
                ->visible(fn (Livewire $livewire) => $livewire->ownerRecord instanceof Repository)
                ->required()
                ->label('Тип'),
            Forms\Components\TextInput::make('url')
                ->visible(fn (Livewire $livewire) => $livewire->ownerRecord instanceof Repository)
                ->required()
                ->label('Url'),
        ];
    }
}
