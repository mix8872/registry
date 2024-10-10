<?php

namespace App\Filament\Clusters\Structure\Resources\CommonRelationManagers;

use App\Filament\Clusters\Structure\Resources\ContainerResource;
use App\Models\Repository;
use App\Models\Server;
use Filament\Forms;
use Livewire\Component as Livewire;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Actions\Action;

class ContainersRelationManager extends RelationManager
{
    protected static string $relationship = 'containers';
    protected static ?string $title = 'Контейнеры';
    protected static ?string $modelLabel = 'Контейнер';
    protected static ?string $pluralModelLabel = 'Контейнеры';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $icon = 'mdi-docker';

    protected static bool $isLazy = false;

    public function form(Form $form): Form
    {
        return $form->schema(ContainerResource::getFormFields());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Название'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->color('primary'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
