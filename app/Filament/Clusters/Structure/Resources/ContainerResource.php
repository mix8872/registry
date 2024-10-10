<?php

namespace App\Filament\Clusters\Structure\Resources;

use App\Filament\Clusters\Structure;
use App\Filament\Clusters\Structure\Resources\ContainerResource\Pages;
use App\Filament\Clusters\Structure\Resources\ContainerResource\RelationManagers;
use App\Models\Container;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component as Livewire;

class ContainerResource extends Resource
{
    protected static ?string $model = Container::class;
    protected static ?string $navigationIcon = 'mdi-docker';
    protected static ?int $navigationSort = 4;
    protected static ?string $modelLabel = 'Контейнер';
    protected static ?string $pluralModelLabel = 'Контейнеры';
    protected static ?string $recordTitleAttribute = 'name';
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
                    ->description(fn(Model $r) => $r->comment)
                    ->label('Название'),
                Tables\Columns\TextColumn::make('repository.name')
                    ->url(fn(Model $r): string|null => $r->repository ? RepositoryResource::getUrl('edit', ['record' => $r->repository->id]) : null, true)
                    ->searchable()->sortable()
                    ->label('Репозиторий'),
                Tables\Columns\TextColumn::make('server.name')
                    ->url(fn(Model $r): string|null => $r->server ? ServerResource::getUrl('edit', ['record' => $r->server->id]) : null, true)
                    ->searchable()->sortable()
                    ->label('Сервер'),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()->dateTime()
                    ->description(fn(Model $r) => $r->createdBy->name)
                    ->label('Создано'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable()->dateTime()
                    ->description(fn(Model $r) => $r->updatedBy->name)
                    ->label('Обновлено'),
            ])
            ->filters([
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
                SelectFilter::make('repository')
                    ->multiple()
                    ->relationship('repository', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Репозиторий'),
                SelectFilter::make('server')
                    ->multiple()
                    ->relationship('server', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Сервер')
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()->icon('mdi-pencil'),
                Tables\Actions\DeleteAction::make()->icon('mdi-close-thick')->requiresConfirmation(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContainers::route('/'),
//            'create' => Pages\CreateContainer::route('/create'),
            'edit' => Pages\EditContainer::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getFormFields()
    {
        return [
            Forms\Components\TextInput::make('name')->required()->maxLength(255)->label('Название'),
            Forms\Components\Select::make('repository_id')
                ->relationship(name: 'repository', titleAttribute: 'name')
                ->required()->label('Репозиторий')
                ->searchable()
                ->suffixAction(
                    Action::make('Перейти')
                        ->icon('heroicon-m-globe-alt')
                        ->iconButton()
                        ->url(fn(Container $r) => $r->repository_id ? RepositoryResource::getUrl('edit', ['record' => $r->repository_id]) : null, true)
                )->hidden(fn(Livewire $livewire) => isset($livewire->ownerRecord) && $livewire->ownerRecord instanceof \App\Models\Repository),
            Forms\Components\Select::make('server_id')
                ->relationship(name: 'server', titleAttribute: 'name')
                ->required()->label('Сервер')
                ->searchable()
                ->suffixAction(
                    Action::make('Перейти')
                        ->icon('heroicon-m-globe-alt')
                        ->iconButton()
                        ->url(fn(Container $r) => $r->server_id ? ServerResource::getUrl('edit', ['record' => $r->server_id]) : null, true)
                )->hidden(fn(Livewire $livewire) => isset($livewire->ownerRecord) && $livewire->ownerRecord instanceof \App\Models\Server),
            Forms\Components\TextInput::make('compose_path')
                ->required()
                ->columnSpanFull()
                ->maxLength(255),
            Forms\Components\Textarea::make('comment')
                ->rows(2)
                ->columnSpanFull()
                ->label('Примечание'),
        ];
    }
}
