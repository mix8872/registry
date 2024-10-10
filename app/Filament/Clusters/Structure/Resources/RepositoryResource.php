<?php

namespace App\Filament\Clusters\Structure\Resources;

use App\Filament\Clusters\Structure;
use App\Filament\Clusters\Structure\Resources\CommonRelationManagers\ContainersRelationManager;
use App\Filament\Clusters\Structure\Resources\CommonRelationManagers\ServersRelationManager;
use App\Filament\Clusters\Structure\Resources\RepositoryResource\Pages;
use App\Models\Repository;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RepositoryResource extends Resource
{
    protected static ?string $model = Repository::class;
    protected static ?string $modelLabel = 'Репозиторий';
    protected static ?string $pluralModelLabel = 'Репозитории';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationIcon = 'mdi-git';
    protected static ?int $navigationSort = 2;
    protected static ?string $cluster = Structure::class;

    public static function form(Form $form): Form
    {
        return $form->schema(self::getFormFields());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->description(fn(Model $r): string => $r->comment ?? '')
                    ->searchable()->sortable()
                    ->label('Название'),
                Tables\Columns\TextColumn::make('project.name')
                    ->label('Проект')
                    ->url(fn(Model $r): string|null => $r->project ? ProjectResource::getUrl('edit', ['record' => $r->project->id]) : null, true)
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('url')
                    ->url(fn(Model $r): string => $r->url ?? '', true)
                    ->sortable()
                    ->label('Ссылка'),
                Tables\Columns\TextColumn::make('created_at')
                    ->description(fn(Model $r) => $r->createdBy->name)
                    ->sortable()->dateTime()
                    ->label('Создано'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->description(fn(Model $r) => $r->updatedBy->name)
                    ->sortable()->dateTime()
                    ->label('Обновлено'),
            ])
            ->filters([
                SelectFilter::make('project')
                    ->multiple()
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Проект'),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('Создано от'),
                        DatePicker::make('created_until')->label('Создано до'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                Filter::make('updated_at')
                    ->form([
                        DatePicker::make('created_from')->label('Обновлено от'),
                        DatePicker::make('created_until')->label('Обновлено до'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('updated_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('updated_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc')
            ->poll('5s');
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

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getFormFields()
    {
        return [
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
                ->maxLength(255)
                ->suffixAction(
                    Action::make('Перейти')
                        ->icon('heroicon-m-globe-alt')
                        ->iconButton()
                        ->url(fn(Repository $r) => $r->url, true)
                )
                ->label('Ссылка'),
            Forms\Components\Textarea::make('comment')->rows(2)->columnSpanFull()->label('Примечание'),
        ];
    }
}
