<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommonRelationManagers\ContainersRelationManager;
use App\Filament\Resources\CommonRelationManagers\ServersRelationManager;
use App\Filament\Resources\RepositoryResource\Pages;
use App\Models\Repository;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\Actions\Action;

class RepositoryResource extends Resource
{
    protected static ?string $model = Repository::class;
    protected static ?string $modelLabel = 'Репозиторий';
    protected static ?string $pluralModelLabel = 'Репозитории';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationIcon = 'mdi-git';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema(self::getFormFields());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->description(fn(Repository $r): string => $r->comment ?? '')
                    ->searchable()
                    ->sortable()
                    ->label('Название'),
                Tables\Columns\TextColumn::make('project.name')
                    ->label('Проект')
                    ->url(fn(Repository $r): string|null => $r->project ? "/registry/projects/{$r->project->id}/edit" : null, true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('url')
                    ->sortable()
                    ->url(fn(Repository $r): string => $r->url ?? '', true)
                    ->label('Ссылка'),
                Tables\Columns\TextColumn::make('created_at')->sortable()->dateTime()
                    ->description(fn(Repository $r) => $r->createdBy->name)->label('Создано'),
                Tables\Columns\TextColumn::make('updated_at')->sortable()->dateTime()
                    ->description(fn(Repository $r) => $r->updatedBy->name)->label('Обновлено'),
            ])
            ->filters([
                SelectFilter::make('project')
                    ->multiple()
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Проект')
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
            ServersRelationManager::class,
            ContainersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRepositories::route('/'),
            'create' => Pages\CreateRepository::route('/create'),
            'edit' => Pages\EditRepository::route('/{record}/edit'),
        ];
    }

    public static function getFormFields()
    {
        return [
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255)->label('Название'),
            Forms\Components\Select::make('project_id')
                ->relationship(name: 'project', titleAttribute: 'name')
                ->required()->label('Проект')
                ->suffixAction(
                    Action::make('Перейти')
                        ->icon('heroicon-m-globe-alt')
                        ->iconButton()
                        ->url(fn(Repository $r) => $r->project_id ? "/registry/projects/{$r->project_id}/edit" : null, true)
                ),
            Forms\Components\TextInput::make('url')
                ->url()
                ->required()
                ->maxLength(255)->label('Ссылка')
                ->suffixAction(
                    Action::make('Перейти')
                        ->icon('heroicon-m-globe-alt')
                        ->iconButton()
                        ->url(fn(Repository $r) => $r->url, true)
                ),
            Forms\Components\Textarea::make('comment')->rows(2)->columnSpanFull()->label('Примечание'),
        ];
    }
}
