<?php

namespace App\Filament\Clusters\Structure\Resources\ServerResource\RelationManagers;

use App\Filament\Clusters\Structure\Resources\ProjectResource;
use App\Filament\Clusters\Structure\Resources\RepositoryResource;
use App\Models\Repository;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
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
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->label('Название'),
            Forms\Components\Select::make('project_id')
                ->relationship(name: 'project', titleAttribute: 'name')
                ->preload()
                ->required()
                ->searchable()
                ->suffixAction(
                    Action::make('Перейти')
                        ->icon('heroicon-m-globe-alt')
                        ->iconButton()
                        ->url(fn(Repository $r) => $r->project_id ? ProjectResource::getUrl('edit', ['record' => $r->project_id]) : null, true)
                )
                ->label('Проект'),
            Forms\Components\TextInput::make('url')
                ->url()
                ->required()
                ->unique()
                ->maxLength(255)
                ->suffixAction(
                    Action::make('Перейти')
                        ->icon('heroicon-m-globe-alt')
                        ->iconButton()
                        ->url(fn(Repository $r) => $r->url, true)
                )
                ->label('Ссылка на репозиторий'),
            Forms\Components\TextInput::make('resource_url')
                ->url()
                ->maxLength(255)
                ->label('Ссылка на ресурс'),
            Forms\Components\ToggleButtons::make('type')
                ->options([
                    'frontend' => 'Frontend',
                    'backend' => 'Backend',
                    'other' => 'Other'
                ])
                ->inline()
                ->label('Тип'),
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
                    ->label('Ссылка на репозиторий'),
                Tables\Columns\TextColumn::make('resource_url')
                    ->sortable()
                    ->url(fn(Repository $r): string => $r->resource_url ?? '', true)
                    ->label('Ссылка на ресурс'),
                Tables\Columns\TextColumn::make('type')
                    ->sortable()
                    ->url(fn(Repository $r): string => $r->url ?? '', true)
                    ->label('Тип'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->multiple()
                    ->preloadRecordSelect()
                    ->color('primary')
                    ->form(fn(AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\TextInput::make('resource_url')
                            ->url()
                            ->maxLength(255)
                            ->label('Ссылка на ресурс'),
                        Forms\Components\ToggleButtons::make('type')
                            ->options([
                                'frontend' => 'Frontend',
                                'backend' => 'Backend',
                                'other' => 'Other'
                            ])
                            ->inline()
                            ->required()
                            ->label('Тип'),
                    ])
                    ->hidden(fn(Livewire $livewire) => $livewire->ownerRecord instanceof \App\Models\Project),
                Tables\Actions\CreateAction::make()->color(fn(Livewire $livewire
                ) => $livewire->ownerRecord instanceof \App\Models\Project ? 'primary' : 'gray'),
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
