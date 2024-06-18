<?php

namespace App\Filament\Resources\CommonRelationManagers;

use App\Models\Repository;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Livewire\Component as Livewire;

class RepositoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'repositories';
    protected static ?string $modelLabel = 'Репозиторий';
    protected static ?string $pluralModelLabel = 'Репозитории';
    protected static ?string $title = 'Репозитории';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $icon = 'mdi-git';

    protected static bool $isLazy = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)->label('Название'),
                Forms\Components\TextInput::make('url')
                    ->required()
                    ->maxLength(255)->label('Ссылка'),
                Forms\Components\Textarea::make('comment')->rows(2)->columnSpanFull()->label('Примечание'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->description(fn(Repository $r): string => $r->comment ?? '')
                    ->searchable()
                    ->sortable()
                    ->label('Название'),
                Tables\Columns\TextColumn::make('url')
                    ->sortable()
                    ->url(fn(Repository $r): string => $r->url ?? '', true)
                    ->label('Ссылка'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->multiple()
                    ->preloadRecordSelect()
                    ->color('primary')
                    ->hidden(fn (Livewire $livewire) => $livewire->ownerRecord instanceOf \App\Models\Project),
                Tables\Actions\CreateAction::make()->color(fn (Livewire $livewire) => $livewire->ownerRecord instanceOf \App\Models\Project ? 'primary' : 'gray'),
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
}
