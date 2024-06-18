<?php

namespace App\Filament\Resources\CommonRelationManagers;

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
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255)->label('Название'),
                Forms\Components\Select::make('repository_id')
                    ->relationship(name: 'repository', titleAttribute: 'name')
                    ->required()->label('Репозиторий')
                    ->suffixAction(
                        Action::make('Перейти')
                            ->icon('heroicon-m-globe-alt')
                            ->iconButton()
                            ->url(fn(Repository $r) => $r->repository_id ? "/registry/repositories/{$r->repository_id}/edit" : null, true)
                    )->hidden(fn (Livewire $livewire) => $livewire->ownerRecord instanceof \App\Models\Repository),
                Forms\Components\Select::make('server_id')
                    ->relationship(name: 'server', titleAttribute: 'name')
                    ->required()->label('Сервер')
                    ->suffixAction(
                        Action::make('Перейти')
                            ->icon('heroicon-m-globe-alt')
                            ->iconButton()
                            ->url(fn(Server $r) => $r->server_id ? "/registry/servers/{$r->server_id}/edit" : null, true)
                    )->hidden(fn (Livewire $livewire) => $livewire->ownerRecord instanceof \App\Models\Server),
                Forms\Components\TextInput::make('compose_path')->required()->columnSpanFull()->maxLength(255),
                Forms\Components\Textarea::make('comment')->rows(2)->columnSpanFull()->label('Примечание'),
            ]);
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
//                Tables\Actions\AttachAction::make()->multiple()->preloadRecordSelect()->color('primary'),
                Tables\Actions\CreateAction::make()->color('primary'),
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
